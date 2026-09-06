@extends('layouts.app')

@section('title', 'Edit Medical Record | Hospital Management System')

@section('content')
    <div class="mb-4">
        <div class="text-secondary">{{ $consultation->patient->patient_number }}</div>
        <h1 class="h3 mb-1">Edit Medical Record</h1>
        <p class="text-secondary mb-0">{{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('consultations.update', $consultation) }}" method="POST">
                @csrf
                @method('PUT')
                @include('consultations._form', ['submitLabel' => 'Save Medical Record'])
            </form>
        </div>
    </div>
@endsection
