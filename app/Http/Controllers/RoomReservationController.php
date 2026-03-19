<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomReservationResource;
use App\Models\RoomReservation;
use App\Services\ReservationAvailableService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class RoomReservationController extends Controller
{
    public function index()
    {
        $room_reservation = RoomReservation::orderBy('id')->get();
        if ($room_reservation->isEmpty()) {
            return response()->json(['message' => 'No room reservations found.'], 404);
        }
        return RoomReservationResource::collection($room_reservation);
    }

    public function store(Request $request)
    {
        try {
            $validations = $request->validate([
                'reservation_id'  => 'required|exists:reservations,id',
                'room_id'         => 'required|exists:rooms,id',
                'arrival_date'    => 'required|date_format:Y-m-d',
                'departure_date'  => 'required|date_format:Y-m-d|after:arrival_date',
                'currencycode'    => 'required|string',
                'meal_plan'       => 'required|string',
                'guest_counts'    => 'required|array',
                'totalprice'      => 'required|numeric',
            ]);
            $arrival   = Carbon::parse($validations['arrival_date'])->startOfDay();
            $departure = Carbon::parse($validations['departure_date'])->endOfDay();
            $service = new ReservationAvailableService();
            $service->checkAvailableRoom(
                room_id: $validations['room_id'],
                arrival: $arrival,
                departure: $departure
            );
            $room_reservation = RoomReservation::create($validations);
            return (new RoomReservationResource($room_reservation))->response()->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $room_reservation = RoomReservation::find($id);
        if (!$room_reservation) {
            return response()->json(['message' => "Room reservation Nº $id was not found."], 404);
        }
        return new RoomReservationResource($room_reservation);
    }

    public function update(Request $request, $id)
    {
        $room_reservation = RoomReservation::find($id);
        if (!$room_reservation) {
            return response()->json(['message' => "Room reservation Nº $id was not found."], 404);
        }
        try {
            $validated = $request->validate([
                'reservation_id'  => 'sometimes|required|exists:reservations,id',
                'room_id'         => 'sometimes|required|exists:rooms,id',
                'arrival_date'    => 'sometimes|required|date_format:Y-m-d',
                'departure_date'  => 'sometimes|required|date_format:Y-m-d|after:arrival_date',
                'currencycode'    => 'sometimes|required|string',
                'meal_plan'       => 'sometimes|required|string',
                'guest_counts'    => 'sometimes|required|array',
                'totalprice'      => 'sometimes|required|numeric',
            ]);
            if ($request->hasAny(['room_id', 'arrival_date', 'departure_date'])) {

                $new_room_id    = $validated['room_id']         ?? $room_reservation->room_id;
                $new_arrival    = $validated['arrival_date']    ?? $room_reservation->arrival_date;
                $new_departure  = $validated['departure_date']  ?? $room_reservation->departure_date;

                $arrival   = Carbon::parse($new_arrival)->startOfDay();
                $departure = Carbon::parse($new_departure)->endOfDay();

                $service = new ReservationAvailableService();
                $service->checkAvailableRoom(
                    room_id: $new_room_id,
                    arrival: $arrival,
                    departure: $departure,
                    exclude_id: $room_reservation->id
                );
            }
            $room_reservation->update($validated);
            return (new RoomReservationResource($room_reservation))->response()->setStatusCode(202);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $room_reservation = RoomReservation::find($id);
        if (!$room_reservation) {
            return response()->json(['message' => "Room reservation ID Nº $id was not found."], 404);
        }
        $room_reservation->delete();
        return response()->json(['message' => "Room reservation ID Nº $id was deleted."], 200);
    }

}
