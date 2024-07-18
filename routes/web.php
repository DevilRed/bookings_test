<?php

use Illuminate\Support\Facades\Route;
use App\Models\Employee;
use App\Models\Service;

Route::get('/', function () {
    $employee = Employee::find(1);
    $service = Service::find(1);
    $availability = (new \App\Bookings\ScheduleAvailability($employee, $service))
        ->forPeriod(
            now()->startOfDay(),
            now()->addMonth()->endOfDay()
        );
});
