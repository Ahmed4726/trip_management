<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boat;
use App\Models\Slot;
use App\Models\Trip;

class CalendarController extends Controller
{
    /* ================= FLEET ================= */

    public function fleet() {
        return view('calendar.fleet', [
            'boats' => Boat::orderBy('name')->get(),
    ]);
    }

    public function fleetIframe() {
        return view('calendar.fleet', ['iframe' => true]);
    }

/* ================= RESOURCES ================= */
public function fleetResources(Request $request)
{
    $boatId = $request->boat_id;

    // Get boats with rooms, optionally filter by boat_id
    $boats = Boat::with('rooms')
        ->when($boatId, fn ($q) => $q->where('id', $boatId))
        ->get();

    $resources = [];

    foreach ($boats as $boat) {
        // Boat as resource
        $resources[] = [
            'id' => 'boat-' . $boat->id,
            'title' => $boat->name,
            'expanded' => true,
        ];

        // Rooms as child resources
        foreach ($boat->rooms as $room) {
            $resources[] = [
                'id' => 'room-' . $room->id,
                'title' => $room->room_name . ' (Cap: ' . $room->capacity . ')',
                'parentId' => 'boat-' . $boat->id,
            ];
        }
    }

    return response()->json($resources);
}

/* ================= EVENTS ================= */
public function fleetEvents(Request $request)
{
    $boatId = $request->boat_id;
    $events = [];

    // Get slots with boats (many-to-many), rooms via boats, bookings with rooms and guests
    $slots = Slot::with([
        'boats.rooms',
        'bookings.rooms.guests', // make sure Booking has rooms() relation
    ])
    ->when($boatId, fn ($q) => $q->whereHas('boats', fn($q2) => $q2->where('boats.id', $boatId)))
    ->get();

    foreach ($slots as $slot) {

        foreach ($slot->boats as $boat) {
            $boatRowId = 'boat-' . $boat->id;

            // SLOT summary bar for boat
            $totalRooms = $boat->rooms->count();
            $bookedRoomIds = $slot->bookings->flatMap(fn($b) => $b->rooms->pluck('id'))->unique();
            $bookedCount = $bookedRoomIds->count();
            $availableCount = $totalRooms - $bookedCount;

            $events[] = [
                'id' => 'slot-' . $slot->id . '-boat-' . $boat->id,
                'resourceId' => $boatRowId,
                'title' => "Slot: {$slot->slot_type} | Booked: $bookedCount | Available: $availableCount",
                'start' => $slot->start_date,
                'end' => $slot->end_date,
                'color' => $bookedCount > 0 ? '#dc3545' : '#198754',
                'extendedProps' => [
                    'type' => 'slot',
                    'slot_id' => $slot->id,
                    'boat_name' => $boat->name,
                    'status' => $slot->status,
                ],
            ];

            // ROOM-level events
            foreach ($boat->rooms as $room) {

                // Check if this room is booked in this slot
                $booking = $slot->bookings->first(function($b) use ($room) {
                    return $b->rooms->contains($room->id);
                });

                if ($booking) {
                    $guestNames = $booking->rooms->firstWhere('id', $room->id)->guests->pluck('name')->join(', ') ?? 'Booked';

                    $events[] = [
                        'id' => 'booking-' . $booking->id . '-room-' . $room->id,
                        'resourceId' => 'room-' . $room->id,
                        'title' => $guestNames,
                        'start' => $slot->start_date,
                        'end' => $slot->end_date,
                        'color' => '#dc3545',
                        'extendedProps' => [
                            'type' => 'booking',
                            'booking_id' => $booking->id,
                            'slot_id' => $slot->id,
                            'room_id' => $room->id,
                            'room_name' => $room->room_name,
                            'boat_name' => $boat->name,
                        ],
                    ];
                } else {
                    // Available room
                    $events[] = [
                        'id' => 'available-' . $slot->id . '-' . $room->id,
                        'resourceId' => 'room-' . $room->id,
                        'start' => $slot->start_date,
                        'end' => $slot->end_date,
                        'display' => 'background',
                        'backgroundColor' => '#d1e7dd',
                        'extendedProps' => [
                            'type' => 'available',
                            'slot_id' => $slot->id,
                            'room_id' => $room->id,
                            'room_name' => $room->room_name,
                            'boat_name' => $boat->name,
                        ],
                    ];
                }

            } // end rooms loop
        } // end boats loop
    } // end slots loop

    return response()->json($events);
}





