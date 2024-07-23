<?php
// list correct employee availability
// accounts for different daily schedule times
// does not show availability for schedule exclusions
// only shows availability from the current time with an hour in advanced

use App\Bookings\ScheduleAvailability;
use App\Models\Service;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Schedule;

it('list correct employee availability', function () {
    // For testing purposes set the date to a specific date
    Carbon::setTestNow(Carbon::parse('1st January 2000'));
    // create employee
    $employee = Employee::factory()
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->addYear()->endOfDay(),
        ]))
        ->create();

    // create service
    $service= Service::factory()
        ->create(['duration' => 40]);

    $availability = (new ScheduleAvailability($employee, $service))
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    expect($availability->current())
        // startsAt is a method on the period
        ->startsAt(now()->setTimeFromTimeString('09:00:00'))
        ->toBeTrue()
        ->endsAt(now()->setTimeFromTimeString('16:20:00'))
        ->toBeTrue();
});

// from 12. min 7.45
