<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Service;
use Illuminate\Http\Request;

class BookingEmployeeController extends Controller
{
    public function __invoke(Employee  $employee)
    {
        return view('bookings.employee', [
            'employee' => $employee,
            'services' => $employee->services()->orderBy('price', 'asc')->get(),
        ]);
    }
}
