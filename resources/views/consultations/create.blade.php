@extends('layouts.app')

@section('title', 'New Consultation | Hospital Management System')

@section('content')
    <div class="mb-4">
        <div class="text-secondary">{{ $appointment->patient->patient_number }}</div>
        <h1 class="h3 mb-1">Consultation: {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</h1>
        <p class="text-secondary mb-0">Appointment: {{ $appointment->appointment_date->format('d M Y, h:i A') }}</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><strong>Reason for visit:</strong><br>{{ $appointment->reason ?: 'Not specified' }}</div>
                <div class="col-md-6"><strong>Doctor:</strong><br>{{ $appointment->doctor->name }}</div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('consultations.store', $appointment) }}" method="POST">
                @csrf
                @include('consultations._form', ['submitLabel' => 'Complete Consultation'])
            </form>
        </div>
    </div>
@endsection
