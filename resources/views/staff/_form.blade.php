@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Full Name</label>
        <input id="name" name="name" class="form-control" value="{{ old('name', $staffMember->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $staffMember->email ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label for="role" class="form-label">Role</label>
        <select id="role" name="role" class="form-select" required>
            @foreach ($roles as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $staffMember->role ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" class="form-select" required>
            <option value="active" @selected(old('status', $staffMember->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $staffMember->status ?? 'active') === 'inactive')>Inactive</option>
        </select>
    </div>
    <div class="col-12">
        <label for="password" class="form-label">Password {{ isset($staffMember) ? '(leave blank to keep current password)' : '' }}</label>
        <input id="password" name="password" type="password" class="form-control" {{ isset($staffMember) ? '' : 'required' }}>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
