<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'hotel_id' => $this->hotel_id,
            'customer_id' => $this->customer_id,
            'date' => $this->date ? Carbon::parse($this->date)->timezone('America/Sao_Paulo')->format('d-m-Y') : null,
            'time' => $this->time ? Carbon::parse($this->time)->timezone('America/Sao_Paulo')->format('H:i:s') : null,
        ];
    }
}
