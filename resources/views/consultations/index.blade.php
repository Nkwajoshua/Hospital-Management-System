@extends('layouts.app')

@section('title', 'Medical Records | Hospital Management System')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Medical Records</h1>
        <p class="text-secondary mb-0">Completed doctor consultations and patient clinical records.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Diagnosis</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($consultations as $consultation)
                        <tr>
                            <td>{{ $consultation->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                {{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}
                                <div class="small text-secondary">{{ $consultation->patient->patient_number }}</div>
                            </td>
                            <td>{{ $consultation->doctor->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($consultation->diagnosis, 80) }}</td>
                            <td class="text-end"><a href="{{ route('consultations.show', $consultation) }}" class="btn btn-sm btn-outline-primary">View Record</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-5">No medical records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $consultations->links() }}</div>
@endsection
