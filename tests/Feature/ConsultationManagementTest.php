<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ConsultationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_doctor_can_create_consultation_and_complete_appointment(): void
    {
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'consult-doctor@test.local');
        $appointment = $this->appointmentFor($doctor);

        $response = $this->actingAs($doctor)->post("/appointments/{$appointment->id}/consultation", [
            'complaint' => 'Fever and headache',
            'diagnosis' => 'Uncomplicated malaria',
            'treatment' => 'Prescribed treatment plan',
            'notes' => 'Review if symptoms persist',
        ]);

        $consultation = Consultation::firstOrFail();

        $response->assertRedirect(route('consultations.show', $consultation));
        $this->assertDatabaseHas('consultations', [
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'diagnosis' => 'Uncomplicated malaria',
        ]);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);
    }

    public function test_doctor_cannot_consult_another_doctors_appointment(): void
    {
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'doctor-a@test.local');
        $otherDoctor = $this->userWithRole(User::ROLE_DOCTOR, 'doctor-b@test.local');
        $appointment = $this->appointmentFor($otherDoctor);

        $this->actingAs($doctor)
            ->get("/appointments/{$appointment->id}/consultation")
            ->assertForbidden();
    }

    public function test_receptionist_cannot_access_medical_records(): void
    {
        $receptionist = $this->userWithRole(User::ROLE_RECEPTIONIST, 'medical-reception@test.local');

        $this->actingAs($receptionist)->get('/consultations')->assertForbidden();
    }

    public function test_doctor_only_sees_their_own_medical_records(): void
    {
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'records-doctor-a@test.local');
        $otherDoctor = $this->userWithRole(User::ROLE_DOCTOR, 'records-doctor-b@test.local');
        $patient = $this->patient();

        Consultation::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'complaint' => 'Complaint A',
            'diagnosis' => 'Diagnosis visible to doctor A',
        ]);

        Consultation::create([
            'patient_id' => $patient->id,
            'doctor_id' => $otherDoctor->id,
            'complaint' => 'Complaint B',
            'diagnosis' => 'Diagnosis belonging to doctor B',
        ]);

        $this->actingAs($doctor)
            ->get('/consultations')
            ->assertOk()
            ->assertSee('Diagnosis visible to doctor A')
            ->assertDontSee('Diagnosis belonging to doctor B');
    }

    public function test_duplicate_consultation_is_not_created_for_same_appointment(): void
    {
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'duplicate-doctor@test.local');
        $appointment = $this->appointmentFor($doctor);

        $consultation = Consultation::create([
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'complaint' => 'Existing complaint',
            'diagnosis' => 'Existing diagnosis',
        ]);

        $this->actingAs($doctor)->post("/appointments/{$appointment->id}/consultation", [
            'complaint' => 'Second complaint',
            'diagnosis' => 'Second diagnosis',
        ])->assertRedirect(route('consultations.show', $consultation));

        $this->assertDatabaseCount('consultations', 1);
    }

    private function appointmentFor(User $doctor): Appointment
    {
        $patient = $this->patient();

        return Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => '2026-09-10 10:00:00',
            'reason' => 'Consultation test',
            'status' => 'scheduled',
        ]);
    }

    private function patient(): Patient
    {
        return Patient::create([
            'patient_number' => 'HMS-'.str_pad((string) (Patient::count() + 1), 5, '0', STR_PAD_LEFT),
            'first_name' => 'Test',
            'last_name' => 'Patient',
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
}
