<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boat;
use App\Models\Agent;
use App\Models\Company;
use App\Models\Trip;
use App\Models\Booking;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BoatController extends Controller
{
    /**
     * Show all boats
     */
    public function boat_index()
    {
        $boats = Boat::with(['rooms.bookings'])->latest()->paginate(10);
        return view('admin.boats.index', compact('boats'));
    }

    /**
     * Show create boat form
     */
    public function create_boat()
    {
        return view('admin.boats.create');
    }

    /**
     * Store new boat
     */
    public function store_boat(Request $request)
    {
        Boat::create([
            'name'        => $request->name,
            'location'    => $request->location,
            'status'      => $request->status,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('boat.index')
            ->with('success', 'Boat created successfully');
    }

    /**
     * Show edit boat form
     */
    public function edit(Boat $boat)
    {
        return view('admin.boats.edit', compact('boat'));
    }

    /**
     * Update boat
     */
    public function update_boat(Request $request, Boat $boat)
    {
        $boat->update([
            'name'        => $request->name,
            'location'    => $request->location,
            'status'      => $request->status,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('boat.index', $boat->id)
            ->with('success', 'Boat updated successfully');
    }

    /**
     * Delete boat
     */
    public function destroy_boat(Boat $boat)
    {
        $boat->delete();

        return redirect()
            ->route('boat.index')
            ->with('success', 'Boat deleted successfully');
    }


    // Controller method
    public function room_index($id)
    {

        $boat = Boat::find($id);
        $rooms = $boat->rooms()->with('bookings')->get();

        $roomsData = $rooms->map(function ($room) {
            return [
                'id' => $room->id,
                'room_name' => $room->room_name,
                'capacity' => $room->capacity,
                'status' => $room->status,
                'is_booked' => $room->bookings->count() > 0,
                'bookings' => $room->bookings->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'trip_title' => $booking->trip->title,
                        'start_date' => $booking->trip->start_date,
                        'end_date' => $booking->trip->end_date,
                        'booking_status' => $booking->booking_status,
                    ];
                }),
            ];
        });

        return response()->json(['rooms' => $roomsData]);
    }

    public function show(Boat $boat)
    {
        $id = $boat->id;
        $boat = Boat::with(['rooms', 'trip.bookings'])->findOrFail($id);

            $bookings = Booking::whereHas('trip', fn($q)=>$q->where('boat_id', $id))
                ->with('trip')
                ->get();

            $nightsBooked = $bookings->sum(fn($b)=>\Carbon\Carbon::parse($b->trip->end_date)->diffInDays($b->trip->start_date));
            $totalNights = $boat->trip->sum(fn($t)=>\Carbon\Carbon::parse($t->end_date)->diffInDays($t->start_date));
            $occupancyRate = $totalNights ? round($nightsBooked / $totalNights * 100, 2) : 0;

            $directCount = $bookings->where('source','Direct')->count();
            $agentCount = $bookings->where('source','By Agent')->count();

            $revenue = auth()->user()->hasRole('admin') ? $bookings->sum('price') : null;

            return view('admin.boats.show', compact(
                'boat','bookings','nightsBooked','occupancyRate','directCount','agentCount','revenue'
            ));
    }


}
