<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'room_reservation_id' => $this->roomReservation->id,
            'rate_id'  => $this->rate->name,
            'date' => $this->date,
            'price'  => $this->price,
        ];
    }
}
