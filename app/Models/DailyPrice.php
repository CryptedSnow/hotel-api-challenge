<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPrice extends Model
{
    use HasFactory;
    protected $table = 'daily_prices';
    protected $primaryKey = 'id';

    protected $fillable = [
        'room_reservation_id',
        'rate_id',
        'date',
        'price',
    ];

    public function roomReservation()
    {
        return $this->belongsTo(RoomReservation::class);
    }

    public function rate()
    {
        return $this->belongsTo(Rate::class);
    }
}
