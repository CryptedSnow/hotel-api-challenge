<?php

namespace App\Http\Controllers;

use App\Http\Resources\RateResource;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RateController extends Controller
{
    public function index()
    {
        $rates = Rate::orderBy('id')->get();
        if ($rates->isEmpty()) {
            return response()->json(['message' => 'No rates found.'], 404);
        }
        return RateResource::collection($rates);
    }

    public function store(Request $request)
    {
        try {
            $validations = $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string',
                'active' => 'required|boolean',
                'price' => 'required|numeric'
            ]);
            $rate = Rate::create($validations);
            return (new RateResource($rate))->response()->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $rate = Rate::find($id);
        if (!$rate) {
            return response()->json(['message' => "Rate ID Nº $id was not found."], 404);
        }
        return new RateResource($rate);
    }

    public function update(Request $request, $id)
    {
        try {
            $rate = Rate::find($id);
            if (!$rate) {
                return response()->json(['message' => "Rate ID Nº $id was not found."], 404);
            }
            $validations = $request->validate([
                'hotel_id' => 'required|exists:hotels,id|sometimes',
                'name' => 'required|string|sometimes',
                'active' => 'required|boolean|sometimes',
                'price' => 'required|numeric|sometimes'
            ]);
            $rate->update($validations);
            return (new RateResource($rate))->response()->setStatusCode(202);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $rate = Rate::find($id);
        if (!$rate) {
            return response()->json(['message' => "Rate ID Nº $id was not found."], 404);
        }
        $rate_name = $rate->name;
        $rate->delete();
        return response()->json(['message' => "$rate_name was deleted."], 200);
    }

}
