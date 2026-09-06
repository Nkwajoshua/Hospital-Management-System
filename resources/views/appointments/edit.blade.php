@extends('layouts.app')

@section('title', 'Edit Appointment | Hospital Management System')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Edit Appointment</h1>
        <p class="text-secondary mb-0">Update the appointment details or status.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('appointments.update', $appointment) }}" method="POST">
                @csrf
                @method('PUT')
                @include('appointments._form', ['submitLabel' => 'Save Changes'])
            </form>
        </div>
    </div>
@endsection
