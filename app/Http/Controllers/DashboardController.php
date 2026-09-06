<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $appointmentQuery = Appointment::query();

        if ($user->role === User::ROLE_DOCTOR) {
            $appointmentQuery->where('doctor_id', $user->id);
        }

        $todayAppointments = (clone $appointmentQuery)
            ->whereDate('appointment_date', today())
            ->count();

        $upcomingAppointments = (clone $appointmentQuery)
            ->with(['patient', 'doctor', 'consultation'])
            ->where('status', 'scheduled')
            ->where('appointment_date', '>=', now()->startOfDay())
            ->orderBy('appointment_date')
            ->limit(5)
            ->get();

        $pendingLabTests = match ($user->role) {
            User::ROLE_ADMIN, User::ROLE_LAB_STAFF => LabTest::where('status', 'pending')->count(),
            User::ROLE_DOCTOR => LabTest::where('status', 'pending')->where('requested_by', $user->id)->count(),
            default => 0,
        };

        $pendingPrescriptions = match ($user->role) {
            User::ROLE_ADMIN, User::ROLE_PHARMACIST => Prescription::where('status', 'pending')->count(),
            User::ROLE_DOCTOR => Prescription::where('status', 'pending')->where('doctor_id', $user->id)->count(),
            default => 0,
        };

        $unpaidBills = in_array($user->role, [User::ROLE_ADMIN, User::ROLE_RECEPTIONIST], true)
            ? Bill::where('status', 'unpaid')->count()
            : 0;

        return view('dashboard', [
            'patientCount' => Patient::count(),
            'doctorCount' => User::where('role', User::ROLE_DOCTOR)->where('status', 'active')->count(),
            'todayAppointments' => $todayAppointments,
            'pendingLabTests' => $pendingLabTests,
            'pendingPrescriptions' => $pendingPrescriptions,
            'unpaidBills' => $unpaidBills,
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }
}
