<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('HMS_ADMIN_PASSWORD');

        if (! $password) {
            $this->command?->warn('HMS_ADMIN_PASSWORD is empty; administrator seed skipped.');

            return;
        }

        User::updateOrCreate(
            ['email' => env('HMS_ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('HMS_ADMIN_NAME', 'System Administrator'),
                'password' => $password,
                'role' => User::ROLE_ADMIN,
                'status' => 'active',
            ],
        );
    }
}
