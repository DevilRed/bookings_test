<?php

namespace App\Bookings;

use App\Models\Employee;
use Carbon\Carbon;

/**
 * Class to hold what employees can fulfill this slot
 */
class Slot
{
    public $employees = [];
    public function __construct(public Carbon $time)
    {
    }

    public function addEmployee(Employee $employee)
    {
        $this->employees[] = $employee;
    }
}
