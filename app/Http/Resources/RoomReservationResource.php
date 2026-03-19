<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomReservationResource extends JsonResource
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
            'reservation_id' => $this->reservation_id,
            'room_id'  => $this->room_id,
            'arrival_date' => $this->arrival_date ? Carbon::parse($this->arrival_date)->timezone('America/Sao_Paulo')->format('d-m-Y') : null,
            'departure_date' => $this->departure_date ? Carbon::parse($this->departure_date)->timezone('America/Sao_Paulo')->format('d-m-Y') : null,
            'currencycode' => $this->currencycode,
            'meal_plan' => $this->meal_plan,
            'guest_counts' => $this->guest_counts,
            'totalprice' => $this->totalprice,
        ];
    }
}
