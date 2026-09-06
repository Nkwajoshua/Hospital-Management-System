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
    <div class="col-12">
        <label for="complaint" class="form-label">Patient Complaint / Symptoms</label>
        <textarea id="complaint" name="complaint" class="form-control" rows="3" required>{{ old('complaint', $consultation->complaint ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label for="diagnosis" class="form-label">Diagnosis</label>
        <textarea id="diagnosis" name="diagnosis" class="form-control" rows="3" required>{{ old('diagnosis', $consultation->diagnosis ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label for="treatment" class="form-label">Treatment / Plan</label>
        <textarea id="treatment" name="treatment" class="form-control" rows="3">{{ old('treatment', $consultation->treatment ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label for="notes" class="form-label">Additional Notes</label>
        <textarea id="notes" name="notes" class="form-control" rows="3">{{ old('notes', $consultation->notes ?? '') }}</textarea>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ isset($consultation) ? route('consultations.show', $consultation) : route('appointments.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
