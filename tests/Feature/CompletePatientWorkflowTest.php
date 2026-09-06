<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Consultation;
use App\Models\LabTest;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CompletePatientWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_patient_journey_from_registration_to_simulated_payment(): void
    {
        $receptionist = $this->user(User::ROLE_RECEPTIONIST, 'workflow-reception@test.local');
        $doctor = $this->user(User::ROLE_DOCTOR, 'workflow-doctor@test.local');
        $labStaff = $this->user(User::ROLE_LAB_STAFF, 'workflow-lab@test.local');
        $pharmacist = $this->user(User::ROLE_PHARMACIST, 'workflow-pharmacy@test.local');

        $medicine = Medicine::create([
            'name' => 'Paracetamol 500mg',
            'stock_quantity' => 20,
            'unit_price' => 300,
        ]);

        $this->actingAs($receptionist)->post('/patients', [
            'first_name' => 'Ada',
            'last_name' => 'Okafor',
            'gender' => 'female',
            'date_of_birth' => '2001-05-10',
            'phone' => '08000000000',
            'address' => 'Ekpoma, Edo State',
        ])->assertRedirect();

        $patient = Patient::firstOrFail();

        $this->actingAs($receptionist)->post('/appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addHour()->format('Y-m-d H:i:s'),
            'reason' => 'Fever and headache',
        ])->assertRedirect(route('appointments.index'));

        $appointment = Appointment::firstOrFail();

        $this->actingAs($doctor)->post("/appointments/{$appointment->id}/consultation", [
            'complaint' => 'Fever and headache for two days.',
            'diagnosis' => 'Suspected malaria.',
            'treatment' => 'Symptomatic treatment while awaiting lab result.',
            'notes' => 'Patient stable.',
        ])->assertRedirect();

        $consultation = Consultation::firstOrFail();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'completed']);

        $this->actingAs($doctor)->post("/consultations/{$consultation->id}/lab-tests", [
            'test_name' => 'Malaria Parasite Test',
        ])->assertRedirect();

        $this->actingAs($doctor)->post("/consultations/{$consultation->id}/prescriptions", [
            'medicine_id' => $medicine->id,
            'dosage' => '500mg',
            'frequency' => 'Three times daily',
            'duration' => '3 days',
            'quantity' => 2,
        ])->assertRedirect();

        $labTest = LabTest::firstOrFail();
        $prescription = Prescription::firstOrFail();

        $this->actingAs($labStaff)->put("/lab-tests/{$labTest->id}", [
            'result' => 'Malaria parasite detected.',
        ])->assertRedirect(route('lab-tests.index'));

        $this->actingAs($pharmacist)
            ->patch("/prescriptions/{$prescription->id}/dispense")
            ->assertRedirect();

        $this->assertDatabaseHas('prescriptions', ['id' => $prescription->id, 'status' => 'dispensed']);
        $this->assertDatabaseHas('medicines', ['id' => $medicine->id, 'stock_quantity' => 18]);

        $this->actingAs($receptionist)
            ->post("/patients/{$patient->id}/billing")
            ->assertRedirect();

        $bill = Bill::firstOrFail();
        $this->assertSame('7600.00', $bill->amount);
        $this->assertSame('unpaid', $bill->status);

        $this->actingAs($receptionist)
            ->post("/billing/{$bill->id}/pay", ['payment_method' => 'card'])
            ->assertRedirect(route('billing.success', $bill));

        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'paid']);
        $this->assertDatabaseHas('payments', [
            'bill_id' => $bill->id,
            'payment_method' => 'card',
            'reference' => 'HMS-PAY-000001',
        ]);
    }

    private function user(string $role, string $email): User
    {
        return User::create([
            'name' => 'Workflow User',
            'email' => $email,
            'password' => Hash::make('secret123'),
            'role' => $role,
            'status' => 'active',
        ]);
    }
}
