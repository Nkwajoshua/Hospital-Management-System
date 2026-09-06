@extends('layouts.app')

@section('title', 'Register Patient | Hospital Management System')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Register Patient</h1>
        <p class="text-secondary mb-0">Create a new patient record.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('patients.store') }}" method="POST">
                @csrf
                @include('patients._form', ['submitLabel' => 'Register Patient'])
            </form>
        </div>
    </div>
@endsection
