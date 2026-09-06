@extends('layouts.app')
@section('title', 'Medicines | Hospital Management System')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3 mb-1">Medicine Stock</h1><p class="text-secondary mb-0">Simple pharmacy inventory for the HMS prototype.</p></div><a href="{{ route('medicines.create') }}" class="btn btn-primary">Add Medicine</a></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Medicine</th><th>Quantity</th><th>Unit Price</th><th></th></tr></thead><tbody>@forelse($medicines as $medicine)<tr><td>{{ $medicine->name }}</td><td>{{ $medicine->stock_quantity }}</td><td>₦{{ number_format((float)$medicine->unit_price,2) }}</td><td class="text-end"><a href="{{ route('medicines.edit',$medicine) }}" class="btn btn-sm btn-outline-secondary">Edit</a></td></tr>@empty<tr><td colspan="4" class="text-center text-secondary py-5">No medicines added yet.</td></tr>@endforelse</tbody></table></div></div><div class="mt-3">{{ $medicines->links() }}</div>
@endsection
