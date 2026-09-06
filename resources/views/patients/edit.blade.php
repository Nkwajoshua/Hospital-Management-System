@extends('layouts.app')

@section('title', 'Edit Patient | Hospital Management System')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Edit Patient</h1>
        <p class="text-secondary mb-0">Update {{ $patient->patient_number }}.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('patients.update', $patient) }}" method="POST">
                @csrf
                @method('PUT')
                @include('patients._form', ['submitLabel' => 'Save Changes'])
            </form>
        </div>
    </div>
@endsection
