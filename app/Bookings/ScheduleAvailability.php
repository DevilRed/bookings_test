<?php
namespace App\Bookings;

use Carbon\Carbon;
use Spatie\Period\Boundaries;
use Spatie\Period\Period;
use Spatie\Period\PeriodCollection;
use Spatie\Period\Precision;

class ScheduleAvailability
{
    protected PeriodCollection $periods;
    public function __construct()
    {
        // custom period collection
        $this->periods = new PeriodCollection();
    }

    public function forPeriod()
    {
        // add code to get idea how periods are working
        // add some availability
        $this->periods = $this->periods->add(
            Period::make(
                now()->startOfDay(),
                now()->addDay()->endOfDay(),
                Precision::MINUTE(),
                Boundaries::EXCLUDE_ALL()
            )
        );
        // subtract some availability
        $this->periods = $this->periods->subtract(
            Period::make(
                Carbon::createFromTimeString('12:00'),
                Carbon::createFromTimeString('12:30'),
                Precision::MINUTE(),
                Boundaries::EXCLUDE_END()
            )
        );
        dd($this->periods);
    }
}