<?php
use Carbon\Carbon;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Appointment;
use App\Bookings\ServiceSlotAvailability;
use App\Bookings\Date;
use App\Bookings\Slot;
/*
shows available time slots for a service
list multiple slots over more than one day
excludes booked appointments for the employee
ignores cancelled appointments
shows multiple employees available for a service
*/

it('shows available time slots for a service', function () {
    Carbon::setTestNow(Carbon::parse('1st January 2000'));

    $employee = Employee::factory()
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
        ]))
        ->create();

    // create service
    $service= Service::factory()
        ->create(['duration' => 30]);

    $availability = (new \App\Bookings\ServiceSlotAvailability(collect([$employee]), $service))
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    expect($availability->first()->date->toDateString())->toEqual(now()->toDateString());
    expect($availability->first()->slots)->toHaveCount(16);
});

it('list multiple slots over more than one day', function () {
    Carbon::setTestNow(Carbon::parse('1st January 2000'));

    $employee = Employee::factory()
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfYear(),
        ]))
        ->create();

    // create service
    $service= Service::factory()
        ->create(['duration' => 30]);

    $availability = (new \App\Bookings\ServiceSlotAvailability(collect([$employee]), $service))
        ->forPeriod(now()->startOfDay(), now()->addDay()->endOfDay());

    // make sure response contains the requested 2 days:
    // now()->startOfDay(), now()->addDay()->endOfDay()
    expect($availability->map(fn ($date) => $date->date->toDateString()))
        ->toContain(
            now()->toDateString(),
            now()->addDay()->toDateString(),
        );
    expect($availability->first()->slots)->toHaveCount(16);
    expect($availability->get(1)->slots)->toHaveCount(16);
});

it('excludes booked appointments for the employee', function () {
    Carbon::setTestNow(Carbon::parse('1st January 2000'));

    // create service
    $service= Service::factory()
        ->create(['duration' => 30]);

    $employee = Employee::factory()
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
        ]))
        ->has(Appointment::factory()->for($service)->state([
            'starts_at' => now()->setTimeFromTimeString('12:00'),
            'end_at' => now()->setTimeFromTimeString('12:45')
        ]))
        ->create();



    $availability = (new ServiceSlotAvailability(collect([$employee]), $service))
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    // get a list of slots in the array
    // map through all the dates
    $slots = $availability->map(function (Date $date) {
        // for each return the slot to compare it's time
        return $date->slots->map(fn (Slot $slot) => $slot->time->toTimeString());
    })
        ->flatten()
        ->toArray();

    expect($slots)
        ->toContain('11:30:00')
        ->not->toContain('12:00:00')
        ->not->toContain('12:30:00')
        ->toContain('13:00:00');
});

it('ignores cancelled appointments', function () {
    Carbon::setTestNow(Carbon::parse('1st January 2000'));

    // create service
    $service= Service::factory()
        ->create(['duration' => 30]);

    $employee = Employee::factory()
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
        ]))
        ->has(Appointment::factory()->for($service)->state([
            'starts_at' => now()->setTimeFromTimeString('12:00'),
            'end_at' => now()->setTimeFromTimeString('12:45'),
            'cancelled_at' => now(),
        ]))
        ->create();



    $availability = (new ServiceSlotAvailability(collect([$employee]), $service))
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    // get a list of slots in the array
    // map through all the dates
    $slots = $availability->map(function (Date $date) {
        // for each return the slot to compare it's time
        return $date->slots->map(fn (Slot $slot) => $slot->time->toTimeString());
    })
        ->flatten()
        ->toArray();
    expect($slots)
        ->toContain('11:30:00')
        // slots should include already cancelled appointments
        ->toContain('12:00:00')
        ->toContain('12:30:00')
        ->toContain('13:00:00');
});

it('shows multiple employees available for a service', function () {
    Carbon::setTestNow(Carbon::parse('1st January 2000'));

    // create service
    $service= Service::factory()
        ->create(['duration' => 30]);

    $employees = Employee::factory()
        ->count(2)
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
        ]))
        ->create();



    $availability = (new ServiceSlotAvailability($employees, $service))
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    expect($availability->first()->slots->first()->employees)->toHaveCount(2);
});
