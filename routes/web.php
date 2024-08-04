<?php

use Illuminate\Support\Facades\Route;
use App\Models\Employee;
use App\Models\Service;
use App\Http\Controllers\BookingController;

Route::get('/', BookingController::class)->name('booking');
