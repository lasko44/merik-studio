<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Main database seeder that orchestrates all seeders.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call([
            RolesAndPermissionsSeeder::class,
            PageComponentsSeeder::class,
            HelpArticlesSeeder::class,
            SampleSiteSeeder::class,
        ]);

        // Create the super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => config('features.super_admin_email', 'matthew.laszkiewicz@gmail.com')],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // Create a test admin user in non-production environments
        if (app()->environment('local', 'testing')) {
            $admin = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
            ]);
            $admin->assignRole('Admin');

            $user = User::factory()->create([
                'name' => 'Regular User',
                'email' => 'user@example.com',
            ]);
            $user->assignRole('User');

            $customer = User::factory()->create([
                'name' => 'Customer',
                'email' => 'customer@example.com',
            ]);
            $customer->assignRole('Customer');
        }
    }
}
