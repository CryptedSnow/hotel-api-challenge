<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rate extends Model
{
    use HasFactory;
    protected $table = 'rates';

    protected $fillable = [
        'id',
        'hotel_id',
        'name',
        'active',
        'price'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
