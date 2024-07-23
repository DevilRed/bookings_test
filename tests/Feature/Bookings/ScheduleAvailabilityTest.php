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

    // ->current() get the first item in the collection
    expect($availability->current())
        // startsAt is a method on the period
        ->startsAt(now()->setTimeFromTimeString('09:00:00'))
        ->toBeTrue()
        ->endsAt(now()->setTimeFromTimeString('16:20:00'))
        ->toBeTrue();
});

it('accounts for different daily schedule times', function () {
    // For testing purposes set the date to a specific date
    Carbon::setTestNow(Carbon::parse('Monday January 2000'));
    // create employee
    $employee = Employee::factory()
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->addYear()->endOfDay(),
            'monday_starts_at' => '11:00:00',
            'monday_ends_at' => '16:00:00',
            'tuesday_starts_at' => '09:00:00',
            'tuesday_ends_at' => '17:00:00',
        ]))
        ->create();

    // create service
    $service= Service::factory()
        ->create(['duration' => 30]);

    $availability = (new ScheduleAvailability($employee, $service))
        ->forPeriod(now()->startOfDay(), now()->addDay()->endOfDay());

    expect($availability->current())
        ->startsAt(now()->setTimeFromTimeString('11:00:00'))
        ->toBeTrue()
        ->endsAt(now()->setTimeFromTimeString('15:30:00'))
        ->toBeTrue();

    // go to the next day
    $availability->next();

    expect($availability->current())
        ->startsAt(now()->addDay()->setTimeFromTimeString('09:00:00'))
        ->toBeTrue()
        ->endsAt(now()->addDay()->setTimeFromTimeString('16:30:00'))
        ->toBeTrue();
});

// from 12. min 8.09
