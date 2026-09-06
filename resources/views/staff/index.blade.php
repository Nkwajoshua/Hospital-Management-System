@extends('layouts.app')

@section('title', 'Staff | Hospital Management System')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Staff Accounts</h1>
            <p class="text-secondary mb-0">Manage users and their hospital roles.</p>
        </div>
        <a href="{{ route('staff.create') }}" class="btn btn-primary">Add Staff</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($staff as $member)
                        <tr>
                            <td>{{ $member->name }} @if($member->id === auth()->id())<span class="badge text-bg-light">You</span>@endif</td>
                            <td>{{ $member->email }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $member->role) }}</td>
                            <td><span class="badge text-bg-{{ $member->status === 'active' ? 'success' : 'secondary' }} text-capitalize">{{ $member->status }}</span></td>
                            <td class="text-end"><a href="{{ route('staff.edit', $member) }}" class="btn btn-sm btn-outline-secondary">Edit</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-5">No staff accounts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $staff->links() }}</div>
@endsection
