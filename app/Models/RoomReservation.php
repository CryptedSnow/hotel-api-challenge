<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomReservation extends Model
{
    use HasFactory;
    protected $table = 'room_reservations';

    protected $fillable = [
        'id',
        'reservation_id',
        'room_id',
        'arrival_date',
        'departure_date',
        'currencycode',
        'meal_plan',
        'guest_counts',
        'totalprice',
    ];

    protected $casts = [
        'guest_counts' => 'json',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function dailyPrices()
    {
        return $this->hasMany(DailyPrice::class);
    }
}
