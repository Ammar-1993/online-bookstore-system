<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $perms = [
            'manage users','manage roles','manage all books','manage all orders',
            'create book','edit own book','delete own book','view own orders',
            'place order','review book',
        ];
        foreach ($perms as $p) Permission::firstOrCreate(['name'=>$p]);

        $admin  = Role::firstOrCreate(['name'=>'Admin']);
        $seller = Role::firstOrCreate(['name'=>'Seller']);
        $user   = Role::firstOrCreate(['name'=>'User']);

        $admin->givePermissionTo(['manage users','manage roles','manage all books','manage all orders']);
        $seller->givePermissionTo(['create book','edit own book','delete own book','view own orders']);
        $user->givePermissionTo(['place order','review book']);
    }
}
