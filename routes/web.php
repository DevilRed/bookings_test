<?php

use Illuminate\Support\Facades\Route;
use App\Models\Employee;
use App\Models\Service;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingEmployeeController;

Route::get('/', BookingController::class)->name('bookings');
Route::get('/bookings/{employee:slug}', BookingEmployeeController::class)->name('bookings.employee');
