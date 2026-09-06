<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('hms:about', function () {
    $this->info('Hospital Management System MVP');
    $this->line('Final-year Computer Science project.');
})->purpose('Display basic HMS project information');
