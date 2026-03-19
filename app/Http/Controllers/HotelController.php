<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotelResource;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::orderBy('id')->get();
        if ($hotels->isEmpty()) {
            return response()->json(['message' => 'No hotels found.'], 404);
        }
        return HotelResource::collection($hotels);
    }

    public function store(Request $request)
    {
        try {
            $validations = $request->validate([
                'name' => 'required|string',
            ]);
            $hotel = Hotel::create($validations);
            return (new HotelResource($hotel))->response()->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) {
            return response()->json(['message' => "Hotel ID Nº $id was not found."], 404);
        }
        return new HotelResource($hotel);
    }

    public function update(Request $request, $id)
    {
        try {
            $hotel = Hotel::find($id);
            if (!$hotel) {
                return response()->json(['message' => "Hotel ID Nº $id was not found."], 404);
            }
            $validations = $request->validate([
                'name' => 'sometimes|required|string',
            ]);
            $hotel->update($validations);
            return (new HotelResource($hotel))->response()->setStatusCode(202);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) {
            return response()->json(['message' => "Hotel ID Nº $id was not found."], 404);
        }
        $hotel_name = $hotel->name;
        $hotel->delete();
        return response()->json(['message' => "$hotel_name was deleted."], 200);
    }

}
