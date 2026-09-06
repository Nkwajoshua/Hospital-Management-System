<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $status = trim((string) $request->query('status', ''));
        $date = trim((string) $request->query('date', ''));

        $appointments = Appointment::query()
            ->with(['patient', 'doctor'])
            ->when($user->role === User::ROLE_DOCTOR, fn (Builder $query) => $query->where('doctor_id', $user->id))
            ->when(in_array($status, ['scheduled', 'completed', 'cancelled'], true), fn (Builder $query) => $query->where('status', $status))
            ->when($date !== '', fn (Builder $query) => $query->whereDate('appointment_date', $date))
            ->orderBy('appointment_date')
            ->paginate(12)
            ->withQueryString();

        return view('appointments.index', compact('appointments', 'status', 'date'));
    }

    public function create(Request $request): View
    {
        $patients = Patient::orderBy('first_name')->orderBy('last_name')->get();
        $doctors = $this->activeDoctors();
        $selectedPatientId = $request->integer('patient') ?: null;

        return view('appointments.create', compact('patients', 'doctors', 'selectedPatientId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $appointment = Appointment::create($this->validateAppointment($request));

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment booked successfully.');
    }

    public function edit(Appointment $appointment): View
    {
        $patients = Patient::orderBy('first_name')->orderBy('last_name')->get();
        $doctors = $this->activeDoctors();

        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $appointment->update($this->validateAppointment($request, true));

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        if ($appointment->status === 'completed') {
            return back()->with('error', 'A completed appointment cannot be cancelled.');
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled.');
    }

    private function validateAppointment(Request $request, bool $editing = false): array
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('role', User::ROLE_DOCTOR)
                    ->where('status', 'active')),
            ],
            'appointment_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'status' => $editing
                ? ['required', Rule::in(['scheduled', 'cancelled'])]
                : ['nullable', Rule::in(['scheduled'])],
        ]);

        $validated['status'] ??= 'scheduled';

        return $validated;
    }

    private function activeDoctors()
    {
        return User::query()
            ->where('role', User::ROLE_DOCTOR)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }
}
