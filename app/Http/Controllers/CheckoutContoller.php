<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Service;
use Illuminate\Http\Request;

class CheckoutContoller extends Controller
{
    public function __invoke(Employee  $employee, Service $service)
    {
        abort_unless($employee->services->contains($service), 404);
        return view('bookings.checkout', compact('employee', 'service'));
    }
}
