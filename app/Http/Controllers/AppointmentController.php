<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __invoke(Request $request)
    {
        \Log::info($request->only('employee','service', 'date', 'time', 'name', 'email'));
    }
}
