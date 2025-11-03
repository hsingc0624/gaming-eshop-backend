<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);

        $perms = [
            'manage products',
            'manage orders',
            'manage users',
            'manage campaigns',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $admin->givePermissionTo(Permission::all());
        $staff->givePermissionTo(['manage products', 'manage orders', 'manage campaigns']);
    }
}
