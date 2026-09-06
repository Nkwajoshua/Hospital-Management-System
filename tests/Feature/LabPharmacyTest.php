<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\LabTest;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LabPharmacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_request_lab_test_for_own_consultation(): void
    {
        [$doctor, $consultation] = $this->consultation();
        $this->actingAs($doctor)->post("/consultations/{$consultation->id}/lab-tests", ['test_name' => 'Full Blood Count'])->assertSessionHas('success');
        $this->assertDatabaseHas('lab_tests', ['consultation_id' => $consultation->id, 'test_name' => 'Full Blood Count', 'status' => 'pending']);
    }

    public function test_lab_staff_can_complete_lab_result(): void
    {
        [$doctor, $consultation] = $this->consultation();
        $labStaff = $this->user(User::ROLE_LAB_STAFF, 'lab@test.local');
        $test = LabTest::create(['patient_id'=>$consultation->patient_id,'consultation_id'=>$consultation->id,'requested_by'=>$doctor->id,'test_name'=>'PCV','status'=>'pending']);
        $this->actingAs($labStaff)->put("/lab-tests/{$test->id}", ['result'=>'PCV within reference range'])->assertRedirect(route('lab-tests.index'));
        $this->assertDatabaseHas('lab_tests', ['id'=>$test->id,'status'=>'completed','result'=>'PCV within reference range']);
    }

    public function test_doctor_can_create_prescription_for_own_consultation(): void
    {
        [$doctor, $consultation] = $this->consultation();
        $medicine = Medicine::create(['name'=>'Paracetamol','stock_quantity'=>100,'unit_price'=>100]);
        $response = $this->actingAs($doctor)->post("/consultations/{$consultation->id}/prescriptions", ['medicine_id'=>$medicine->id,'dosage'=>'500 mg','frequency'=>'Twice daily','duration'=>'3 days','quantity'=>6]);
        $prescription = Prescription::firstOrFail();
        $response->assertRedirect(route('prescriptions.show',$prescription));
        $this->assertDatabaseHas('prescription_items', ['prescription_id'=>$prescription->id,'medicine_id'=>$medicine->id,'quantity'=>6]);
    }

    public function test_pharmacist_dispensing_reduces_stock(): void
    {
        [$doctor, $consultation] = $this->consultation();
        $pharmacist = $this->user(User::ROLE_PHARMACIST, 'pharmacy@test.local');
        $medicine = Medicine::create(['name'=>'Amoxicillin','stock_quantity'=>20,'unit_price'=>250]);
        $prescription = Prescription::create(['patient_id'=>$consultation->patient_id,'consultation_id'=>$consultation->id,'doctor_id'=>$doctor->id,'status'=>'pending']);
        $prescription->items()->create(['medicine_id'=>$medicine->id,'dosage'=>'500 mg','frequency'=>'Three times daily','duration'=>'5 days','quantity'=>15]);
        $this->actingAs($pharmacist)->patch("/prescriptions/{$prescription->id}/dispense")->assertSessionHas('success');
        $this->assertSame(5, $medicine->fresh()->stock_quantity);
        $this->assertSame('dispensed', $prescription->fresh()->status);
    }

    public function test_prescription_is_not_dispensed_when_stock_is_insufficient(): void
    {
        [$doctor, $consultation] = $this->consultation();
        $pharmacist = $this->user(User::ROLE_PHARMACIST, 'low-stock@test.local');
        $medicine = Medicine::create(['name'=>'Cefuroxime','stock_quantity'=>2,'unit_price'=>400]);
        $prescription = Prescription::create(['patient_id'=>$consultation->patient_id,'consultation_id'=>$consultation->id,'doctor_id'=>$doctor->id,'status'=>'pending']);
        $prescription->items()->create(['medicine_id'=>$medicine->id,'dosage'=>'250 mg','quantity'=>5]);
        $this->actingAs($pharmacist)->patch("/prescriptions/{$prescription->id}/dispense")->assertSessionHas('error');
        $this->assertSame(2, $medicine->fresh()->stock_quantity);
        $this->assertSame('pending', $prescription->fresh()->status);
    }

    public function test_pharmacist_can_manage_medicine_stock(): void
    {
        $pharmacist = $this->user(User::ROLE_PHARMACIST, 'stock@test.local');
        $this->actingAs($pharmacist)->post('/medicines', ['name'=>'Vitamin C','stock_quantity'=>50,'unit_price'=>150])->assertRedirect(route('medicines.index'));
        $this->assertDatabaseHas('medicines', ['name'=>'Vitamin C','stock_quantity'=>50]);
    }

    private function consultation(): array
    {
        $doctor = $this->user(User::ROLE_DOCTOR, uniqid('doctor').'@test.local');
        $patient = Patient::create(['patient_number'=>'HMS-'.str_pad((string)(Patient::count()+1),5,'0',STR_PAD_LEFT),'first_name'=>'Test','last_name'=>'Patient']);
        $consultation = Consultation::create(['patient_id'=>$patient->id,'doctor_id'=>$doctor->id,'complaint'=>'Test complaint','diagnosis'=>'Test diagnosis']);
        return [$doctor, $consultation];
    }

    private function user(string $role, string $email): User
    {
        return User::create(['name'=>ucwords(str_replace('_',' ',$role)),'email'=>$email,'password'=>Hash::make('secret123'),'role'=>$role,'status'=>'active']);
    }
}
