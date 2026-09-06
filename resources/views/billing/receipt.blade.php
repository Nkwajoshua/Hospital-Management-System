@extends('layouts.app')

@section('title', 'Receipt '.$bill->payment->reference)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none"><div><h1 class="h3 mb-0">Payment Receipt</h1></div><div class="d-flex gap-2"><button class="btn btn-primary" onclick="window.print()">Print Receipt</button><a class="btn btn-outline-secondary" href="{{ route('billing.show', $bill) }}">Back</a></div></div>
    <div class="card border-0 shadow-sm" id="receipt"><div class="card-body p-4 p-md-5">
        <div class="text-center mb-4"><h2 class="h3">Hospital Management System</h2><div class="text-secondary">Prototype Payment Receipt</div></div>
        <div class="row g-3 mb-4"><div class="col-sm-6"><strong>Receipt:</strong> {{ $bill->payment->reference }}</div><div class="col-sm-6 text-sm-end"><strong>Date:</strong> {{ $bill->payment->payment_date->format('d M Y, h:i A') }}</div><div class="col-sm-6"><strong>Patient:</strong> {{ $bill->patient->first_name }} {{ $bill->patient->last_name }}</div><div class="col-sm-6 text-sm-end"><strong>Patient ID:</strong> {{ $bill->patient->patient_number }}</div></div>
        <div class="table-responsive"><table class="table"><thead><tr><th>Description</th><th class="text-end">Qty</th><th class="text-end">Amount</th></tr></thead><tbody>@foreach ($bill->details ?? [] as $item)<tr><td>{{ $item['label'] }}</td><td class="text-end">{{ $item['quantity'] }}</td><td class="text-end">₦{{ number_format((float) $item['amount'], 2) }}</td></tr>@endforeach</tbody><tfoot><tr class="fw-bold"><td colspan="2" class="text-end">Total Paid</td><td class="text-end">₦{{ number_format((float) $bill->amount, 2) }}</td></tr></tfoot></table></div>
        <div class="mt-4"><strong>Payment Method:</strong> {{ ucwords(str_replace('_', ' ', $bill->payment->payment_method)) }}<br><strong>Status:</strong> <span class="text-success">PAID</span></div>
        <p class="text-secondary small mt-4 mb-0">Academic prototype receipt. No real financial transaction was processed.</p>
    </div></div>
@endsection
