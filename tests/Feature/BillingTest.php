<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_receptionist_can_generate_bill_for_latest_consultation(): void
    {
        $receptionist = $this->userWithRole(User::ROLE_RECEPTIONIST, 'billing-reception@test.local');
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'billing-doctor@test.local');
        $patient = $this->patient();
        $consultation = Consultation::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'complaint' => 'Fever',
            'diagnosis' => 'Malaria',
        ]);

        $response = $this->actingAs($receptionist)->post("/patients/{$patient->id}/billing");

        $bill = Bill::firstOrFail();
        $response->assertRedirect(route('billing.show', $bill));
        $this->assertSame($consultation->id, $bill->consultation_id);
        $this->assertSame('HMS-BILL-000001', $bill->reference);
        $this->assertSame('5000.00', $bill->amount);
        $this->assertSame('unpaid', $bill->status);
    }

    public function test_simulated_payment_marks_bill_paid_and_creates_reference(): void
    {
        $receptionist = $this->userWithRole(User::ROLE_RECEPTIONIST, 'pay-reception@test.local');
        $patient = $this->patient();
        $bill = Bill::create([
            'patient_id' => $patient->id,
            'reference' => 'HMS-BILL-000001',
            'amount' => 7500,
            'details' => [['label' => 'Consultation', 'quantity' => 1, 'unit_price' => 7500, 'amount' => 7500]],
            'status' => 'unpaid',
        ]);

        $this->actingAs($receptionist)
            ->post("/billing/{$bill->id}/pay", ['payment_method' => 'card'])
            ->assertRedirect(route('billing.success', $bill));

        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'paid']);
        $this->assertDatabaseHas('payments', [
            'bill_id' => $bill->id,
            'reference' => 'HMS-PAY-000001',
            'payment_method' => 'card',
        ]);
    }

    public function test_doctor_cannot_access_billing_module(): void
    {
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'billing-blocked@test.local');

        $this->actingAs($doctor)->get('/billing')->assertForbidden();
    }

    public function test_bill_is_not_duplicated_for_same_consultation(): void
    {
        $receptionist = $this->userWithRole(User::ROLE_RECEPTIONIST, 'duplicate-bill@test.local');
        $doctor = $this->userWithRole(User::ROLE_DOCTOR, 'duplicate-doctor@test.local');
        $patient = $this->patient();
        Consultation::create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

        $this->actingAs($receptionist)->post("/patients/{$patient->id}/billing");
        $this->actingAs($receptionist)->post("/patients/{$patient->id}/billing");

        $this->assertSame(1, Bill::count());
    }

    private function patient(): Patient
    {
        return Patient::create([
            'patient_number' => 'HMS-00001',
            'first_name' => 'Test',
            'last_name' => 'Patient',
        ]);
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
