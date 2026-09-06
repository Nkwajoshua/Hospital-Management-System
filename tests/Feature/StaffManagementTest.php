<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_staff_account(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN, 'admin-staff@test.local');

        $this->actingAs($admin)->post('/staff', [
            'name' => 'Dr Test User',
            'email' => 'new-doctor@test.local',
            'password' => 'password123',
            'role' => User::ROLE_DOCTOR,
            'status' => 'active',
        ])->assertRedirect(route('staff.index'));

        $user = User::where('email', 'new-doctor@test.local')->firstOrFail();
        $this->assertSame(User::ROLE_DOCTOR, $user->role);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_non_admin_cannot_manage_staff(): void
    {
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'staff-restricted@test.local');

        $this->actingAs($doctor)->get('/staff')->assertForbidden();
        $this->actingAs($doctor)->get('/staff/create')->assertForbidden();
    }

    public function test_admin_can_change_role_and_status_without_changing_password(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN, 'staff-admin-update@test.local');
        $member = $this->userWithRole(User::ROLE_RECEPTIONIST, 'staff-member@test.local');
        $oldPassword = $member->password;

        $this->actingAs($admin)->put("/staff/{$member->id}", [
            'name' => $member->name,
            'email' => $member->email,
            'password' => '',
            'role' => User::ROLE_LAB_STAFF,
            'status' => 'inactive',
        ])->assertRedirect(route('staff.index'));

        $member->refresh();
        $this->assertSame(User::ROLE_LAB_STAFF, $member->role);
        $this->assertSame('inactive', $member->status);
        $this->assertSame($oldPassword, $member->password);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN, 'self-admin@test.local');

        $this->actingAs($admin)->put("/staff/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'password' => '',
            'role' => User::ROLE_ADMIN,
            'status' => 'inactive',
        ])->assertSessionHas('error');

        $this->assertSame('active', $admin->fresh()->status);
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
