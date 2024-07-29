<?php

namespace App\Bookings;

use Carbon\Carbon;

/**
 * Class to hold what employees can fulfill this slot
 */
class Slot
{
    public $employees;
    public function __construct(public Carbon $time)
    {
    }
}
