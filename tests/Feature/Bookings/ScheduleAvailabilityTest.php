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
use App\Models\ScheduleExclusion;

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

    // go to the next period
    $availability->next();

    expect($availability->current())
        ->startsAt(now()->addDay()->setTimeFromTimeString('09:00:00'))
        ->toBeTrue()
        ->endsAt(now()->addDay()->setTimeFromTimeString('16:30:00'))
        ->toBeTrue();
});

it('does not show availability for schedule exclusions', function () {
    // For testing purposes set the date to a specific date
    Carbon::setTestNow(Carbon::parse('1st January 2000'));
    // create employee
    $employee = Employee::factory()
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->addYear()->endOfDay(),
        ]))
        //add schedule exclusion for entire day tomorrow
        ->has(ScheduleExclusion::factory()->state([
            'starts_at' => now()->addDay()->startOfDay(),
            'ends_at' => now()->addDay()->endOfDay(),
        ]), 'schedulesExclusions')// pass the method name of the relationship
        // add schedule exclusion for lunch
        ->has(ScheduleExclusion::factory()->state([
            'starts_at' => now()->setTimeFromTimeString('12:00:00'),
            'ends_at' => now()->setTimeFromTimeString('13:00:00'),
        ]), 'schedulesExclusions')
        ->create();

    // create service
    $service= Service::factory()
        ->create(['duration' => 30]);

    $availability = (new ScheduleAvailability($employee, $service))
        ->forPeriod(now()->startOfDay(), now()->addDay()->endOfDay());

    expect($availability->current())
        ->startsAt(now()->setTimeFromTimeString('09:00:00'))
        ->toBeTrue()
        // the exclusion start at 12:00:00  so we have 11:59:00 available
        ->endsAt(now()->setTimeFromTimeString('11:59:00'))
        ->toBeTrue();

    // even the next available period is in the same day we need to go to the next period to work on that
    $availability->next();
    expect($availability->current())
        ->startsAt(now()->setTimeFromTimeString('13:00:00'))
        ->toBeTrue()
        // the exclusion start at 12:00:00  so we have 11:59:00 available
        ->endsAt(now()->setTimeFromTimeString('16:30:00'))
        ->toBeTrue();

    $availability->next();

    // since tomorrow is entire day off, the period does not exist
    expect($availability->valid())->toBeFalse();
});

it('only shows availability from the current time with an hour in advanced', function () {
    // For testing purposes set the date to a specific date
    // the date is important for the test in place
    Carbon::setTestNow(Carbon::parse('1st January 2000 09:15:00'));
    // next availability should 10:00

    // create employee
    $employee = Employee::factory()
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->addYear()->endOfDay(),
        ]))
        ->create();

    // create service
    $service= Service::factory()
        ->create(['duration' => 30]);

    $availability = (new ScheduleAvailability($employee, $service))
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    expect($availability->current())
        ->startsAt(now()->setTimeFromTimeString('10:00:00'))
        ->toBeTrue();
});

// from 12. min 15.23
