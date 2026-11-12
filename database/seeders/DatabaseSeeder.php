<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        $user = User::query()->firstOrCreate(
            ['email' => 'owner@petora.test'],
            [
                'name' => 'Owner',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $user->syncRoles(['Owner']);
    }
}
