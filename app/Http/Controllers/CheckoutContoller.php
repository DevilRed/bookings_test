<?php
namespace App\Http\Controllers;

use App\Bookings\ServiceSlotAvailability;
use App\Models\Employee;
use App\Models\Service;
use App\Bookings\Date;

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
        $availableDates = $availability
            ->hasSlots()
            ->mapWithKeys(
                fn(Date $date) => [ $date->date->toDateString() => $date->slots->count()
                ]
            )
            ->toArray()
        ;
        ;
        return view('bookings.checkout',
            compact('employee', 'service', 'firstAvailableDate', 'availableDates'));
    }
}
