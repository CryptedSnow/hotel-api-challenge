<?php

namespace App\Services;

use App\Models\RoomReservation;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ReservationAvailableService
{
    public function checkAvailableRoom(
        int $room_id,
        Carbon $arrival,
        Carbon $departure,
        ?int $exclude_id = null
    ): void {
        $query = RoomReservation::query()
            ->where('room_id', $room_id)
            ->where(function ($q) use ($arrival, $departure) {
                // Caso 1: nova chegada durante reserva existente
                $q->whereBetween('arrival_date', [$arrival, $departure])
                  // Caso 2: nova saída durante reserva existente
                  ->orWhereBetween('departure_date', [$arrival, $departure])
                  // Caso 3: reserva existente completamente dentro do novo período
                  ->orWhere(function ($sub) use ($arrival, $departure) {
                      $sub->where('arrival_date', '<=', $arrival)
                          ->where('departure_date', '>=', $departure);
                  });
            });

        if ($exclude_id !== null) {
            $query->where('id', '!=', $exclude_id);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'period' => 'O quarto já está reservado para o período solicitado.',
            ]);
        }
    }
}
