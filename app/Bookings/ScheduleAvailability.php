<?php
namespace App\Bookings;

use App\Models\Employee;
use App\Models\ScheduleExclusion;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Spatie\Period\Boundaries;
use Spatie\Period\Period;
use Spatie\Period\PeriodCollection;
use Spatie\Period\Precision;

class ScheduleAvailability
{
    protected PeriodCollection $periods;
    public function __construct(protected Employee $employee, protected Service $service)
    {
        // custom period collection
        $this->periods = new PeriodCollection();
    }

    /**
     * Get the available period for given time, all operations are working over periods property
     * @param Carbon $startsAt
     * @param Carbon $endsAt
     * @return PeriodCollection
     */
    public function forPeriod(Carbon $startsAt, Carbon $endsAt)
    {
        // use collect to iterate over the days of a period to check if an employee is available on that day
        collect(CarbonPeriod::create($startsAt, $endsAt)->days())
            ->each(function ($date) {
                $this->addAvailabilityFromSchedule($date);
                $this->employee->schedulesExclusions()->each(function (ScheduleExclusion $exclusion) {
                    $this->subtractScheduleExclusion($exclusion);
                });

                $this->excludeTimePassedToday();
            });
        return $this->periods;
    }

    /**
     * Check if an employee can work on given days
     * If so, set availability over periods property
     * @param Carbon $date
     * @return void
     */
    protected function addAvailabilityFromSchedule(Carbon $date): void
    {
        // if there is no schedule for the $date just return
        if (!$schedule = $this->employee->schedules()
            ->where('starts_at', '<=', $date)
            ->where('ends_at', '>=', $date)->first()) {
            return;
        }
        // schedule is a collection,
        // check for empty results
        if(![$startsAt, $endsAt] = $schedule->getWorkingHoursForDate($date)) {
            return;
        }
        // add a valid period
        $this->periods = $this->periods->add(
            Period::make(
                // use copy() to not mutate the value of the current date
                $date->copy()->setTimeFromTimeString($startsAt),
                // take into account the service duration time to avoid inconsistencies and give wrong time periods
                $date->copy()->setTimeFromTimeString($endsAt)->subMinutes($this->service->duration),
                Precision::MINUTE()
            )
        );
    }

    /**
     * Subtract the service duration
     * @param ScheduleExclusion $exclusion
     * @return void
     */
    protected function subtractScheduleExclusion(ScheduleExclusion $exclusion): void
    {
        $this->periods = $this->periods->subtract(
            Period::make(
                $exclusion->starts_at,
                $exclusion->ends_at,
                Precision::MINUTE(),
                Boundaries::EXCLUDE_END()
            )
        );
    }

    /**
     * Exclude time already passed from current day
     * @return void
     */
    protected function excludeTimePassedToday(): void
    {
        $this->periods = $this->periods->subtract(
            Period::make(
                now()->startOfDay(),
                now()->endOfHour(),
                Precision::MINUTE(),
                Boundaries::EXCLUDE_START()
            )
        );
    }
}
