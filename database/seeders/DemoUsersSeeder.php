<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email'=>'admin@bookstore.test'],
            ['name'=>'Admin', 'password'=>Hash::make('password')]
        ); $admin->assignRole('Admin');

        $seller = User::firstOrCreate(
            ['email'=>'seller@bookstore.test'],
            ['name'=>'Seller', 'password'=>Hash::make('password')]
        ); $seller->assignRole('Seller');

        $user = User::firstOrCreate(
            ['email'=>'user@bookstore.test'],
            ['name'=>'User', 'password'=>Hash::make('password')]
        ); $user->assignRole('User');
    }
}
