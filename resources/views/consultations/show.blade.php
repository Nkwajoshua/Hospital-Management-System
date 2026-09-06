@extends('layouts.app')

@section('title', 'Medical Record | Hospital Management System')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="text-secondary">{{ $consultation->patient->patient_number }}</div>
            <h1 class="h3 mb-1">{{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}</h1>
            <p class="text-secondary mb-0">Consulted by {{ $consultation->doctor->name }} on {{ $consultation->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="d-flex gap-2">
            @if (auth()->user()->role === 'doctor' && $consultation->doctor_id === auth()->id())
                <a href="{{ route('consultations.edit', $consultation) }}" class="btn btn-outline-primary">Edit Record</a>
            @endif
            <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="mb-4"><div class="text-secondary small mb-1">Complaint / Symptoms</div><div>{{ $consultation->complaint }}</div></div>
                    <div class="mb-4"><div class="text-secondary small mb-1">Diagnosis</div><div>{{ $consultation->diagnosis }}</div></div>
                    <div class="mb-4"><div class="text-secondary small mb-1">Treatment / Plan</div><div>{{ $consultation->treatment ?: 'Not specified' }}</div></div>
                    <div><div class="text-secondary small mb-1">Additional Notes</div><div>{{ $consultation->notes ?: 'None' }}</div></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6">Linked Appointment</h2>
                    <div>{{ $consultation->appointment?->appointment_date?->format('d M Y, h:i A') ?: 'No appointment' }}</div>
                    <span class="badge text-bg-success mt-2">Completed</span>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6">Next Clinical Actions</h2>
                    <p class="text-secondary small mb-0">Laboratory requests and prescriptions will be attached to this consultation in the next modules.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
