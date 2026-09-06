@extends('layouts.app')

@section('title', 'Payment Successful | Hospital Management System')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-5">
                    <div class="display-4 text-success mb-3">✓</div>
                    <h1 class="h2">Payment Successful</h1>
                    <p class="text-secondary">The prototype payment has been completed and the patient bill is now marked as paid.</p>
                    <div class="border rounded text-start p-3 my-4"><div class="row g-3"><div class="col-sm-6"><div class="small text-secondary">Payment Reference</div><strong>{{ $bill->payment->reference }}</strong></div><div class="col-sm-6"><div class="small text-secondary">Amount Paid</div><strong>₦{{ number_format((float) $bill->payment->amount, 2) }}</strong></div><div class="col-sm-6"><div class="small text-secondary">Method</div><strong>{{ ucwords(str_replace('_', ' ', $bill->payment->payment_method)) }}</strong></div><div class="col-sm-6"><div class="small text-secondary">Status</div><strong class="text-success">PAID</strong></div></div></div>
                    <div class="d-flex justify-content-center gap-2 flex-wrap"><a class="btn btn-success" href="{{ route('billing.receipt', $bill) }}">View Receipt</a><a class="btn btn-outline-primary" href="{{ route('patients.show', $bill->patient) }}">Patient Profile</a><a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Dashboard</a></div>
                </div>
            </div>
        </div>
    </div>
@endsection
