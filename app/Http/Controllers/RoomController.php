<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::orderBy('id')->get();
        if ($rooms->isEmpty()) {
            return response()->json(['message' => 'No rooms found.'], 404);
        }
        return RoomResource::collection($rooms);
    }

    public function store(Request $request)
    {
        try {
            $validations = $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string',
                'inventory_count' => 'required|integer'
            ]);
            $room = Room::create($validations);
            return (new RoomResource($room))->response()->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json(['message' => "Room ID Nº $id was not found."], 404);
        }
        return new RoomResource($room);
    }

    public function update(Request $request, $id)
    {
        try {
            $room = Room::find($id);
            if (!$room) {
                return response()->json(['message' => "Room ID Nº $id was not found."], 404);
            }
            $validations = $request->validate([
                'hotel_id' => 'required|exists:hotels,id|sometimes',
                'name' => 'required|string|sometimes',
                'inventory_count' => 'required|integer|sometimes'
            ]);
            $room->update($validations);
            return (new RoomResource($room))->response()->setStatusCode(202);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json(['message' => "Room ID Nº $id was not found."], 404);
        }
        $room_name = $room->name;
        $room->delete();
        return response()->json(['message' => "$room_name was deleted."], 200);
    }
}
