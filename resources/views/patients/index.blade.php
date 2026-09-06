@extends('layouts.app')

@section('title', 'Patients | Hospital Management System')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Patients</h1>
            <p class="text-secondary mb-0">Register, search and view patient records.</p>
        </div>

        @if (in_array(auth()->user()->role, ['admin', 'receptionist'], true))
            <a href="{{ route('patients.create') }}" class="btn btn-primary">Register Patient</a>
        @endif
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('patients.index') }}" class="row g-2">
                <div class="col-md-10">
                    <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Search by patient number, name or phone">
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Patient No.</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Registered</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($patients as $patient)
                        <tr>
                            <td class="fw-medium">{{ $patient->patient_number }}</td>
                            <td>{{ $patient->first_name }} {{ $patient->last_name }}</td>
                            <td class="text-capitalize">{{ $patient->gender ?: 'Not specified' }}</td>
                            <td>{{ $patient->phone ?: '—' }}</td>
                            <td>{{ $patient->created_at->format('d M Y') }}</td>
                            <td class="text-end"><a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-secondary">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-5">No patient records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $patients->links() }}</div>
@endsection
