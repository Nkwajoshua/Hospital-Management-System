<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PrescriptionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $status = trim((string) $request->query('status', ''));

        $prescriptions = Prescription::query()
            ->with(['patient', 'doctor', 'items.medicine'])
            ->when($user->role === User::ROLE_DOCTOR, fn (Builder $query) => $query->where('doctor_id', $user->id))
            ->when(in_array($status, ['pending', 'dispensed'], true), fn (Builder $query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('prescriptions.index', compact('prescriptions', 'status'));
    }

    public function create(Request $request, Consultation $consultation): View
    {
        $this->authorizeDoctor($request, $consultation);
        $consultation->load('patient');

        return view('prescriptions.create', [
            'consultation' => $consultation,
            'medicines' => Medicine::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, Consultation $consultation): RedirectResponse
    {
        $this->authorizeDoctor($request, $consultation);

        $validated = $request->validate([
            'medicine_id' => ['required', 'integer', 'exists:medicines,id'],
            'dosage' => ['required', 'string', 'max:255'],
            'frequency' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $prescription = DB::transaction(function () use ($request, $consultation, $validated) {
            $prescription = Prescription::create([
                'patient_id' => $consultation->patient_id,
                'consultation_id' => $consultation->id,
                'doctor_id' => $request->user()->id,
                'status' => 'pending',
            ]);

            $prescription->items()->create($validated);

            return $prescription;
        });

        return redirect()->route('prescriptions.show', $prescription)->with('success', 'Prescription created successfully.');
    }

    public function show(Request $request, Prescription $prescription): View
    {
        $this->authorizeView($request, $prescription);
        $prescription->load(['patient', 'doctor', 'consultation', 'items.medicine']);

        return view('prescriptions.show', compact('prescription'));
    }

    public function dispense(Request $request, Prescription $prescription): RedirectResponse
    {
        if ($prescription->status === 'dispensed') {
            return back()->with('error', 'This prescription has already been dispensed.');
        }

        $error = DB::transaction(function () use ($prescription) {
            $prescription->load('items');

            foreach ($prescription->items as $item) {
                $medicine = Medicine::query()->lockForUpdate()->findOrFail($item->medicine_id);

                if ($medicine->stock_quantity < $item->quantity) {
                    return "Insufficient stock for {$medicine->name}.";
                }
            }

            foreach ($prescription->items as $item) {
                Medicine::whereKey($item->medicine_id)->decrement('stock_quantity', $item->quantity);
            }

            $prescription->update(['status' => 'dispensed']);

            return null;
        });

        if ($error) {
            return back()->with('error', $error);
        }

        return back()->with('success', 'Prescription dispensed successfully.');
    }

    private function authorizeDoctor(Request $request, Consultation $consultation): void
    {
        abort_unless(
            $request->user()->role === User::ROLE_DOCTOR && $consultation->doctor_id === $request->user()->id,
            403
        );
    }

    private function authorizeView(Request $request, Prescription $prescription): void
    {
        $user = $request->user();
        $allowed = in_array($user->role, [User::ROLE_ADMIN, User::ROLE_PHARMACIST], true)
            || ($user->role === User::ROLE_DOCTOR && $prescription->doctor_id === $user->id);

        abort_unless($allowed, 403);
    }
}
