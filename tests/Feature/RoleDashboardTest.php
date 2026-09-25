<?php

use App\Models\User;

test('every role logs in and reaches its own dashboard', function (string $role, string $path) {
    $user = User::factory()->create(['role' => $role, 'is_active' => true]);

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect($path);

    $this->actingAs($user)->get($path)->assertOk();

    $this->actingAs($user)->get('/dashboard')->assertRedirect($path);
})->with([
    'superadmin' => ['superadmin', '/superadmin/dashboard'],
    'admin' => ['admin', '/admin/dashboard'],
    'finance_officer' => ['finance_officer', '/finance/dashboard'],
    'exam_officer' => ['exam_officer', '/exam/dashboard'],
    'proprietor' => ['proprietor', '/proprietor/dashboard'],
    'staff' => ['staff', '/staff/dashboard'],
]);
