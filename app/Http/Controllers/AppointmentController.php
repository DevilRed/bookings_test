<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function __invoke(AppointmentRequest $request)
    {
        try {
            $appointmentData = $request->only('employee_id', 'service_id', 'starts_at', 'end_at', 'name', 'email');

            // Log the specific data being used to create the appointment
            \Log::info('Appointment data:', $appointmentData);

            Appointment::create($appointmentData);
        } catch (\Exception $e) {
            \Log::error('Appointment creation error: ' . $e->getMessage());
            dd($e);
        }

    }
}
