<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_allowed_role_can_access_role_protected_route(): void
    {
        Route::middleware(['web', 'auth', 'role:admin'])->get('/_test/admin-only', fn () => 'ok');

        $admin = $this->userWithRole(User::ROLE_ADMIN, 'admin@test.local');

        $this->actingAs($admin)->get('/_test/admin-only')->assertOk();
    }

    public function test_other_roles_receive_forbidden_response(): void
    {
        Route::middleware(['web', 'auth', 'role:admin'])->get('/_test/admin-only', fn () => 'ok');

        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'doctor@test.local');

        $this->actingAs($doctor)->get('/_test/admin-only')->assertForbidden();
    }

    private function userWithRole(string $role, string $email): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => $email,
            'password' => Hash::make('secret123'),
            'role' => $role,
            'status' => 'active',
        ]);
    }
}
