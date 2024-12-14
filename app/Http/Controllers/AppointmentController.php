<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function __invoke(AppointmentRequest $request)
    {
        \Log::info(
            $request->only('employee_id', 'service_id', 'starts_at', 'ends_at', 'name', 'email')
        );
        /*Appointment::create(
            $request->only('employee_id', 'service_id', 'starts_at', 'ends_at', 'name', 'email')
        );*/
    }
}
