<?php

use App\Http\Controllers\{CustomerController, DailyPriceController, HotelController, ImportsController};
use App\Http\Controllers\{RateController, ReservationController, RoomController, RoomReservationController};
use Illuminate\Support\Facades\Route;

Route::apiResources([
    'hotel' => HotelController::class,
    'room' => RoomController::class,
    'rate' => RateController::class,
    'customer' => CustomerController::class,
    'reservation' => ReservationController::class,
    'room-reservation' => RoomReservationController::class,
    'daily-price' => DailyPriceController::class,
]);

Route::get('start-import', [ImportsController::class, 'import']);
