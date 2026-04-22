<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions (optional - can be expanded based on needs)
        $permissions = [
            'view dashboard',
            'manage users',
            'manage finances',
            'manage exams',
            'manage staff',
            'view reports',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $roles = [
            'superadmin',
            'finance_officer',
            'admin',
            'exam_officer',
            'staff',
            'proprietor',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Assign permissions to roles
        $superadmin = Role::where('name', 'superadmin')->first();
        $superadmin->syncPermissions(Permission::all());

        $financeOfficer = Role::where('name', 'finance_officer')->first();
        $financeOfficer->syncPermissions(['view dashboard', 'manage finances', 'view reports']);

        $admin = Role::where('name', 'admin')->first();
        $admin->syncPermissions(['view dashboard', 'manage users', 'manage staff', 'view reports']);

        $examOfficer = Role::where('name', 'exam_officer')->first();
        $examOfficer->syncPermissions(['view dashboard', 'manage exams', 'view reports']);

        $staff = Role::where('name', 'staff')->first();
        $staff->syncPermissions(['view dashboard']);

        $proprietor = Role::where('name', 'proprietor')->first();
        $proprietor->syncPermissions(['view dashboard', 'view reports', 'manage settings']);

        // Create users for each role for testing
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
            ],
            [
                'name' => 'Finance Officer',
                'email' => 'finance@example.com',
                'password' => Hash::make('password'),
                'role' => 'finance_officer',
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Exam Officer',
                'email' => 'exam@example.com',
                'password' => Hash::make('password'),
                'role' => 'exam_officer',
            ],
            [
                'name' => 'Staff Member',
                'email' => 'staff@example.com',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ],
            [
                'name' => 'Proprietor',
                'email' => 'proprietor@example.com',
                'password' => Hash::make('password'),
                'role' => 'proprietor',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Assign role using Spatie Permission
            $user->syncRoles([$userData['role']]);
        }

        $this->command->info('Roles and users created successfully!');
        $this->command->info('Test users:');
        $this->command->info('  superadmin@example.com / password');
        $this->command->info('  finance@example.com / password');
        $this->command->info('  admin@example.com / password');
        $this->command->info('  exam@example.com / password');
        $this->command->info('  staff@example.com / password');
        $this->command->info('  proprietor@example.com / password');
    }
}
