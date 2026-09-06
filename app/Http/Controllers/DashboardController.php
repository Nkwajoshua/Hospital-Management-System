<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'patientCount' => Patient::count(),
            'doctorCount' => User::where('role', User::ROLE_DOCTOR)->where('status', 'active')->count(),
            'todayAppointments' => Appointment::whereDate('appointment_date', today())->count(),
            'pendingLabTests' => LabTest::where('status', 'pending')->count(),
            'pendingPrescriptions' => Prescription::where('status', 'pending')->count(),
            'unpaidBills' => Bill::where('status', 'unpaid')->count(),
        ]);
    }
}
