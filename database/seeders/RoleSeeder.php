<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles
        $roles = [
            ['key' => 'admin', 'name' => 'Admin', 'description' => 'Operational administrator with limited finance permissions.'],
            ['key' => 'manager', 'name' => 'Manager', 'description' => 'Operational manager with scoped finance view.'],
            ['key' => 'staff', 'name' => 'Staff', 'description' => 'Operational staff with no finance permissions.'],
        ];

        foreach ($roles as $r) {
            DB::table('roles')->updateOrInsert(
                ['key' => $r['key']],
                ['name' => $r['name'], 'description' => $r['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // Map permissions to roles
        $permissionMap = [
            'super_admin' => [
                'admins.manage','transactions.manage','transactions.view','products.manage','packages.manage','addons.manage','vendors.manage','customers.manage','system.manage','finance.manage','rekon.manage'
            ],
            'admin' => [
                'transactions.view','transactions.manage','products.manage','packages.manage','addons.manage','vendors.manage','customers.manage'
            ],
            'manager' => [
                'transactions.view'
            ],
            'staff' => [
                // minimal; staff will not have sensitive permissions
            ],
        ];

        foreach ($permissionMap as $roleKey => $permKeys) {
            $role = DB::table('roles')->where('key', $roleKey)->first();
            if (!$role) continue;

            foreach ($permKeys as $pkey) {
                $perm = DB::table('permissions')->where('key', $pkey)->first();
                if (!$perm) continue;

                DB::table('role_permission')->updateOrInsert([
                    'role_id' => $role->id,
                    'permission_id' => $perm->id,
                ], ['created_at' => now(), 'updated_at' => now()]);
            }
        }
    }
}
