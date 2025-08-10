<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@bookstore.test'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );
        $this->ensurePersonalTeam($admin, 'Admin Team');
        $admin->assignRole('Admin');

        // Seller
        $seller = User::firstOrCreate(
            ['email' => 'seller@bookstore.test'],
            ['name' => 'Seller', 'password' => Hash::make('password')]
        );
        $this->ensurePersonalTeam($seller, 'Seller Team');
        $seller->assignRole('Seller');

        // User
        $user = User::firstOrCreate(
            ['email' => 'user@bookstore.test'],
            ['name' => 'User', 'password' => Hash::make('password')]
        );
        $this->ensurePersonalTeam($user, 'User Team');
        $user->assignRole('User');
    }

    /**
     * إنشاء فريق شخصي إن لم يوجد، وتعيينه كفريق حالي.
     */
    protected function ensurePersonalTeam(User $user, string $teamName): void
    {
        if (! $user->currentTeam) {
            $team = Team::firstOrCreate(
                ['user_id' => $user->id, 'personal_team' => true],
                ['name' => $teamName]
            );
            // اربط المستخدم بالفريق واجعله الحالي
            $user->switchTeam($team);
        }
    }
}
