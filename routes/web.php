<?php

use Illuminate\Support\Facades\Route;
use App\Models\Employee;

Route::get('/', function () {
    $availability = (new \App\Bookings\ScheduleAvailability())
        ->forPeriod(
            now()->startOfDay(),
            now()->addMonth()->endOfDay()
        );
});
