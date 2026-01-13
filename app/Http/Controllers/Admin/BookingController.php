<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Slot, Boat, Room};
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['slot','boat','room'])->orderBy('created_at','desc')->get();
        return view('admin.booking.index', compact('bookings'));
    }

    public function create()
    {
        $slots = Slot::with('boat.rooms')->orderBy('start_date')->get();
        return view('admin.booking.create', compact('slots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'slot_id' => 'nullable|exists:slots,id',
            'boat_id' => 'required|exists:boats,id',
            'room_id' => 'required|exists:rooms,id',
            'guest_name' => 'required|string',
            'guest_email' => 'nullable|email',
            'guest_phone' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $slotId = $request->slot_id;

        // Inline slot creation if none selected
        if (!$slotId) {
            $existingSlot = Slot::where('boat_id', $request->boat_id)
                                ->whereDate('start_date', $request->start_date ?? now())
                                ->whereDate('end_date', $request->end_date ?? now())
                                ->first();

            if ($existingSlot) {
                $slotId = $existingSlot->id;
            } else {
                $slot = Slot::create([
                    'slot_type' => 'Open Trip',
                    'status' => 'Available',
                    'boat_id' => $request->boat_id,
                    'region_id' => $request->boat->region_id ?? null,
                    'departure_port_id' => null,
                    'arrival_port_id' => null,
                    'start_date' => $request->start_date ?? now(),
                    'end_date' => $request->end_date ?? now(),
                    'available_rooms' => [$request->room_id],
                ]);
                $slotId = $slot->id;
            }
        }

        // Collision check – same room and overlapping slot
        $collision = Booking::where('slot_id', $slotId)
                            ->where('room_id', $request->room_id)
                            ->exists();

        if ($collision) {
            return back()->withErrors(['room_id'=>'This room is already booked for the selected slot'])->withInput();
        }

        Booking::create([
            'slot_id' => $slotId,
            'boat_id' => $request->boat_id,
            'room_id' => $request->room_id,
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'notes' => $request->notes,
            'status' => 'Pending',
        ]);

        return redirect()->route('admin.bookings.index')->with('success','Booking created successfully.');
    }

    public function edit(Booking $booking)
    {
        $slots = Slot::with('boat.rooms')->orderBy('start_date')->get();
        return view('admin.booking.edit', compact('booking','slots'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'slot_id' => 'required|exists:slots,id',
            'boat_id' => 'required|exists:boats,id',
            'room_id' => 'required|exists:rooms,id',
            'guest_name' => 'required|string',
            'guest_email' => 'nullable|email',
            'guest_phone' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:Pending,Confirmed,Cancelled',
        ]);

        // Collision check
        $collision = Booking::where('slot_id', $request->slot_id)
                            ->where('room_id', $request->room_id)
                            ->where('id','!=',$booking->id)
                            ->exists();

        if ($collision) {
            return back()->withErrors(['room_id'=>'This room is already booked for the selected slot'])->withInput();
        }

        $booking->update($request->all());

        return redirect()->route('admin.bookings.index')->with('success','Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('success','Booking deleted successfully.');
    }
}
