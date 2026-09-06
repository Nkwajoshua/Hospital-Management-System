<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PatientManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_receptionist_can_register_a_patient(): void
    {
        $receptionist = $this->userWithRole(User::ROLE_RECEPTIONIST, 'reception@test.local');

        $response = $this->actingAs($receptionist)->post('/patients', [
            'first_name' => 'Ada',
            'last_name' => 'Okafor',
            'gender' => 'female',
            'date_of_birth' => '2001-05-10',
            'phone' => '08000000000',
            'address' => 'Ekpoma, Edo State',
        ]);

        $patient = Patient::firstOrFail();

        $this->assertSame('HMS-00001', $patient->patient_number);
        $response->assertRedirect(route('patients.show', $patient));
        $this->assertDatabaseHas('patients', ['first_name' => 'Ada', 'last_name' => 'Okafor']);
    }

    public function test_doctor_can_view_patients_but_cannot_register_one(): void
    {
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'doctor-patient@test.local');

        $this->actingAs($doctor)->get('/patients')->assertOk();
        $this->actingAs($doctor)->get('/patients/create')->assertForbidden();
    }

    public function test_patient_search_matches_number_name_or_phone(): void
    {
        $user = $this->userWithRole(User::ROLE_RECEPTIONIST, 'search@test.local');

        Patient::create([
            'patient_number' => 'HMS-00001',
            'first_name' => 'Chidi',
            'last_name' => 'Nwosu',
            'phone' => '08111111111',
        ]);

        Patient::create([
            'patient_number' => 'HMS-00002',
            'first_name' => 'Bola',
            'last_name' => 'Adeyemi',
            'phone' => '08222222222',
        ]);

        $this->actingAs($user)
            ->get('/patients?search=Chidi')
            ->assertOk()
            ->assertSee('Chidi')
            ->assertDontSee('Bola');
    }

    public function test_admin_can_update_and_delete_unused_patient_record(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN, 'patient-admin@test.local');
        $patient = Patient::create([
            'patient_number' => 'HMS-00001',
            'first_name' => 'Old',
            'last_name' => 'Name',
        ]);

        $this->actingAs($admin)->put("/patients/{$patient->id}", [
            'first_name' => 'New',
            'last_name' => 'Name',
        ])->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patients', ['id' => $patient->id, 'first_name' => 'New']);

        $this->actingAs($admin)
            ->delete("/patients/{$patient->id}")
            ->assertRedirect(route('patients.index'));

        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
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
