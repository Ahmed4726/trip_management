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

        $boats = Boat::with('rooms')
            ->when($boatId, fn ($q) => $q->where('id', $boatId))
            ->get();

        $resources = $boats->map(function ($boat) {

            $boatResource = [
                'id' => 'boat-' . $boat->id,
                'title' => $boat->name,
                'expanded' => true,
            ];

            $roomResources = $boat->rooms->map(function ($room) use ($boat) {
                return [
                    'id' => 'room-' . $room->id,
                    'title' => $room->room_name . ' (Cap: ' . $room->capacity . ')',
                    'parentId' => 'boat-' . $boat->id,
                ];
            });

            return array_merge([$boatResource], $roomResources->toArray());
        })->flatten(1);

        return response()->json($resources);
    }

    /* ================= EVENTS ================= */

public function fleetEvents(Request $request)
{
    $boatId = $request->boat_id;
    $events = [];

    $slots = Slot::with(['boat.rooms', 'bookings'])
        ->when($boatId, fn ($q) => $q->where('boat_id', $boatId))
        ->get();

    foreach ($slots as $slot) {

        $boatRowId = 'boat-' . $slot->boat_id;

        /* SLOT BAR */
        $events[] = [
            'id' => 'slot-' . $slot->id,
            'resourceId' => $boatRowId,
            'title' => $slot->slot_type . ' | ' . $slot->status,
            'start' => $slot->start_date,
            'end' => $slot->end_date,
            'color' => $slot->isBlocked() ? '#6c757d' : '#198754',
            'extendedProps' => [
                'type' => 'slot',
                'slot_id' => $slot->id,
                'status' => $slot->status,
                'room_name' => null, // <-- no single room here
                'boat_name' => $slot->boat->name,
            ],
        ];

        /* ROOMS */
        foreach ($slot->boat->rooms as $room) {

            $booking = $slot->bookings->firstWhere('room_id', $room->id);

            if ($booking) {
                // Booked room
                $events[] = [
                    'id' => 'booking-' . $booking->id,
                    'resourceId' => 'room-' . $room->id,
                    'title' => $booking->guest_name ?? 'Booked',
                    'start' => $slot->start_date,
                    'end' => $slot->end_date,
                    'color' => '#dc3545',
                    'extendedProps' => [
                        'type' => 'booking',
                        'booking_id' => $booking->id,
                        'room_id' => $room->id,
                        'room_name' => $room->room_name,
                        'boat_name' => $slot->boat->name,
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
                        'boat_name' => $slot->boat->name,
                    ],
                ];
            }
        }
    }

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
