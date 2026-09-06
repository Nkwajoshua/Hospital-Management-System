@extends('layouts.app')

@section('title', $bill->reference.' | Billing')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div><div class="text-secondary">{{ $bill->reference }}</div><h1 class="h3 mb-1">Patient Bill</h1><p class="text-secondary mb-0">{{ $bill->patient->patient_number }} · {{ $bill->patient->first_name }} {{ $bill->patient->last_name }}</p></div>
        <div class="d-flex gap-2">
            @if ($bill->status === 'unpaid')<a class="btn btn-success" href="{{ route('billing.checkout', $bill) }}">Proceed to Payment</a>@else<a class="btn btn-outline-success" href="{{ route('billing.receipt', $bill) }}">View Receipt</a>@endif
            <a class="btn btn-outline-secondary" href="{{ route('billing.index') }}">Back</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3"><h2 class="h5">Bill Breakdown</h2><span class="badge {{ $bill->status === 'paid' ? 'text-bg-success' : 'text-bg-warning' }} text-uppercase">{{ $bill->status }}</span></div>
            <div class="table-responsive"><table class="table"><thead><tr><th>Service</th><th class="text-end">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Amount</th></tr></thead><tbody>
            @foreach ($bill->details ?? [] as $item)
                <tr><td>{{ $item['label'] }}</td><td class="text-end">{{ $item['quantity'] }}</td><td class="text-end">₦{{ number_format((float) $item['unit_price'], 2) }}</td><td class="text-end">₦{{ number_format((float) $item['amount'], 2) }}</td></tr>
            @endforeach
            </tbody><tfoot><tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td class="text-end">₦{{ number_format((float) $bill->amount, 2) }}</td></tr></tfoot></table></div>
            <p class="small text-secondary mb-0">This billing flow is part of the academic prototype. Payment is simulated and no real money is processed.</p>
        </div>
    </div>
@endsection
