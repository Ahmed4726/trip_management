<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $boat = Boat::findOrFail($request->boat_id);
        $rooms = $boat->rooms;

        return view('admin.room.index', compact('boat','rooms'));
    }

    public function create(Request $request)
    {
        $boat = Boat::findOrFail($request->boat_id);
        return view('admin.room.create', compact('boat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'boat_id' => 'required|exists:boats,id',
            'name' => 'required',
        ]);

        Room::create($request->only(
            'boat_id','name','deck','bed_type','extra_beds','capacity'
        ));

        Boat::where('id',$request->boat_id)
            ->increment('total_rooms');

        return redirect()
            ->route('admin.rooms.index',['boat_id'=>$request->boat_id])
            ->with('success','Room added.');
    }

    public function edit(Room $room)
    {
        return view('admin.room.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $room->update($request->only(
            'name','deck','bed_type','extra_beds','capacity'
        ));

        return redirect()
            ->route('admin.rooms.index',['boat_id'=>$room->boat_id])
            ->with('success','Room updated.');
    }

    public function destroy(Room $room)
    {
        if (!$room->canBeDeleted()) {
            return back()->with(
                'error',
                'Room cannot be deleted because it has bookings.'
            );
        }

        $boatId = $room->boat_id;
        $room->delete();

        Boat::where('id',$boatId)->decrement('total_rooms');

        return back()->with('success','Room deleted.');
    }
}
