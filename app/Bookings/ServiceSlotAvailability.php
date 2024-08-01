<?php

namespace App\Bookings;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\Period\Boundaries;
use Spatie\Period\Period;
use App\Bookings\SlotRangeGenerator;
use Spatie\Period\Precision;
use Spatie\Period\PeriodCollection;

class ServiceSlotAvailability
{

    /**
     * hold what employees can fulfill the availability
     * @param Collection $employees
     * @param Service $service
     */
    public function __construct(protected Collection $employees, protected Service $service)
    {
    }

    public function forPeriod(Carbon $startsAt, Carbon $endsAt)
    {
        $range = (new SlotRangeGenerator($startsAt, $endsAt))->generate($this->service->duration);

        $this->employees->each(function (Employee $employee) use ($startsAt, $endsAt, &$range) {
            // get the availability for the employee
            $periods = (new ScheduleAvailability($employee, $this->service))
                ->forPeriod($startsAt, $endsAt);
            // remove already booked appointments from availability list
            $periods = $this->removeAppointments($periods, $employee);
            foreach ($periods as $period) {
                $this->addAvailableEmployeeForPeriod($range, $period, $employee);
            }
            // remove appointments from the period collection
            // add the available employees to the range
        });
        // remove empty slots
        $range = $this->removeEmptySlots($range);

        return $range;
    }

    public function removeAppointments(PeriodCollection $period, Employee $employee)
    {
        $employee->appointments->whereNull('cancelled_at')->each(function (Appointment $appointment) use (&$period) {
            $period = $period->subtract(
                Period::make(
                // take into account the length of the appointment
                    $appointment->starts_at->copy()->subMinutes($this->service->duration)->addMinute(),
                    $appointment->end_at,
                    Precision::MINUTE(),
                    Boundaries::EXCLUDE_ALL()
                )
            );
        });
        return $period;
    }

    public function removeEmptySlots(Collection $range): Collection
    {
        return $range->filter(function (Date $date) {
            // filter slots with available employees
            $date->slots = $date->slots->filter(function (Slot $slot) {
                return $slot->hasEmployees();
            });
            return true;
        });
    }


    protected function addAvailableEmployeeForPeriod(Collection $range, Period $period, Employee $employee)
    {
        $range->each(function (Date $date) use($period, $employee) {
            // iterate over each slot of date and see if an employee can fulfill that slot based on period
            $date->slots->each(function (Slot $slot) use ($period, $employee) {
                // period contains the slot time
                if ($period->contains(($slot->time))) {
                    $slot->addEmployee($employee);
                }
            });
        });
    }
}
