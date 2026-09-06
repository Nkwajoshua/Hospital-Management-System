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
        <label for="patient_id" class="form-label">Patient</label>
        <select id="patient_id" name="patient_id" class="form-select" required>
            <option value="">Select patient</option>
            @foreach ($patients as $patientOption)
                @php($patientValue = old('patient_id', $appointment->patient_id ?? $selectedPatientId ?? ''))
                <option value="{{ $patientOption->id }}" @selected((string) $patientValue === (string) $patientOption->id)>
                    {{ $patientOption->patient_number }} — {{ $patientOption->first_name }} {{ $patientOption->last_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label for="doctor_id" class="form-label">Doctor</label>
        <select id="doctor_id" name="doctor_id" class="form-select" required>
            <option value="">Select doctor</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected((string) old('doctor_id', $appointment->doctor_id ?? '') === (string) $doctor->id)>{{ $doctor->name }}</option>
            @endforeach
        </select>
        @if ($doctors->isEmpty())
            <div class="form-text text-danger">No active doctor account is available. An administrator must create one first.</div>
        @endif
    </div>

    <div class="col-md-6">
        <label for="appointment_date" class="form-label">Appointment Date & Time</label>
        <input id="appointment_date" type="datetime-local" name="appointment_date" class="form-control" value="{{ old('appointment_date', isset($appointment) ? $appointment->appointment_date->format('Y-m-d\TH:i') : '') }}" required>
    </div>

    @isset($appointment)
        <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select" required>
                <option value="scheduled" @selected(old('status', $appointment->status) === 'scheduled')>Scheduled</option>
                <option value="cancelled" @selected(old('status', $appointment->status) === 'cancelled')>Cancelled</option>
            </select>
        </div>
    @endisset

    <div class="col-12">
        <label for="reason" class="form-label">Reason for Appointment</label>
        <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Brief reason for the visit">{{ old('reason', $appointment->reason ?? '') }}</textarea>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
