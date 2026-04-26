<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffManagementController extends Controller
{
    public function index()
    {
        $staff = Staff::with('user', 'classes')->latest()->get();
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        $classes = SchoolClass::where('is_active', true)->get();
        return view('admin.staff.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|unique:staff,staff_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:staff,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'employment_date' => 'nullable|date',
            'address' => 'nullable|string',
            'password' => 'required|min:8',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:classes,id',
        ]);

        DB::beginTransaction();
        try {
            // Create user account
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            
            // Assign teacher role
            $role = Role::firstOrCreate(['name' => 'teacher']);
            $user->assignRole($role);

            // Create staff record
            $staff = Staff::create([
                'user_id' => $user->id,
                'staff_id' => $validated['staff_id'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'position' => $validated['position'] ?? null,
                'department' => $validated['department'] ?? null,
                'employment_date' => $validated['employment_date'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_active' => true,
            ]);

            // Assign classes if provided
            if (!empty($validated['classes'])) {
                foreach ($validated['classes'] as $classId) {
                    $staff->classes()->attach($classId, ['role' => 'teacher']);
                }
            }

            DB::commit();
            return redirect()->route('admin.staff.index')->with('success', 'Staff member created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create staff: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $staff = Staff::with('user', 'classes')->findOrFail($id);
        $classes = SchoolClass::where('is_active', true)->get();
        $assignedClasses = $staff->classes->pluck('id')->toArray();
        return view('admin.staff.edit', compact('staff', 'classes', 'assignedClasses'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        
        $validated = $request->validate([
            'staff_id' => 'required|unique:staff,staff_id,' . $staff->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->user_id . '|unique:staff,email,' . $staff->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'employment_date' => 'nullable|date',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
            'password' => 'nullable|min:8',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:classes,id',
        ]);

        DB::beginTransaction();
        try {
            // Update user account
            $staff->user->update([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
            ]);
            
            if (!empty($validated['password'])) {
                $staff->user->password = Hash::make($validated['password']);
                $staff->user->save();
            }

            // Update staff record
            $staff->update([
                'staff_id' => $validated['staff_id'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'position' => $validated['position'] ?? null,
                'department' => $validated['department'] ?? null,
                'employment_date' => $validated['employment_date'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_active' => $validated['is_active'] ?? $staff->is_active,
            ]);

            // Sync classes
            $newClasses = $validated['classes'] ?? [];
            $staff->classes()->sync($newClasses);

            DB::commit();
            return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update staff: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        
        try {
            $staff->user->delete(); // This will cascade delete the staff record
            return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete staff: ' . $e->getMessage()]);
        }
    }

    public function toggleStatus($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->is_active = !$staff->is_active;
        $staff->save();
        
        $status = $staff->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.staff.index')->with('success', 'Staff member ' . $status . ' successfully.');
    }
}
