@extends('layouts.app')

@section('title', 'Billing | Hospital Management System')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h1 class="h3 mb-1">Billing</h1><p class="text-secondary mb-0">Patient bills and prototype payment status.</p></div>
    </div>

    <form class="row g-2 mb-4" method="GET">
        <div class="col-md-6"><input class="form-control" name="search" value="{{ $search }}" placeholder="Search patient name or number"></div>
        <div class="col-md-3"><select class="form-select" name="status"><option value="">All statuses</option><option value="unpaid" @selected($status === 'unpaid')>Unpaid</option><option value="paid" @selected($status === 'paid')>Paid</option></select></div>
        <div class="col-md-3"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Reference</th><th>Patient</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr></thead>
                <tbody>
                @forelse ($bills as $bill)
                    <tr>
                        <td class="fw-semibold">{{ $bill->reference }}</td>
                        <td>{{ $bill->patient->patient_number }} · {{ $bill->patient->first_name }} {{ $bill->patient->last_name }}</td>
                        <td>₦{{ number_format((float) $bill->amount, 2) }}</td>
                        <td><span class="badge {{ $bill->status === 'paid' ? 'text-bg-success' : 'text-bg-warning' }} text-uppercase">{{ $bill->status }}</span></td>
                        <td>{{ $bill->created_at->format('d M Y') }}</td>
                        <td><a class="btn btn-sm btn-outline-primary" href="{{ route('billing.show', $bill) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-secondary py-5">No bills found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($bills->hasPages())<div class="card-footer bg-white">{{ $bills->links() }}</div>@endif
    </div>
@endsection
