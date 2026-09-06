<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AppointmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_receptionist_can_book_an_appointment_with_an_active_doctor(): void
    {
        $receptionist = $this->userWithRole(User::ROLE_RECEPTIONIST, 'appointment-reception@test.local');
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'appointment-doctor@test.local');
        $patient = $this->patient();

        $response = $this->actingAs($receptionist)->post('/appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => '2026-09-10 10:30:00',
            'reason' => 'General consultation',
        ]);

        $response->assertRedirect(route('appointments.index'));
        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'status' => 'scheduled',
        ]);
    }

    public function test_non_doctor_cannot_be_assigned_to_an_appointment(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN, 'appointment-admin@test.local');
        $receptionist = $this->userWithRole(User::ROLE_RECEPTIONIST, 'not-doctor@test.local');
        $patient = $this->patient();

        $this->actingAs($admin)->post('/appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $receptionist->id,
            'appointment_date' => '2026-09-10 10:30:00',
        ])->assertSessionHasErrors('doctor_id');

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_doctor_only_sees_appointments_assigned_to_them(): void
    {
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'doctor-one@test.local');
        $otherDoctor = $this->userWithRole(User::ROLE_DOCTOR, 'doctor-two@test.local');
        $patient = $this->patient();

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => '2026-09-10 09:00:00',
            'reason' => 'Assigned to first doctor',
            'status' => 'scheduled',
        ]);

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $otherDoctor->id,
            'appointment_date' => '2026-09-10 11:00:00',
            'reason' => 'Assigned to second doctor',
            'status' => 'scheduled',
        ]);

        $this->actingAs($doctor)
            ->get('/appointments')
            ->assertOk()
            ->assertSee('Assigned to first doctor')
            ->assertDontSee('Assigned to second doctor');
    }

    public function test_doctor_cannot_create_or_edit_appointments(): void
    {
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'doctor-restricted@test.local');
        $patient = $this->patient();
        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => '2026-09-10 09:00:00',
            'status' => 'scheduled',
        ]);

        $this->actingAs($doctor)->get('/appointments')->assertOk();
        $this->actingAs($doctor)->get('/appointments/create')->assertForbidden();
        $this->actingAs($doctor)->get("/appointments/{$appointment->id}/edit")->assertForbidden();
    }

    public function test_receptionist_can_cancel_a_scheduled_appointment(): void
    {
        $receptionist = $this->userWithRole(User::ROLE_RECEPTIONIST, 'cancel-reception@test.local');
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'cancel-doctor@test.local');
        $patient = $this->patient();
        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => '2026-09-10 09:00:00',
            'status' => 'scheduled',
        ]);

        $this->actingAs($receptionist)
            ->patch("/appointments/{$appointment->id}/cancel")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
        ]);
    }

    private function userWithRole(string $role, string $email): User
    {
        return User::create([
            'name' => ucwords(str_replace('_', ' ', $role)),
            'email' => $email,
            'password' => Hash::make('secret123'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function patient(): Patient
    {
        return Patient::create([
            'patient_number' => 'HMS-00001',
            'first_name' => 'Test',
            'last_name' => 'Patient',
        ]);
    }
}
