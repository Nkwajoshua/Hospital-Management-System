@extends('layouts.app')

@section('title', 'Dashboard | Hospital Management System')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-secondary mb-0">A simple overview of the hospital workflow.</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('patients.index') }}" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary">Patients</div><div class="display-6 fw-semibold">{{ $patientCount }}</div></div></div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary">Active Doctors</div><div class="display-6 fw-semibold">{{ $doctorCount }}</div></div></div>
        </div>
        <div class="col-md-6 col-xl-4">
            @if (in_array(auth()->user()->role, ['admin', 'receptionist', 'doctor'], true))
                <a href="{{ route('appointments.index', ['date' => today()->format('Y-m-d')]) }}" class="text-decoration-none text-dark">
                    <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary">Today's Appointments</div><div class="display-6 fw-semibold">{{ $todayAppointments }}</div></div></div>
                </a>
            @else
                <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary">Today's Appointments</div><div class="display-6 fw-semibold">{{ $todayAppointments }}</div></div></div>
            @endif
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary">Pending Lab Tests</div><div class="display-6 fw-semibold">{{ $pendingLabTests }}</div></div></div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary">Pending Prescriptions</div><div class="display-6 fw-semibold">{{ $pendingPrescriptions }}</div></div></div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary">Unpaid Bills</div><div class="display-6 fw-semibold">{{ $unpaidBills }}</div></div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">Current MVP modules</h2>
            <p class="text-secondary mb-0">Authentication, patients, appointments and doctor consultations are active. Laboratory and prescription workflows are next.</p>
        </div>
    </div>
@endsection
