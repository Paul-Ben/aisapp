<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeeItemRequest;
use App\Http\Requests\UpdateFeeItemRequest;
use App\Models\ClassCategory;
use App\Models\FeeItem;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FeeManagementController extends Controller
{
    public function index(): View
    {
        $feeItems = FeeItem::withCount(['classes', 'classCategories'])
            ->orderBy('name')
            ->get();

        return view('finance.fees.index', compact('feeItems'));
    }

    public function create(): View
    {
        $classes = SchoolClass::with('category')->orderBy('name')->orderBy('arm')->get();
        $classCategories = ClassCategory::orderBy('name')->get();

        return view('finance.fees.create', compact('classes', 'classCategories'));
    }

    public function store(StoreFeeItemRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $feeItem = FeeItem::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'amount' => $data['amount'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            $feeItem->classes()->sync($data['classes'] ?? []);
            $feeItem->classCategories()->sync($data['class_categories'] ?? []);

            DB::commit();

            return redirect()->route('finance.fees.index')
                ->with('success', 'Fee item created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed to create fee item: '.$e->getMessage()])
                ->withInput();
        }
    }

    public function edit(FeeItem $fee): View
    {
        $classes = SchoolClass::with('category')->orderBy('name')->orderBy('arm')->get();
        $classCategories = ClassCategory::orderBy('name')->get();
        $fee->load(['classes', 'classCategories']);

        return view('finance.fees.edit', compact('fee', 'classes', 'classCategories'));
    }

    public function update(UpdateFeeItemRequest $request, FeeItem $fee): RedirectResponse
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $fee->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'amount' => $data['amount'],
                'is_active' => $request->boolean('is_active'),
            ]);

            $fee->classes()->sync($data['classes'] ?? []);
            $fee->classCategories()->sync($data['class_categories'] ?? []);

            DB::commit();

            return redirect()->route('finance.fees.index')
                ->with('success', 'Fee item updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed to update fee item: '.$e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(FeeItem $fee): RedirectResponse
    {
        if ($fee->classes()->count() > 0 || $fee->classCategories()->count() > 0) {
            return back()->with('error', 'Cannot delete fee item that is assigned to classes or class categories.');
        }

        $fee->delete();

        return redirect()->route('finance.fees.index')
            ->with('success', 'Fee item deleted successfully.');
    }
}
