@extends('layouts.app')

@section('title', 'Dashboard | Hospital Management System')

@section('content')
@php($role = auth()->user()->role)

<section class="hms-page-hero mb-4">
    <div>
        <span class="hms-eyebrow">Hospital workspace</span>
        <h1 class="h2 mb-2">Welcome, {{ auth()->user()->name }}</h1>
        <p class="text-secondary mb-0">The core patient workflow is ready for demonstration and testing.</p>
    </div>
    <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
        @if (in_array($role, ['admin', 'receptionist'], true))
            <a href="{{ route('patients.create') }}" class="btn btn-primary">Register Patient</a>
            <a href="{{ route('appointments.create') }}" class="btn btn-outline-primary">Book Appointment</a>
        @elseif ($role === 'doctor')
            <a href="{{ route('appointments.index', ['status' => 'scheduled']) }}" class="btn btn-primary">My Appointments</a>
            <a href="{{ route('consultations.index') }}" class="btn btn-outline-primary">Medical Records</a>
        @elseif ($role === 'lab_staff')
            <a href="{{ route('lab-tests.index', ['status' => 'pending']) }}" class="btn btn-primary">Pending Lab Tests</a>
        @elseif ($role === 'pharmacist')
            <a href="{{ route('prescriptions.index', ['status' => 'pending']) }}" class="btn btn-primary">Pending Prescriptions</a>
            <a href="{{ route('medicines.index') }}" class="btn btn-outline-primary">Medicine Stock</a>
        @endif
    </div>
</section>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('patients.index') }}" class="text-decoration-none text-dark">
            <div class="card hms-metric-card h-100"><div class="card-body"><div class="hms-metric-label">Registered Patients</div><div class="hms-metric-value">{{ $patientCount }}</div><div class="hms-metric-caption">Patient records in the system</div></div></div>
        </a>
    </div>

    @if ($role === 'admin')
        <div class="col-sm-6 col-xl-3"><a href="{{ route('staff.index') }}" class="text-decoration-none text-dark"><div class="card hms-metric-card h-100"><div class="card-body"><div class="hms-metric-label">Active Doctors</div><div class="hms-metric-value">{{ $doctorCount }}</div><div class="hms-metric-caption">Available doctor accounts</div></div></div></a></div>
    @endif

    @if (in_array($role, ['admin', 'receptionist', 'doctor'], true))
        <div class="col-sm-6 col-xl-3"><a href="{{ route('appointments.index', ['date' => now()->toDateString()]) }}" class="text-decoration-none text-dark"><div class="card hms-metric-card h-100"><div class="card-body"><div class="hms-metric-label">Today's Appointments</div><div class="hms-metric-value">{{ $todayAppointments }}</div><div class="hms-metric-caption">Scheduled activity for today</div></div></div></a></div>
    @endif

    @if (in_array($role, ['admin', 'doctor', 'lab_staff'], true))
        <div class="col-sm-6 col-xl-3"><a href="{{ route('lab-tests.index', ['status' => 'pending']) }}" class="text-decoration-none text-dark"><div class="card hms-metric-card h-100"><div class="card-body"><div class="hms-metric-label">Pending Lab Tests</div><div class="hms-metric-value">{{ $pendingLabTests }}</div><div class="hms-metric-caption">Requests awaiting results</div></div></div></a></div>
    @endif

    @if (in_array($role, ['admin', 'doctor', 'pharmacist'], true))
        <div class="col-sm-6 col-xl-3"><a href="{{ route('prescriptions.index', ['status' => 'pending']) }}" class="text-decoration-none text-dark"><div class="card hms-metric-card h-100"><div class="card-body"><div class="hms-metric-label">Pending Prescriptions</div><div class="hms-metric-value">{{ $pendingPrescriptions }}</div><div class="hms-metric-caption">Prescriptions awaiting dispensing</div></div></div></a></div>
    @endif

    @if (in_array($role, ['admin', 'receptionist'], true))
        <div class="col-sm-6 col-xl-3"><a href="{{ route('billing.index', ['status' => 'unpaid']) }}" class="text-decoration-none text-dark"><div class="card hms-metric-card h-100"><div class="card-body"><div class="hms-metric-label">Unpaid Bills</div><div class="hms-metric-value">{{ $unpaidBills }}</div><div class="hms-metric-caption">Bills awaiting prototype payment</div></div></div></a></div>
    @endif
</div>

<div class="row g-4">
    @if (in_array($role, ['admin', 'receptionist', 'doctor'], true))
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                        <div><h2 class="h5 mb-1">Upcoming Appointments</h2><p class="small text-secondary mb-0">The next scheduled patient visits.</p></div>
                        <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light"><tr><th>Patient</th><th>Doctor</th><th>Date & Time</th><th>Status</th></tr></thead>
                            <tbody>
                            @forelse ($upcomingAppointments as $appointment)
                                <tr>
                                    <td><a href="{{ route('patients.show', $appointment->patient) }}" class="fw-semibold text-decoration-none">{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</a><div class="small text-secondary">{{ $appointment->patient->patient_number }}</div></td>
                                    <td>{{ $appointment->doctor->name }}</td>
                                    <td>{{ $appointment->appointment_date->format('d M Y, h:i A') }}</td>
                                    <td><span class="badge text-bg-primary">Scheduled</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><div class="hms-empty-state"><strong>No upcoming appointments</strong><span>The schedule is clear for now.</span></div></td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="{{ in_array($role, ['admin', 'receptionist', 'doctor'], true) ? 'col-xl-4' : 'col-12' }}">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <span class="badge text-bg-success mb-3">MVP Ready</span>
                <h2 class="h5">Prototype workflow</h2>
                <p class="text-secondary small">The complete final-year demonstration path is connected end to end.</p>
                <ol class="hms-workflow-list mb-4">
                    <li>Patient registration</li>
                    <li>Appointment and doctor assignment</li>
                    <li>Consultation and medical record</li>
                    <li>Laboratory and prescription</li>
                    <li>Pharmacy dispensing</li>
                    <li>Billing</li>
                    <li>Simulated payment and receipt</li>
                </ol>
                <div class="hms-note"><strong>Prototype note:</strong> payment is simulated and does not contact a real payment gateway.</div>
            </div>
        </div>
    </div>
</div>
@endsection
