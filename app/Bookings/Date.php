<?php

namespace App\Bookings;

use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * Class to hold information about slots
 */
class Date
{
    public Collection $slots;

    public function __construct(public Carbon $date)
    {
        $this->slots = new Collection();
    }

    public function addSlot(Slot $slot)
    {
        $this->slots->push($slot);
    }
    public function containsSlot (string $time)
    {
        return $this->slots->search(function (Slot $slot) use ($time) {
            return $slot->time->format('H:i') === $time;
        });
    }
}
