<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Service;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function __invoke(AppointmentRequest $request)
    {
        try {
            $service = Service::find($request->service_id);

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
