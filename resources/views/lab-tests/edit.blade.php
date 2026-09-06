@extends('layouts.app')
@section('title', 'Lab Result | Hospital Management System')
@section('content')
<div class="mb-4"><h1 class="h3 mb-1">Record Laboratory Result</h1><p class="text-secondary mb-0">{{ $labTest->patient->patient_number }} · {{ $labTest->patient->first_name }} {{ $labTest->patient->last_name }}</p></div>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><div class="mb-3"><strong>Test:</strong> {{ $labTest->test_name }}</div><form action="{{ route('lab-tests.update',$labTest) }}" method="POST">@csrf @method('PUT')<label for="result" class="form-label">Result</label><textarea id="result" name="result" class="form-control" rows="7" required>{{ old('result',$labTest->result) }}</textarea>@error('result')<div class="text-danger small mt-1">{{ $message }}</div>@enderror<div class="d-flex gap-2 mt-4"><button class="btn btn-primary">Save Result</button><a href="{{ route('lab-tests.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
