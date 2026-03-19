<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;
    protected $table = 'rooms';

    protected $fillable = [
        'id',
        'hotel_id',
        'name',
        'inventory_count'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

}
