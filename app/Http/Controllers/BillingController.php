<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BillingController extends Controller
{
    private const CONSULTATION_FEE = 5000.00;
    private const LAB_TEST_FEE = 2000.00;

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));

        $bills = Bill::query()
            ->with(['patient', 'payment'])
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->whereHas('patient', function (Builder $patientQuery) use ($search) {
                    $patientQuery->where('patient_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['unpaid', 'paid'], true), fn (Builder $query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('billing.index', compact('bills', 'search', 'status'));
    }

    public function store(Patient $patient): RedirectResponse
    {
        $consultation = $patient->consultations()
            ->with(['labTests', 'prescriptions.items.medicine'])
            ->latest()
            ->first();

        if (! $consultation) {
            return back()->with('error', 'A consultation must be completed before a bill can be generated.');
        }

        $existing = Bill::where('consultation_id', $consultation->id)->first();

        if ($existing) {
            return redirect()->route('billing.show', $existing)
                ->with('success', 'A bill already exists for the latest consultation.');
        }

        $details = [[
            'label' => 'Consultation',
            'quantity' => 1,
            'unit_price' => self::CONSULTATION_FEE,
            'amount' => self::CONSULTATION_FEE,
        ]];

        foreach ($consultation->labTests as $labTest) {
            $details[] = [
                'label' => 'Laboratory: '.$labTest->test_name,
                'quantity' => 1,
                'unit_price' => self::LAB_TEST_FEE,
                'amount' => self::LAB_TEST_FEE,
            ];
        }

        foreach ($consultation->prescriptions as $prescription) {
            foreach ($prescription->items as $item) {
                $unitPrice = (float) $item->medicine->unit_price;
                $amount = $unitPrice * $item->quantity;

                $details[] = [
                    'label' => 'Medicine: '.$item->medicine->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                ];
            }
        }

        $total = array_sum(array_column($details, 'amount'));

        $bill = DB::transaction(function () use ($patient, $consultation, $details, $total) {
            $bill = Bill::create([
                'patient_id' => $patient->id,
                'consultation_id' => $consultation->id,
                'amount' => $total,
                'status' => 'unpaid',
                'details' => $details,
            ]);

            $bill->update(['reference' => sprintf('HMS-BILL-%06d', $bill->id)]);

            return $bill;
        });

        return redirect()->route('billing.show', $bill)->with('success', 'Bill generated successfully.');
    }

    public function show(Bill $bill): View
    {
        $bill->load(['patient', 'consultation.doctor', 'payment']);

        return view('billing.show', compact('bill'));
    }

    public function checkout(Bill $bill): View|RedirectResponse
    {
        $bill->load(['patient', 'payment']);

        if ($bill->status === 'paid') {
            return redirect()->route('billing.success', $bill);
        }

        return view('billing.checkout', compact('bill'));
    }

    public function pay(Request $request, Bill $bill): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:cash,card,bank_transfer'],
        ]);

        DB::transaction(function () use ($bill, $validated) {
            $lockedBill = Bill::query()->lockForUpdate()->findOrFail($bill->id);

            if ($lockedBill->status === 'paid') {
                return;
            }

            $payment = Payment::create([
                'bill_id' => $lockedBill->id,
                'amount' => $lockedBill->amount,
                'payment_method' => $validated['payment_method'],
                'payment_date' => now(),
            ]);

            $payment->update(['reference' => sprintf('HMS-PAY-%06d', $payment->id)]);
            $lockedBill->update(['status' => 'paid']);
        });

        return redirect()->route('billing.success', $bill)->with('success', 'Payment completed successfully.');
    }

    public function success(Bill $bill): View|RedirectResponse
    {
        $bill->load(['patient', 'payment']);

        if ($bill->status !== 'paid' || ! $bill->payment) {
            return redirect()->route('billing.checkout', $bill);
        }

        return view('billing.success', compact('bill'));
    }

    public function receipt(Bill $bill): View|RedirectResponse
    {
        $bill->load(['patient', 'payment']);

        if ($bill->status !== 'paid' || ! $bill->payment) {
            return redirect()->route('billing.checkout', $bill);
        }

        return view('billing.receipt', compact('bill'));
    }
}
