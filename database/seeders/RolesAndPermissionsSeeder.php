<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds the default roles and permissions for the CMS.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Page permissions
            'pages.view',
            'pages.create',
            'pages.update',
            'pages.delete',
            'pages.force-delete',
            'pages.restore',

            // User permissions
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.force-delete',

            // Theme permissions
            'themes.view',
            'themes.update',

            // Calendar permissions
            'calendar.view',
            'calendar.manage',

            // Email campaign permissions
            'campaigns.view',
            'campaigns.create',
            'campaigns.update',
            'campaigns.delete',

            // Product/Stripe permissions
            'products.view',
            'products.create',
            'products.update',
            'products.delete',

            // Component library permissions
            'components.view',
            'components.create',
            'components.update',
            'components.delete',

            // Site settings permissions
            'settings.view',
            'settings.update',

            // Blog permissions
            'blog.view',
            'blog.create',
            'blog.update',
            'blog.delete',
            'blog.force-delete',
            'blog.restore',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Customer role (lowest level)
        $customerRole = Role::create(['name' => 'Customer']);
        $customerRole->givePermissionTo([
            'pages.view',
            'calendar.view',
            'products.view',
        ]);

        // Create User role
        $userRole = Role::create(['name' => 'User']);
        $userRole->givePermissionTo([
            'pages.view',
            'pages.create',
            'pages.update',
            'pages.delete',
            'themes.view',
            'themes.update',
            'calendar.view',
            'calendar.manage',
            'campaigns.view',
            'campaigns.create',
            'campaigns.update',
            'campaigns.delete',
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'components.view',
            'components.create',
            'components.update',
            'components.delete',
            'settings.view',
            'settings.update',
            'blog.view',
            'blog.create',
            'blog.update',
            'blog.delete',
        ]);

        // Create Admin role
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo([
            'pages.view',
            'pages.create',
            'pages.update',
            'pages.delete',
            'pages.restore',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'themes.view',
            'themes.update',
            'calendar.view',
            'calendar.manage',
            'campaigns.view',
            'campaigns.create',
            'campaigns.update',
            'campaigns.delete',
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'components.view',
            'components.create',
            'components.update',
            'components.delete',
            'settings.view',
            'settings.update',
            'blog.view',
            'blog.create',
            'blog.update',
            'blog.delete',
            'blog.restore',
        ]);

        // Create Super Admin role with all permissions
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());
    }
}
