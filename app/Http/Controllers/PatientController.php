<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $patients = Patient::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('patient_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('patients.index', compact('patients', 'search'));
    }

    public function create(): View
    {
        return view('patients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePatient($request);
        $validated['patient_number'] = $this->nextPatientNumber();

        $patient = Patient::create($validated);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient registered successfully.');
    }

    public function show(Patient $patient): View
    {
        $patient->loadCount([
            'appointments',
            'consultations',
            'labTests',
            'prescriptions',
            'bills',
        ]);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $patient->update($this->validatePatient($request));

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient information updated successfully.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $hasActivity = $patient->appointments()->exists()
            || $patient->consultations()->exists()
            || $patient->labTests()->exists()
            || $patient->prescriptions()->exists()
            || $patient->bills()->exists();

        if ($hasActivity) {
            return redirect()
                ->route('patients.show', $patient)
                ->with('error', 'This patient has hospital records and cannot be deleted.');
        }

        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient record deleted.');
    }

    private function validatePatient(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
        ]);
    }

    private function nextPatientNumber(): string
    {
        $nextId = ((int) Patient::max('id')) + 1;

        do {
            $number = 'HMS-'.str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);
            $nextId++;
        } while (Patient::where('patient_number', $number)->exists());

        return $number;
    }
}
