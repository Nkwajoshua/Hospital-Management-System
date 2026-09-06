@extends('layouts.app')

@section('title', 'Edit Staff | Hospital Management System')

@section('content')
    <div class="mb-4"><h1 class="h3 mb-1">Edit Staff Account</h1><p class="text-secondary mb-0">Update {{ $staffMember->name }}.</p></div>
    <div class="card border-0 shadow-sm"><div class="card-body p-4"><form action="{{ route('staff.update', $staffMember) }}" method="POST">@csrf @method('PUT') @include('staff._form', ['submitLabel' => 'Save Changes'])</form></div></div>
@endsection
