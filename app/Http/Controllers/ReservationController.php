<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::orderBy('id')->get();
        if ($reservations->isEmpty()) {
            return response()->json(['message' => 'No reservations found.'], 404);
        }
        return ReservationResource::collection($reservations);
    }

    public function store(Request $request)
    {
        try {
            $validations = $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'customer_id' => 'required|exists:customers,id',
                'date' => 'required|date_format:Y-m-d',
                'time' => 'required|date_format:H:i',
            ]);
            $reservation = Reservation::create($validations);
            return (new ReservationResource($reservation))->response()->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json(['message' => "Reservation ID Nº $id was not found."], 404);
        }
        return new ReservationResource($reservation);
    }

    public function update(Request $request, $id)
    {
        try {
            $reservation = Reservation::find($id);
            if (!$reservation) {
                return response()->json(['message' => "Reservation ID Nº $id was not found."], 404);
            }
            $validations = $request->validate([
                'hotel_id' => 'required|exists:hotels,id|sometimes',
                'customer_id' => 'required|exists:customers,id|sometimes',
                'date' => 'required|date_format:Y-m-d|sometimes',
                'time' => 'required|date_format:H:i|sometimes',
            ]);
            $reservation->update($validations);
            return (new ReservationResource($reservation))->response()->setStatusCode(202);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $reservation = Reservation::find($id);
        $reservation_hotel = $reservation->hotel->name;
        if (!$reservation) {
            return response()->json(['message' => "Reservation ID Nº $id was not found."], 404);
        }
        $reservation->delete();
        return response()->json(['message' => "Reservation $reservation_hotel was deleted."], 200);
    }

}
