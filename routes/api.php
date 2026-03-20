<?php

use App\Http\Controllers\{CustomerController, DailyPriceController, HotelController, ImportsController};
use App\Http\Controllers\{RateController, ReservationController, RoomController, RoomReservationController};
use Illuminate\Support\Facades\Route;

Route::apiResources([
    'hotels' => HotelController::class,
    'rooms' => RoomController::class,
    'rates' => RateController::class,
    'customers' => CustomerController::class,
    'reservations' => ReservationController::class,
    'room-reservations' => RoomReservationController::class,
    'daily-prices' => DailyPriceController::class,
]);

Route::get('start-import', [ImportsController::class, 'import']);
