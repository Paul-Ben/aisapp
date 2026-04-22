<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the role seeder to create roles, permissions and test users
        $this->call([
            RoleSeeder::class,
        ]);
    }
}
