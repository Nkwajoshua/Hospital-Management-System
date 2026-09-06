<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DefenceReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_primary_pages_render_for_each_staff_role(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN, 'ui-admin@test.local');
        $receptionist = $this->userWithRole(User::ROLE_RECEPTIONIST, 'ui-reception@test.local');
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'ui-doctor@test.local');
        $lab = $this->userWithRole(User::ROLE_LAB_STAFF, 'ui-lab@test.local');
        $pharmacist = $this->userWithRole(User::ROLE_PHARMACIST, 'ui-pharmacy@test.local');

        $this->assertPagesRender($admin, ['/dashboard', '/patients', '/appointments', '/consultations', '/lab-tests', '/prescriptions', '/medicines', '/billing', '/staff']);
        $this->assertPagesRender($receptionist, ['/dashboard', '/patients', '/appointments', '/billing']);
        $this->assertPagesRender($doctor, ['/dashboard', '/patients', '/appointments', '/consultations', '/lab-tests', '/prescriptions']);
        $this->assertPagesRender($lab, ['/dashboard', '/patients', '/lab-tests']);
        $this->assertPagesRender($pharmacist, ['/dashboard', '/patients', '/prescriptions', '/medicines']);
    }

    public function test_dashboard_shows_completed_prototype_status(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN, 'ready-admin@test.local');

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('MVP Ready')
            ->assertSee('Simulated payment and receipt');
    }

    public function test_role_boundaries_remain_intact_after_ui_polish(): void
    {
        $receptionist = $this->userWithRole(User::ROLE_RECEPTIONIST, 'boundary-reception@test.local');
        $lab = $this->userWithRole(User::ROLE_LAB_STAFF, 'boundary-lab@test.local');
        $pharmacist = $this->userWithRole(User::ROLE_PHARMACIST, 'boundary-pharmacy@test.local');

        $this->actingAs($receptionist)->get('/consultations')->assertForbidden();
        $this->actingAs($lab)->get('/billing')->assertForbidden();
        $this->actingAs($pharmacist)->get('/staff')->assertForbidden();
    }

    private function assertPagesRender(User $user, array $paths): void
    {
        foreach ($paths as $path) {
            $this->actingAs($user)
                ->get($path)
                ->assertOk()
                ->assertSee('HMS');
        }
    }

    private function userWithRole(string $role, string $email): User
    {
        return User::create([
            'name' => 'Defence Test User',
            'email' => $email,
            'password' => Hash::make('secret123'),
            'role' => $role,
            'status' => 'active',
        ]);
    }
}
