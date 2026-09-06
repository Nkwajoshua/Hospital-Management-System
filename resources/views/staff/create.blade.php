@extends('layouts.app')

@section('title', 'Add Staff | Hospital Management System')

@section('content')
    <div class="mb-4"><h1 class="h3 mb-1">Add Staff Account</h1><p class="text-secondary mb-0">Create a login for hospital staff.</p></div>
    <div class="card border-0 shadow-sm"><div class="card-body p-4"><form action="{{ route('staff.store') }}" method="POST">@csrf @include('staff._form', ['submitLabel' => 'Create Account'])</form></div></div>
@endsection
