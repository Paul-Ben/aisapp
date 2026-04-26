<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassCategory;
use App\Models\SchoolClass;
use App\Models\Staff;
use Illuminate\Http\Request;

class ClassManagementController extends Controller
{
    public function index()
    {
        $categories = ClassCategory::with('classes')->get();
        $classes = SchoolClass::with('category', 'staff')->latest()->get();
        return view('admin.classes.index', compact('categories', 'classes'));
    }

    public function create()
    {
        $categories = ClassCategory::all();
        return view('admin.classes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_category_id' => 'required|exists:class_categories,id',
            'name' => 'required|string|max:255',
            'arm' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        SchoolClass::create($validated);
        return redirect()->route('admin.classes.index')->with('success', 'Class created successfully.');
    }

    public function edit($id)
    {
        $class = SchoolClass::findOrFail($id);
        $categories = ClassCategory::all();
        return view('admin.classes.edit', compact('class', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $class = SchoolClass::findOrFail($id);
        
        $validated = $request->validate([
            'class_category_id' => 'required|exists:class_categories,id',
            'name' => 'required|string|max:255',
            'arm' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $class->update($validated);
        return redirect()->route('admin.classes.index')->with('success', 'Class updated successfully.');
    }

    public function destroy($id)
    {
        $class = SchoolClass::findOrFail($id);
        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Class deleted successfully.');
    }

    // Category management
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:class_categories,name',
            'description' => 'nullable|string',
        ]);

        ClassCategory::create($validated);
        return redirect()->route('admin.classes.index')->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = ClassCategory::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:class_categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);
        return redirect()->route('admin.classes.index')->with('success', 'Category updated successfully.');
    }

    public function destroyCategory($id)
    {
        $category = ClassCategory::findOrFail($id);
        
        if ($category->classes()->count() > 0) {
            return redirect()->route('admin.classes.index')->with('error', 'Cannot delete category with existing classes.');
        }
        
        $category->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Category deleted successfully.');
    }

    // Assign staff to class
    public function assignStaff(Request $request, $classId)
    {
        $class = SchoolClass::findOrFail($classId);
        
        $validated = $request->validate([
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:staff,id',
            'role' => 'nullable|string|max:50',
        ]);

        $role = $validated['role'] ?? 'teacher';
        
        foreach ($validated['staff_ids'] as $staffId) {
            $class->staff()->syncWithoutDetaching([$staffId => ['role' => $role]]);
        }

        return redirect()->route('admin.classes.index')->with('success', 'Staff assigned successfully.');
    }

    public function removeStaff($classId, $staffId)
    {
        $class = SchoolClass::findOrFail($classId);
        $class->staff()->detach($staffId);
        return redirect()->route('admin.classes.index')->with('success', 'Staff removed from class successfully.');
    }
}
