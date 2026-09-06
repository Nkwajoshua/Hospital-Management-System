<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_dashboard(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
    }

    public function test_core_hms_tables_are_created(): void
    {
        foreach ([
            'users',
            'patients',
            'appointments',
            'consultations',
            'lab_tests',
            'medicines',
            'prescriptions',
            'prescription_items',
            'bills',
            'payments',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Expected table [{$table}] to exist.");
        }
    }
}
