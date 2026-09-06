<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabTestController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    });

    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');

    Route::middleware('role:admin,receptionist')->group(function () {
        Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    });

    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->middleware('role:admin')->name('patients.destroy');

    Route::get('/appointments', [AppointmentController::class, 'index'])->middleware('role:admin,receptionist,doctor')->name('appointments.index');

    Route::middleware('role:admin,receptionist')->group(function () {
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
        Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    });

    Route::middleware('role:admin,doctor')->group(function () {
        Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultations.index');
        Route::get('/consultations/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');
    });

    Route::middleware('role:doctor')->group(function () {
        Route::get('/appointments/{appointment}/consultation', [ConsultationController::class, 'create'])->name('consultations.create');
        Route::post('/appointments/{appointment}/consultation', [ConsultationController::class, 'store'])->name('consultations.store');
        Route::get('/consultations/{consultation}/edit', [ConsultationController::class, 'edit'])->name('consultations.edit');
        Route::put('/consultations/{consultation}', [ConsultationController::class, 'update'])->name('consultations.update');
        Route::post('/consultations/{consultation}/lab-tests', [LabTestController::class, 'store'])->name('lab-tests.store');
        Route::get('/consultations/{consultation}/prescriptions/create', [PrescriptionController::class, 'create'])->name('prescriptions.create');
        Route::post('/consultations/{consultation}/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
    });

    Route::get('/lab-tests', [LabTestController::class, 'index'])->middleware('role:admin,doctor,lab_staff')->name('lab-tests.index');
    Route::middleware('role:lab_staff')->group(function () {
        Route::get('/lab-tests/{labTest}/edit', [LabTestController::class, 'edit'])->name('lab-tests.edit');
        Route::put('/lab-tests/{labTest}', [LabTestController::class, 'update'])->name('lab-tests.update');
    });

    Route::middleware('role:admin,pharmacist')->group(function () {
        Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
        Route::get('/medicines/create', [MedicineController::class, 'create'])->name('medicines.create');
        Route::post('/medicines', [MedicineController::class, 'store'])->name('medicines.store');
        Route::get('/medicines/{medicine}/edit', [MedicineController::class, 'edit'])->name('medicines.edit');
        Route::put('/medicines/{medicine}', [MedicineController::class, 'update'])->name('medicines.update');
    });

    Route::get('/prescriptions', [PrescriptionController::class, 'index'])->middleware('role:admin,doctor,pharmacist')->name('prescriptions.index');
    Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show'])->middleware('role:admin,doctor,pharmacist')->name('prescriptions.show');
    Route::patch('/prescriptions/{prescription}/dispense', [PrescriptionController::class, 'dispense'])->middleware('role:pharmacist')->name('prescriptions.dispense');

    Route::middleware('role:admin,receptionist')->group(function () {
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
        Route::post('/patients/{patient}/billing', [BillingController::class, 'store'])->name('billing.store');
        Route::get('/billing/{bill}', [BillingController::class, 'show'])->name('billing.show');
        Route::get('/billing/{bill}/checkout', [BillingController::class, 'checkout'])->name('billing.checkout');
        Route::post('/billing/{bill}/pay', [BillingController::class, 'pay'])->name('billing.pay');
        Route::get('/billing/{bill}/success', [BillingController::class, 'success'])->name('billing.success');
        Route::get('/billing/{bill}/receipt', [BillingController::class, 'receipt'])->name('billing.receipt');
    });
});
