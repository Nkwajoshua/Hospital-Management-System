# Hospital Management System

A simple web-based Hospital Management System built as an undergraduate Computer Science final-year project.

## Project Goal

Build a functional MVP that demonstrates one complete patient workflow:

**Patient Registration → Appointment → Doctor Consultation → Laboratory / Prescription → Pharmacy → Billing → Simulated Payment**

The project intentionally focuses on a complete, demonstrable academic prototype rather than a production-grade hospital platform.

## Core Features

- Staff authentication and role-based access
- Administrator staff account management
- Patient registration, search and records
- Appointment booking and doctor assignment
- Doctor consultations and medical records
- Laboratory requests and results
- Medicine inventory, prescriptions and dispensing
- Patient billing
- Simulated payment success flow and printable receipt

## Staff Roles

- Administrator
- Receptionist
- Doctor
- Laboratory Staff
- Pharmacist

Patients do not require login accounts in the MVP.

## Technology Stack

- Laravel 13
- PHP 8.3+
- Blade templates
- Bootstrap 5
- MySQL
- PHPUnit / Laravel feature tests
- GitHub Actions CI

## Local Setup

### 1. Clone the repository

```bash
git clone https://github.com/Nkwajoshua/Hospital-Management-System.git
cd Hospital-Management-System
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Create the environment file

```bash
cp .env.example .env
php artisan key:generate
```

On Windows Command Prompt, use `copy .env.example .env` instead of `cp`.

### 4. Configure MySQL

Create a MySQL database, for example `hospital_management`, then set these values in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hospital_management
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Create an administrator

Set the following in `.env`:

```env
HMS_ADMIN_NAME="System Administrator"
HMS_ADMIN_EMAIL=admin@example.com
HMS_ADMIN_PASSWORD=change-this-password
```

Then run:

```bash
php artisan db:seed
```

### 7. Start the application

```bash
php artisan serve
```

Open the local URL shown by Laravel and sign in with the administrator account.

## Optional Defence Demo Data

To populate the system with realistic sample users, medicines, patients and hospital activity, run:

```bash
php artisan db:seed --class=DemoSeeder
```

The demo accounts are:

| Role | Email | Password |
| --- | --- | --- |
| Administrator | `admin@hms.test` | `password` |
| Receptionist | `reception@hms.test` | `password` |
| Doctor | `doctor@hms.test` | `password` |
| Laboratory Staff | `lab@hms.test` | `password` |
| Pharmacist | `pharmacy@hms.test` | `password` |

These accounts are for local demonstration only and must not be used as real credentials.

The demo dataset includes one completed patient journey and one scheduled patient appointment so the dashboards and modules are not empty during presentation.

## Simulated Payment

The payment module is intentionally simulated for the final-year prototype. The user selects a payment method and clicks the payment button, after which the system:

1. Creates a prototype payment record.
2. Generates an `HMS-PAY-...` reference.
3. Marks the bill as paid.
4. Displays a payment-success page.
5. Provides a printable receipt.

No payment gateway is contacted and no real money is transferred.

## Running Tests

```bash
php artisan test
```

The automated suite includes an end-to-end test for the complete MVP workflow from patient registration through simulated payment.

## MVP Completion Workflow

1. Administrator creates staff accounts.
2. Receptionist registers a patient.
3. Receptionist books an appointment with a doctor.
4. Doctor records a consultation.
5. Doctor requests a laboratory test and/or creates a prescription.
6. Laboratory staff records the result.
7. Pharmacist dispenses the medicine.
8. Receptionist generates the bill.
9. The payment is simulated successfully.
10. The system displays a receipt and retains the patient's records.

## Outside the MVP Scope

The following are deliberately excluded:

- Real payment gateway integration
- Insurance/HMO claims
- Payroll and HR management
- Telemedicine
- Ambulance tracking
- Blood bank management
- Surgery/theatre management
- Multi-hospital support
- Advanced accounting
- Complex inventory procurement
- AI diagnosis

These can be presented as possible future enhancements.
