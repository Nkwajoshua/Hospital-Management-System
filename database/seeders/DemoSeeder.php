<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Consultation;
use App\Models\LabTest;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Demo Administrator', 'email' => 'admin@hms.test', 'role' => User::ROLE_ADMIN],
            ['name' => 'Grace Reception', 'email' => 'reception@hms.test', 'role' => User::ROLE_RECEPTIONIST],
            ['name' => 'Dr. Samuel Okoro', 'email' => 'doctor@hms.test', 'role' => User::ROLE_DOCTOR],
            ['name' => 'Ada Laboratory', 'email' => 'lab@hms.test', 'role' => User::ROLE_LAB_STAFF],
            ['name' => 'Michael Pharmacy', 'email' => 'pharmacy@hms.test', 'role' => User::ROLE_PHARMACIST],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                    'status' => 'active',
                ]
            );
        }

        $doctor = User::where('email', 'doctor@hms.test')->firstOrFail();

        $paracetamol = Medicine::updateOrCreate(
            ['name' => 'Paracetamol 500mg'],
            ['stock_quantity' => 200, 'unit_price' => 300]
        );
        Medicine::updateOrCreate(
            ['name' => 'Artemether/Lumefantrine 20/120mg'],
            ['stock_quantity' => 100, 'unit_price' => 1800]
        );
        Medicine::updateOrCreate(
            ['name' => 'Amoxicillin 500mg'],
            ['stock_quantity' => 150, 'unit_price' => 1200]
        );

        $completedPatient = Patient::updateOrCreate(
            ['patient_number' => 'HMS-DEMO-001'],
            [
                'first_name' => 'Chinwe',
                'last_name' => 'Eze',
                'gender' => 'female',
                'date_of_birth' => '2000-04-18',
                'phone' => '08030000001',
                'address' => 'Ekpoma, Edo State',
                'emergency_contact_name' => 'Emeka Eze',
                'emergency_contact_phone' => '08030000002',
            ]
        );

        $appointment = Appointment::updateOrCreate(
            [
                'patient_id' => $completedPatient->id,
                'doctor_id' => $doctor->id,
                'reason' => 'Persistent fever and headache',
            ],
            [
                'appointment_date' => now()->subDay()->setTime(10, 0),
                'status' => 'completed',
            ]
        );

        $consultation = Consultation::updateOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'patient_id' => $completedPatient->id,
                'doctor_id' => $doctor->id,
                'complaint' => 'Fever, headache and weakness for three days.',
                'diagnosis' => 'Uncomplicated malaria.',
                'treatment' => 'Antimalarial therapy, fluids and symptomatic treatment.',
                'notes' => 'Patient advised to return if symptoms persist.',
            ]
        );

        LabTest::updateOrCreate(
            ['consultation_id' => $consultation->id, 'test_name' => 'Malaria Parasite Test'],
            [
                'patient_id' => $completedPatient->id,
                'requested_by' => $doctor->id,
                'result' => 'Malaria parasite detected.',
                'status' => 'completed',
            ]
        );

        $prescription = Prescription::firstOrCreate(
            ['consultation_id' => $consultation->id],
            [
                'patient_id' => $completedPatient->id,
                'doctor_id' => $doctor->id,
                'status' => 'dispensed',
            ]
        );
        $prescription->update(['status' => 'dispensed']);
        $prescription->items()->updateOrCreate(
            ['medicine_id' => $paracetamol->id],
            [
                'dosage' => '500mg',
                'frequency' => 'Three times daily',
                'duration' => '3 days',
                'quantity' => 2,
            ]
        );

        $billDetails = [
            ['label' => 'Consultation', 'quantity' => 1, 'unit_price' => 5000, 'amount' => 5000],
            ['label' => 'Laboratory: Malaria Parasite Test', 'quantity' => 1, 'unit_price' => 2000, 'amount' => 2000],
            ['label' => 'Medicine: Paracetamol 500mg', 'quantity' => 2, 'unit_price' => 300, 'amount' => 600],
        ];

        $bill = Bill::updateOrCreate(
            ['consultation_id' => $consultation->id],
            [
                'patient_id' => $completedPatient->id,
                'reference' => 'HMS-BILL-DEMO-001',
                'amount' => 7600,
                'details' => $billDetails,
                'status' => 'paid',
            ]
        );

        Payment::updateOrCreate(
            ['bill_id' => $bill->id],
            [
                'reference' => 'HMS-PAY-DEMO-001',
                'amount' => 7600,
                'payment_method' => 'cash',
                'payment_date' => now()->subDay()->setTime(11, 30),
            ]
        );

        $scheduledPatient = Patient::updateOrCreate(
            ['patient_number' => 'HMS-DEMO-002'],
            [
                'first_name' => 'Ibrahim',
                'last_name' => 'Musa',
                'gender' => 'male',
                'date_of_birth' => '1998-09-12',
                'phone' => '08040000001',
                'address' => 'Ekpoma, Edo State',
            ]
        );

        Appointment::updateOrCreate(
            [
                'patient_id' => $scheduledPatient->id,
                'doctor_id' => $doctor->id,
                'reason' => 'General consultation',
            ],
            [
                'appointment_date' => now()->addDay()->setTime(9, 30),
                'status' => 'scheduled',
            ]
        );

        $this->command?->info('Demo HMS data created. All demo staff accounts use password: password');
    }
}
