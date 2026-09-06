@extends('layouts.app')

@section('title', 'Simulated Payment | Hospital Management System')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <span class="badge text-bg-info mb-3">Prototype Payment</span>
                    <h1 class="h3">Complete Payment</h1>
                    <p class="text-secondary">No real payment gateway is connected. Selecting a method and continuing will simulate a successful transaction for the final-year project demonstration.</p>
                    <div class="border rounded p-3 mb-4"><div class="small text-secondary">Bill</div><div class="fw-semibold">{{ $bill->reference }}</div><div class="small text-secondary mt-2">Patient</div><div>{{ $bill->patient->first_name }} {{ $bill->patient->last_name }}</div><div class="small text-secondary mt-2">Amount</div><div class="h3 mb-0">₦{{ number_format((float) $bill->amount, 2) }}</div></div>
                    <form method="POST" action="{{ route('billing.pay', $bill) }}">@csrf
                        <label class="form-label" for="payment_method">Payment Method</label>
                        <select class="form-select mb-4" id="payment_method" name="payment_method" required><option value="cash">Cash</option><option value="card">Card</option><option value="bank_transfer">Bank Transfer</option></select>
                        <div class="d-grid gap-2"><button class="btn btn-success btn-lg" type="submit">Pay ₦{{ number_format((float) $bill->amount, 2) }}</button><a class="btn btn-outline-secondary" href="{{ route('billing.show', $bill) }}">Cancel</a></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
