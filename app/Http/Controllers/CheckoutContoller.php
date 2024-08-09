<?php
namespace App\Http\Controllers;

use App\Bookings\ServiceSlotAvailability;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Http\Request;

class CheckoutContoller extends Controller
{
    public function __invoke(Employee  $employee, Service $service)
    {
        abort_unless($employee->services->contains($service), 404);

        $availability = (new ServiceSlotAvailability(collect([$employee]), $service))
            ->forPeriod(
                now()->startOfDay(),
                now()->addMonth()->endOfDay(),
            );
        $firstAvailableDate = $availability->firstAvailableDate()->date->toDateString();
        return view('bookings.checkout',
            compact('employee', 'service', 'firstAvailableDate'));
    }
}
