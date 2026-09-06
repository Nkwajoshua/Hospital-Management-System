@extends('layouts.app')

@section('title', 'Appointments | Hospital Management System')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Appointments</h1>
            <p class="text-secondary mb-0">
                {{ auth()->user()->role === 'doctor' ? 'Your assigned patient appointments.' : 'Manage patient appointments and doctor assignments.' }}
            </p>
        </div>

        @if (in_array(auth()->user()->role, ['admin', 'receptionist'], true))
            <a href="{{ route('appointments.create') }}" class="btn btn-primary">Book Appointment</a>
        @endif
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('appointments.index') }}" class="row g-2">
                <div class="col-md-5">
                    <label for="date" class="form-label small text-secondary">Date</label>
                    <input id="date" type="date" name="date" value="{{ $date }}" class="form-control">
                </div>
                <div class="col-md-5">
                    <label for="status" class="form-label small text-secondary">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach (['scheduled' => 'Scheduled', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid align-self-end">
                    <button class="btn btn-outline-primary" type="submit">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->appointment_date->format('d M Y, h:i A') }}</td>
                            <td>
                                <a href="{{ route('patients.show', $appointment->patient) }}" class="text-decoration-none">
                                    {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                </a>
                                <div class="small text-secondary">{{ $appointment->patient->patient_number }}</div>
                            </td>
                            <td>{{ $appointment->doctor->name }}</td>
                            <td>{{ $appointment->reason ?: '—' }}</td>
                            <td><span class="badge text-bg-{{ $appointment->status === 'scheduled' ? 'primary' : ($appointment->status === 'completed' ? 'success' : 'secondary') }} text-capitalize">{{ $appointment->status }}</span></td>
                            <td class="text-end text-nowrap">
                                @if (auth()->user()->role === 'doctor' && $appointment->doctor_id === auth()->id())
                                    @if ($appointment->consultation)
                                        <a href="{{ route('consultations.show', $appointment->consultation) }}" class="btn btn-sm btn-outline-primary">Medical Record</a>
                                    @elseif ($appointment->status === 'scheduled')
                                        <a href="{{ route('consultations.create', $appointment) }}" class="btn btn-sm btn-primary">Start Consultation</a>
                                    @endif
                                @elseif (auth()->user()->role === 'admin' && $appointment->consultation)
                                    <a href="{{ route('consultations.show', $appointment->consultation) }}" class="btn btn-sm btn-outline-primary">Medical Record</a>
                                @endif

                                @if (in_array(auth()->user()->role, ['admin', 'receptionist'], true))
                                    @if ($appointment->status !== 'completed')
                                        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    @endif
                                    @if ($appointment->status === 'scheduled')
                                        <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-5">No appointments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $appointments->links() }}</div>
@endsection
