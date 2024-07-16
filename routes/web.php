<?php

use Illuminate\Support\Facades\Route;
use App\Models\Employee;

Route::get('/', function () {
    $employee = Employee::find(2);

    dd($employee->services);
    return view('welcome');
});
