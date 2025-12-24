<?php

namespace App\Http\Controllers;
use App\Models\Boat;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // Room index
public function room_index(Boat $boat)
{
    $rooms = $boat->rooms; // fetch rooms for this boat
    return view('admin.rooms.index', compact('rooms', 'boat'));
}

// Create room
public function create_room(Boat $boat)
{
    return view('admin.rooms.create', compact('boat'));
}

// Store room
public function store_room(Request $request, Boat $boat)
{
    $request->validate([
        'room_name' => 'required|string',
        'capacity' => 'required|integer|min:1',
        'price_per_night' => 'nullable|numeric',
        'status' => 'required|string',
    ]);

    $boat->rooms()->create($request->all());

    return redirect()->route('room.index', $boat->id)
                     ->with('success', 'Room created successfully');
}


    public function edit(Boat $boat, Room $room)
    {
        return view('admin.rooms.edit', compact('boat', 'room'));
    }

    public function update_room(Request $request, Boat $boat, Room $room)
    {
        $request->validate([
            'room_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'nullable|numeric',
            'status' => 'required|in:available,maintenance,inactive',
        ]);

        $room->update($request->all());

        return redirect()
            ->route('room.index', $boat->id)
            ->with('success', 'Room updated successfully');
    }

    public function destroy(Boat $boat, Room $room)
    {
        $room->delete();

        return redirect()
            ->route('room.index', $boat->id)
            ->with('success', 'Room deleted successfully');
    }
}
