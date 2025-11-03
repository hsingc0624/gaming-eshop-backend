<?php
namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;


class RoleSeeder extends Seeder {
public function run(){
$admin = Role::firstOrCreate(['name'=>'Admin']);
$staff = Role::firstOrCreate(['name'=>'Staff']);
$viewer= Role::firstOrCreate(['name'=>'Viewer']);


Permission::firstOrCreate(['name'=>'manage products']);
Permission::firstOrCreate(['name'=>'manage orders']);
Permission::firstOrCreate(['name'=>'manage users']);
Permission::firstOrCreate(['name'=>'manage campaigns']);


$admin->givePermissionTo(Permission::all());
$staff->givePermissionTo(['manage products','manage orders','manage campaigns']);
}
}