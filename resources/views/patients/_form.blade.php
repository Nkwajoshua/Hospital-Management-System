@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-md-6">
        <label for="first_name" class="form-label">First Name</label>
        <input id="first_name" name="first_name" class="form-control" value="{{ old('first_name', $patient->first_name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label for="last_name" class="form-label">Last Name</label>
        <input id="last_name" name="last_name" class="form-control" value="{{ old('last_name', $patient->last_name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label for="gender" class="form-label">Gender</label>
        <select id="gender" name="gender" class="form-select">
            <option value="">Select</option>
            @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" @selected(old('gender', $patient->gender ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label for="date_of_birth" class="form-label">Date of Birth</label>
        <input id="date_of_birth" name="date_of_birth" type="date" class="form-control" value="{{ old('date_of_birth', isset($patient) && $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-4">
        <label for="phone" class="form-label">Phone</label>
        <input id="phone" name="phone" class="form-control" value="{{ old('phone', $patient->phone ?? '') }}">
    </div>
    <div class="col-12">
        <label for="address" class="form-label">Address</label>
        <textarea id="address" name="address" class="form-control" rows="2">{{ old('address', $patient->address ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
        <input id="emergency_contact_name" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}">
    </div>
    <div class="col-md-6">
        <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
        <input id="emergency_contact_phone" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone ?? '') }}">
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ isset($patient) ? route('patients.show', $patient) : route('patients.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
