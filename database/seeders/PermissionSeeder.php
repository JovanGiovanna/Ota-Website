<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['key' => 'admins.manage', 'name' => 'Manage Admins', 'description' => 'Create, update and delete admin accounts.'],
            ['key' => 'transactions.manage', 'name' => 'Manage Transactions', 'description' => 'Approve, reject and process refunds.'],
            ['key' => 'transactions.view', 'name' => 'View Transactions', 'description' => 'View booking transactions.'],
            ['key' => 'products.manage', 'name' => 'Manage Products', 'description' => 'Create, update and delete products.'],
            ['key' => 'packages.manage', 'name' => 'Manage Packages', 'description' => 'Create, update and delete packages.'],
            ['key' => 'addons.manage', 'name' => 'Manage Addons', 'description' => 'Create, update and delete addons.'],
            ['key' => 'vendors.manage', 'name' => 'Manage Vendors', 'description' => 'Create, update and delete vendors and vendor details.'],
            ['key' => 'customers.manage', 'name' => 'Manage Customers', 'description' => 'Ban/unban and view customers.'],
            ['key' => 'system.manage', 'name' => 'Manage System', 'description' => 'Access system settings and rekon.'],
            ['key' => 'finance.manage', 'name' => 'Manage Finance', 'description' => 'Edit finance rules, fees and tax settings.'],
            ['key' => 'rekon.manage', 'name' => 'Manage Rekon', 'description' => 'Access and perform reconciliation operations.'],
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['key' => $perm['key']],
                ['name' => $perm['name'], 'description' => $perm['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
