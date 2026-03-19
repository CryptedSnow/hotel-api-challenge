<?php

namespace App\Http\Controllers;

use App\Http\Resources\DailyPriceResource;
use App\Models\DailyPrice;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DailyPriceController extends Controller
{
    public function index()
    {
        $daily_price = DailyPrice::orderBy('id')->get();
        if ($daily_price->isEmpty()) {
            return response()->json(['message' => 'No daily prices found.'], 404);
        }
        return DailyPriceResource::collection($daily_price);
    }

    public function store(Request $request)
    {
        try {
            $validations = $request->validate([
                'room_reservation_id' => 'required|exists:room_reservations,id',
                'rate_id' => 'required|exists:rates,id',
                'date' => 'required|date_format:Y-m-d',
                'price' => 'required|numeric',
            ]);
            $daily_price = DailyPrice::create($validations);
            return (new DailyPriceResource($daily_price))->response()->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $daily_price = DailyPrice::find($id);
        if (!$daily_price) {
            return response()->json(['message' => "Daily price Nº $id was not found."], 404);
        }
        return new DailyPriceResource($daily_price);
    }

    public function update(Request $request, $id)
    {
        try {
            $daily_price = DailyPrice::find($id);
            if (!$daily_price) {
                return response()->json(['message' => "Daily price ID Nº $id was not found."], 404);
            }
            $validations = $request->validate([
                'room_reservation_id' => 'required|exists:room_reservations,id|sometimes',
                'rate_id' => 'required|exists:rates,id|sometimes',
                'date' => 'required|date_format:Y-m-d|sometimes',
                'price' => 'required|numeric|sometimes',
            ]);
            $daily_price->update($validations);
            return (new DailyPriceResource($daily_price))->response()->setStatusCode(202);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $daily_price = DailyPrice::find($id);
        if (!$daily_price) {
            return response()->json(['message' => "Daily price ID Nº $id was not found."], 404);
        }
        $daily_price->delete();
        return response()->json(['message' => "Daily price ID Nº $id was deleted."], 200);
    }

}
