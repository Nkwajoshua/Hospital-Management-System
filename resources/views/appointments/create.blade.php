@extends('layouts.app')

@section('title', 'Book Appointment | Hospital Management System')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Book Appointment</h1>
        <p class="text-secondary mb-0">Assign a patient to an available doctor.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('appointments.store') }}" method="POST">
                @csrf
                @include('appointments._form', ['submitLabel' => 'Book Appointment'])
            </form>
        </div>
    </div>
@endsection
