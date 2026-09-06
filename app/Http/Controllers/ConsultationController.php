<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $patientId = $request->integer('patient') ?: null;

        $consultations = Consultation::query()
            ->with(['patient', 'doctor', 'appointment'])
            ->when($user->role === User::ROLE_DOCTOR, fn (Builder $query) => $query->where('doctor_id', $user->id))
            ->when($patientId, fn (Builder $query) => $query->where('patient_id', $patientId))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('consultations.index', compact('consultations'));
    }

    public function create(Request $request, Appointment $appointment): View|RedirectResponse
    {
        $this->authorizeDoctorForAppointment($request, $appointment);

        if ($appointment->consultation) {
            return redirect()->route('consultations.show', $appointment->consultation);
        }

        if ($appointment->status !== 'scheduled') {
            return redirect()->route('appointments.index')->with('error', 'Only scheduled appointments can be consulted.');
        }

        $appointment->load(['patient', 'doctor']);

        return view('consultations.create', compact('appointment'));
    }

    public function store(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeDoctorForAppointment($request, $appointment);

        if ($appointment->consultation) {
            return redirect()->route('consultations.show', $appointment->consultation)
                ->with('error', 'A consultation already exists for this appointment.');
        }

        if ($appointment->status !== 'scheduled') {
            return redirect()->route('appointments.index')->with('error', 'Only scheduled appointments can be consulted.');
        }

        $validated = $this->validateConsultation($request);

        $consultation = DB::transaction(function () use ($appointment, $request, $validated) {
            $consultation = Consultation::create([
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $request->user()->id,
                'appointment_id' => $appointment->id,
                ...$validated,
            ]);

            $appointment->update(['status' => 'completed']);

            return $consultation;
        });

        return redirect()
            ->route('consultations.show', $consultation)
            ->with('success', 'Consultation recorded successfully.');
    }

    public function show(Request $request, Consultation $consultation): View
    {
        $this->authorizeConsultationView($request, $consultation);
        $consultation->load(['patient', 'doctor', 'appointment', 'labTests', 'prescriptions.items.medicine']);

        return view('consultations.show', compact('consultation'));
    }

    public function edit(Request $request, Consultation $consultation): View
    {
        $this->authorizeDoctorForConsultation($request, $consultation);
        $consultation->load(['patient', 'appointment']);

        return view('consultations.edit', compact('consultation'));
    }

    public function update(Request $request, Consultation $consultation): RedirectResponse
    {
        $this->authorizeDoctorForConsultation($request, $consultation);
        $consultation->update($this->validateConsultation($request));

        return redirect()
            ->route('consultations.show', $consultation)
            ->with('success', 'Medical record updated successfully.');
    }

    private function validateConsultation(Request $request): array
    {
        return $request->validate([
            'complaint' => ['required', 'string', 'max:3000'],
            'diagnosis' => ['required', 'string', 'max:3000'],
            'treatment' => ['nullable', 'string', 'max:3000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function authorizeDoctorForAppointment(Request $request, Appointment $appointment): void
    {
        $user = $request->user();

        abort_unless($user->role === User::ROLE_DOCTOR && $appointment->doctor_id === $user->id, 403);
    }

    private function authorizeConsultationView(Request $request, Consultation $consultation): void
    {
        $user = $request->user();
        $allowed = $user->role === User::ROLE_ADMIN
            || ($user->role === User::ROLE_DOCTOR && $consultation->doctor_id === $user->id);

        abort_unless($allowed, 403);
    }

    private function authorizeDoctorForConsultation(Request $request, Consultation $consultation): void
    {
        $user = $request->user();

        abort_unless($user->role === User::ROLE_DOCTOR && $consultation->doctor_id === $user->id, 403);
    }
}
