<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(): View
    {
        $staff = User::orderBy('name')->paginate(15);

        return view('staff.index', compact('staff'));
    }

    public function create(): View
    {
        return view('staff.create', ['roles' => $this->roles()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateStaff($request);
        User::create($validated);

        return redirect()->route('staff.index')->with('success', 'Staff account created successfully.');
    }

    public function edit(User $staff): View
    {
        return view('staff.edit', [
            'staffMember' => $staff,
            'roles' => $this->roles(),
        ]);
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        $validated = $this->validateStaff($request, $staff);

        if ($staff->id === $request->user()->id && ($validated['status'] ?? 'active') !== 'active') {
            return back()->withInput()->with('error', 'You cannot deactivate your own administrator account.');
        }

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Staff account updated successfully.');
    }

    private function validateStaff(Request $request, ?User $staff = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($staff?->id),
            ],
            'password' => [$staff ? 'nullable' : 'required', 'string', 'min:8'],
            'role' => ['required', Rule::in(array_keys($this->roles()))],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }

    private function roles(): array
    {
        return [
            User::ROLE_ADMIN => 'Administrator',
            User::ROLE_RECEPTIONIST => 'Receptionist',
            User::ROLE_DOCTOR => 'Doctor',
            User::ROLE_LAB_STAFF => 'Laboratory Staff',
            User::ROLE_PHARMACIST => 'Pharmacist',
        ];
    }
}
