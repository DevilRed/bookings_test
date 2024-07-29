<?php

namespace App\Bookings;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SlotRangeGenerator
{

    public function __construct(protected Carbon $startsAt, protected Carbon $endsAt)
    {
        //
    }

    public function generate(int $interval)
    {
        $collection = collect();
        // get days for period with 1 day interval
        $days = CarbonPeriod::create($this->startsAt, '1 day', $this->endsAt);
        foreach ($days as $day) {
            $date = new Date($day);
            // get list of times in minutes for entire day
            $times = CarbonPeriod::create($day->startOfDay(), sprintf('%d minutes', $interval), $day->copy()->endOfDay());
            foreach ($times as $time) {
                $date->addSlot(new Slot($time));
            }
            $collection->push($date);
            // from 14
        }
        return $collection;
    }
}
