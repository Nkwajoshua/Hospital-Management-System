@extends('layouts.app')

@section('title', $patient->patient_number.' | Hospital Management System')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="text-secondary">{{ $patient->patient_number }}</div>
            <h1 class="h3 mb-1">{{ $patient->first_name }} {{ $patient->last_name }}</h1>
            <p class="text-secondary mb-0">Patient profile and hospital activity summary.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if (in_array(auth()->user()->role, ['admin', 'receptionist'], true))
                <a href="{{ route('appointments.create', ['patient' => $patient->id]) }}" class="btn btn-primary">Book Appointment</a>
                @if ($patient->consultations_count > 0)
                    <form action="{{ route('billing.store', $patient) }}" method="POST">@csrf<button class="btn btn-success" type="submit">Generate Bill</button></form>
                @endif
                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-primary">Edit</a>
            @endif
            @if (in_array(auth()->user()->role, ['admin', 'doctor'], true) && $patient->consultations_count > 0)
                <a href="{{ route('consultations.index', ['patient' => $patient->id]) }}" class="btn btn-outline-primary">Medical Records</a>
            @endif
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Patient Information</h2>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Gender</dt><dd class="col-sm-8 text-capitalize">{{ $patient->gender ?: 'Not specified' }}</dd>
                        <dt class="col-sm-4">Date of Birth</dt><dd class="col-sm-8">{{ $patient->date_of_birth?->format('d M Y') ?: 'Not specified' }}</dd>
                        <dt class="col-sm-4">Phone</dt><dd class="col-sm-8">{{ $patient->phone ?: 'Not specified' }}</dd>
                        <dt class="col-sm-4">Address</dt><dd class="col-sm-8">{{ $patient->address ?: 'Not specified' }}</dd>
                        <dt class="col-sm-4">Emergency Contact</dt><dd class="col-sm-8">{{ $patient->emergency_contact_name ?: 'Not specified' }}</dd>
                        <dt class="col-sm-4">Emergency Phone</dt><dd class="col-sm-8">{{ $patient->emergency_contact_phone ?: 'Not specified' }}</dd>
                        <dt class="col-sm-4">Registered</dt><dd class="col-sm-8">{{ $patient->created_at->format('d M Y, h:i A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Hospital Activity</h2>
                    <div class="row g-2">
                        <div class="col-6"><div class="border rounded p-3"><div class="text-secondary small">Appointments</div><div class="h4 mb-0">{{ $patient->appointments_count }}</div></div></div>
                        <div class="col-6"><div class="border rounded p-3"><div class="text-secondary small">Consultations</div><div class="h4 mb-0">{{ $patient->consultations_count }}</div></div></div>
                        <div class="col-6"><div class="border rounded p-3"><div class="text-secondary small">Lab Tests</div><div class="h4 mb-0">{{ $patient->lab_tests_count }}</div></div></div>
                        <div class="col-6"><div class="border rounded p-3"><div class="text-secondary small">Prescriptions</div><div class="h4 mb-0">{{ $patient->prescriptions_count }}</div></div></div>
                        <div class="col-6"><div class="border rounded p-3"><div class="text-secondary small">Bills</div><div class="h4 mb-0">{{ $patient->bills_count }}</div></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()->role === 'admin')
        <div class="card border-danger mt-4">
            <div class="card-body">
                <h2 class="h6 text-danger">Delete Patient Record</h2>
                <p class="text-secondary small">Deletion is allowed only when the patient has no hospital activity.</p>
                <form action="{{ route('patients.destroy', $patient) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm" type="submit">Delete Patient</button>
                </form>
            </div>
        </div>
    @endif
@endsection
