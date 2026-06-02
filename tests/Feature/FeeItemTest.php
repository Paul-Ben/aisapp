<?php

use App\Http\Controllers\Finance\FeeManagementController;
use App\Models\ClassCategory;
use App\Models\FeeItem;
use App\Models\SchoolClass;

it('creates a fee item with assignments', function () {
    $category = ClassCategory::firstOrCreate(['name' => 'Test Category']);
    $class = SchoolClass::firstOrCreate(
        ['name' => 'Test Class'],
        ['class_category_id' => $category->id, 'is_active' => true]
    );

    $fee = FeeItem::create([
        'name' => 'Tuition',
        'description' => 'Test tuition fee',
        'amount' => 50000.00,
        'is_active' => true,
    ]);

    $fee->classes()->sync([$class->id]);
    $fee->classCategories()->sync([$category->id]);

    $loaded = FeeItem::withCount(['classes', 'classCategories'])->find($fee->id);

    expect($loaded->name)->toBe('Tuition');
    expect((float) $loaded->amount)->toBe(50000.00);
    expect($loaded->is_active)->toBeTrue();
    expect($loaded->classes_count)->toBe(1);
    expect($loaded->class_categories_count)->toBe(1);

    $fee->delete();
    $class->delete();
    $category->delete();
});

it('blocks deleting a fee item that is assigned', function () {
    $category = ClassCategory::firstOrCreate(['name' => 'Delete Test Category']);

    $fee = FeeItem::create([
        'name' => 'To Delete',
        'amount' => 1000,
        'is_active' => true,
    ]);

    $fee->classCategories()->sync([$category->id]);

    $controller = app(FeeManagementController::class);
    $response = $controller->destroy($fee);

    expect(FeeItem::find($fee->id))->not->toBeNull();
    expect(session('error'))->toContain('Cannot delete');

    $fee->classCategories()->detach();
    $fee->delete();
    $category->delete();
});
