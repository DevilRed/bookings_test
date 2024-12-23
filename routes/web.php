<?php

use App\Http\Controllers\ConfirmationController;
use Illuminate\Support\Facades\Route;
use App\Models\Employee;
use App\Models\Service;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingEmployeeController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentDestroyController;

// fake date for all app to help testing
\Carbon\Carbon::setTestNow(now()->addDay()->setTimeFromTimeString('09:00:00'));

Route::get('/', BookingController::class)->name('bookings');
Route::get('/bookings/{employee:slug}', BookingEmployeeController::class)->name('bookings.employee');
Route::get('/checkout/{employee:slug}/{service:slug}', CheckoutController::class)->name('checkout');
Route::get('/slots/{employee:slug}/{service:slug}', SlotController::class)->name('slots');
Route::post('/appointments',AppointmentController::class)->name('appointments');
Route::get('/confirmation/{appointment:uuid}', ConfirmationController::class)->name('confirmation');
Route::delete('/appointment/{appointment}', AppointmentDestroyController::class)->name('appointments.destroy');

