<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\LabTest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LabTestController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $status = trim((string) $request->query('status', ''));

        $tests = LabTest::query()
            ->with(['patient', 'requester'])
            ->when($user->role === User::ROLE_DOCTOR, fn (Builder $query) => $query->where('requested_by', $user->id))
            ->when(in_array($status, ['pending', 'completed'], true), fn (Builder $query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('lab-tests.index', compact('tests', 'status'));
    }

    public function store(Request $request, Consultation $consultation): RedirectResponse
    {
        abort_unless(
            $request->user()->role === User::ROLE_DOCTOR && $consultation->doctor_id === $request->user()->id,
            403
        );

        $validated = $request->validate([
            'test_name' => ['required', 'string', 'max:255'],
        ]);

        LabTest::create([
            'patient_id' => $consultation->patient_id,
            'consultation_id' => $consultation->id,
            'requested_by' => $request->user()->id,
            'test_name' => $validated['test_name'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Laboratory test requested successfully.');
    }

    public function edit(LabTest $labTest): View
    {
        $labTest->load(['patient', 'requester']);

        return view('lab-tests.edit', compact('labTest'));
    }

    public function update(Request $request, LabTest $labTest): RedirectResponse
    {
        $validated = $request->validate([
            'result' => ['required', 'string', 'max:10000'],
        ]);

        $labTest->update([
            'result' => $validated['result'],
            'status' => 'completed',
        ]);

        return redirect()->route('lab-tests.index')->with('success', 'Laboratory result recorded successfully.');
    }
}
