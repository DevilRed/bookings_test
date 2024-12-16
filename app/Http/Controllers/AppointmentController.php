<?php

namespace App\Http\Controllers;

use App\Bookings\ServiceSlotAvailability;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function __invoke(AppointmentRequest $request)
    {
        try {
            $service = Service::find($request->service_id);
            $employee = Employee::find($request->employee_id);

            $availability = (new ServiceSlotAvailability(collect([$employee]), $service))
                ->forPeriod(
                    Carbon::parse($request->date)->startOfDay(),
                    Carbon::parse($request->date)->endOfDay(),
                );

            if(!$availability->first()->containsSlot($request->time)) {
                return response()->json([
                    'error' => 'That slot was taken while you were making your booking, please try again'
                ], 409);
            }

            $appointmentData = $request->only('employee_id', 'service_id', 'name', 'email') + [
                    'starts_at' => $date = Carbon::parse($request->date)->setTimeFromTimeString($request->time),
                    // add service duration on start date
                    'end_at' => $date->copy()->addMinutes($service->duration),
                ]
            ;

            Appointment::create($appointmentData);
        } catch (\Exception $e) {
            \Log::error('Appointment creation error: ' . $e->getMessage());
            dd($e);
        }

    }
}