    /* ================= DRAG & DROP ================= */
    public function moveEvent(Request $request)
    {
        if ($request->type === 'trip' || $request->type === 'open-trip') {
            Trip::where('id',$request->id)->update([
                'start_date'=>$request->start,
                'end_date'=>$request->end
            ]);
            return response()->json(['ok'=>true]);
        }
        return response()->json(['ok'=>false,'message'=>'Invalid type']);
    }

 // Iframe version
public function boatIframe(Boat $boat) {
    return view('calendar.boat', ['boat'=>$boat,'iframe'=>true]);
}

// Boat resources: boat + rooms (collapsed by default)
public function boatResources(Boat $boat) {
    $resources = [
        ['id'=>'boat-'.$boat->id,'title'=>$boat->name,'expanded'=>false]
    ];

    foreach ($boat->rooms as $room) {
        $resources[] = [
            'id'=>'room-'.$room->id,
            'title'=>$room->room_name.' (Cap: '.$room->capacity.')',
            'parentId'=>'boat-'.$boat->id
        ];
    }
    return $resources;
}

// Boat events (Open Trips + Private Trips + Bookings)
public function boatEvents(Boat $boat)
{
    $events = [];

    // Get all slots with bookings
    $slots = $boat->slots()->with('bookings')->get();

    foreach ($slots as $slot) {
        $boatRowId = 'boat-' . $boat->id;

        // Slot-level summary for boat
        $totalRooms = $boat->rooms->count();
        $bookedRoomIds = $slot->bookings->pluck('room_id')->unique();
        $bookedCount = $bookedRoomIds->count();
        $availableCount = $totalRooms - $bookedCount;

        $events[] = [
            'id' => 'slot-' . $slot->id,
            'resourceId' => $boatRowId,
            'title' => "Slot: {$slot->title} | Booked: $bookedCount | Available: $availableCount",
            'start' => $slot->start_date,
            'end' => $slot->end_date,
            'color' => $bookedCount > 0 ? '#dc3545' : '#198754',
            'extendedProps' => [
                'type' => 'slot',
                'slot_id' => $slot->id,
            ],
        ];

        // Room-level events
        foreach ($boat->rooms as $room) {
            $roomBooking = $slot->bookings->firstWhere('room_id', $room->id);

            // dd($roomBooking);
            if ($roomBooking) {
                $events[] = [
                    'id' => 'booking-' . $roomBooking->id,
                    'resourceId' => 'room-' . $room->id,
                    'title' => $roomBooking->guest_name,
                    'start' => $slot->start_date,
                    'end' => $slot->end_date,
                    'color' => '#dc3545',
                    'extendedProps' => [
                        'type' => 'booking',
                        'booking_id' => $roomBooking->id,
                        'slot_id' => $slot->id,
                        'room_id' => $room->id,
                        'customer_name' => $roomBooking->guest_name,
                        'room_name' => $roomBooking->room->room_name ?? 'Room ' . $room->id,
                    ],
                ];
            } else {
                $events[] = [
                    'id' => 'available-' . $slot->id . '-' . $room->id,
                    'resourceId' => 'room-' . $room->id,
                    'start' => $slot->start_date,
                    'end' => $slot->end_date,
                    'display' => 'background',
                    'backgroundColor' => '#d1e7dd',
                    'extendedProps' => [
                        'type' => 'available',
                        'slot_id' => $slot->id,
                        'room_id' => $room->id,
                    ],
                ];
            }
        }
    }

    return response()->json($events);
}



}
