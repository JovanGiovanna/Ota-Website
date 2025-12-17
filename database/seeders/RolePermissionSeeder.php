<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['key' => 'transactions.view',   'name' => 'View Transactions',   'description' => 'View bookings, transactions, and rekon'],
            ['key' => 'transactions.manage', 'name' => 'Manage Transactions', 'description' => 'Approve, reject, verify payments, refunds'],
            ['key' => 'packages.manage',     'name' => 'Manage Packages',     'description' => 'Create, update, delete packages'],
            ['key' => 'products.manage',     'name' => 'Manage Products',     'description' => 'Create, update, delete products'],
            ['key' => 'addons.manage',       'name' => 'Manage Addons',       'description' => 'Create, update, delete addons'],
            ['key' => 'vendors.manage',      'name' => 'Manage Vendors',      'description' => 'Manage vendor users and info'],
            ['key' => 'customers.manage',    'name' => 'Manage Customers',    'description' => 'Ban/unban, manage customers'],
            ['key' => 'system.manage',       'name' => 'Manage System',       'description' => 'Locations, types, categories, email settings'],
            ['key' => 'admins.manage',       'name' => 'Manage Admins',       'description' => 'Manage admin users and roles'],
        ];

        // Ensure permissions exist
        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['key' => $perm['key']], [
                'name' => $perm['name'],
                'description' => $perm['description'] ?? null,
            ]);
        }

        // Ensure roles exist
        $roles = [
            'super_admin' => [
                'name' => 'Super Admin',
                'description' => 'Full access to all features',
                'permissions' => collect($permissions)->pluck('key')->all(),
            ],
            'finance' => [
                'name' => 'Finance',
                'description' => 'Finance role with transaction visibility and actions',
                // Grant view by default; add manage if they should verify/refund
                'permissions' => [
                    'transactions.view',
                    // 'transactions.manage', // Uncomment to allow approvals/refunds
                ],
            ],
            'ops_manager' => [
                'name' => 'Operations Manager',
                'description' => 'Manages vendors, products, addons, packages, customers',
                'permissions' => [
                    'vendors.manage',
                    'products.manage',
                    'addons.manage',
                    'packages.manage',
                    'customers.manage',
                ],
            ],
            'system_manager' => [
                'name' => 'System Manager',
                'description' => 'Manages system settings and taxonomy',
                'permissions' => [
                    'system.manage',
                ],
            ],
            'admin_manager' => [
                'name' => 'Admin Manager',
                'description' => 'Manages admin users and roles',
                'permissions' => [
                    'admins.manage',
                ],
            ],
        ];

        foreach ($roles as $key => $meta) {
            $role = Role::updateOrCreate(['key' => $key], [
                'name' => $meta['name'],
                'description' => $meta['description'] ?? null,
            ]);

            $permIds = Permission::whereIn('key', $meta['permissions'])->pluck('id')->all();
            // Sync exact mapping for clarity; adjust to syncWithoutDetaching if you prefer additive behavior
            $role->permissions()->sync($permIds);
        }
    }
}
