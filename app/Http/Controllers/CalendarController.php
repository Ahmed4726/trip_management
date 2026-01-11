<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boat;
use App\Models\Trip;

class CalendarController extends Controller
{
    /* ================= FLEET ================= */

    public function fleet() {
        return view('calendar.fleet');
    }

    public function fleetIframe() {
        return view('calendar.fleet', ['iframe' => true]);
    }

    /* ================= RESOURCES ================= */

    public function fleetResources()
    {
        $boats = Boat::with('rooms')->get();

        $resources = $boats->map(function ($boat) {

            // Boat row
            $boatResource = [
                'id' => 'boat-'.$boat->id,
                'title' => $boat->name,
                'expanded' => false, // rooms collapsed by default
            ];

            $roomResources = $boat->rooms->map(function($room) use ($boat) {
                return [
                    'id' => 'room-'.$room->id,
                    'title' => $room->room_name.' (Cap: '.$room->capacity.')',
                    'parentId' => 'boat-'.$boat->id
                ];
            });

            return array_merge([$boatResource], $roomResources->toArray());

        })->flatten(1);

        return $resources;
    }

    /* ================= EVENTS ================= */

public function fleetEvents()
{
    $events = [];

    $trips = Trip::with([
        'boat.rooms',
        'bookings'
    ])->get();

    foreach ($trips as $trip) {

        $boatRowId = 'boat-'.$trip->boat_id;

        /* ================= OPEN TRIPS ================= */
        if ($trip->trip_type === 'open') {

            $bookingCount = $trip->bookings->count();
            $capacity = $trip->max_bookings ?? 10;

            // Color logic for OT
            if ($bookingCount >= $capacity) {
                $color = '#198754'; // green
                $label = 'Fully Booked';
            } elseif ($bookingCount > 2) {
                $color = '#fd7e14'; // orange
                $label = 'Limited Seats';
            } elseif ($bookingCount > 0) {
                $color = '#ffc107'; // yellow
                $label = 'Few Bookings';
            } else {
                $color = '#6f42c1'; // purple
                $label = 'Available';
            }

            // OT availability bar (boat row)
            $events[] = [
                'id' => 'open-trip-'.$trip->id,
                'resourceId' => $boatRowId,
                'title' => "Open Trip · $label ($bookingCount/$capacity)",
                'start' => $trip->start_date,
                'end' => $trip->end_date,
                'color' => $color,
                'classNames' => ['open'],
                'extendedProps' => [
                    'type' => 'open',
                    'trip_id' => $trip->id,
                    'booking_count' => $bookingCount,
                    'capacity' => $capacity
                ]
            ];

            // Loop through rooms to show booking per room
            foreach ($trip->boat->rooms as $room) {
                $booking = $trip->bookings->firstWhere('room_id', $room->id);

                if ($booking) {
                    // Booked room
                    $events[] = [
                        'id' => 'open-booking-'.$booking->id,
                        'resourceId' => 'room-'.$room->id,
                        'title' => $booking->customer_name,
                        'start' => $trip->start_date,
                        'end' => $trip->end_date,
                        'color' => '#dc3545',
                        'extendedProps' => [
                            'type' => 'booking',
                            'booking_id' => $booking->id,
                            'room_id' => $room->id
                        ]
                    ];
                } else {
                    // Available room
                    $events[] = [
                        'id' => 'open-available-'.$trip->id.'-'.$room->id,
                        'resourceId' => 'room-'.$room->id,
                        'start' => $trip->start_date,
                        'end' => $trip->end_date,
                        'display' => 'background',
                        'backgroundColor' => '#d1e7dd',
                        'extendedProps' => [
                            'type' => 'available',
                            'trip_id' => $trip->id,
                            'room_id' => $room->id
                        ]
                    ];
                }
            }

            continue; // skip private trip logic
        }

        /* ================= PRIVATE TRIPS ================= */
        $totalRooms    = $trip->boat->rooms->count();
        $bookedRoomIds = $trip->bookings->pluck('room_id')->unique();
        $bookedCount   = $bookedRoomIds->count();
        $available     = $totalRooms - $bookedCount;

        // Boat summary
        $events[] = [
            'id' => 'trip-'.$trip->id,
            'resourceId' => $boatRowId,
            'title' => "Total: $totalRooms | Available: $available | Booked: $bookedCount",
            'start' => $trip->start_date,
            'end' => $trip->end_date,
            'color' => $bookedCount > 0 ? '#dc3545' : '#198754',
            'extendedProps' => [
                'type' => 'trip',
                'trip_id' => $trip->id,
            ]
        ];

        // Booked rooms
        foreach ($trip->bookings as $booking) {
            $events[] = [
                'id' => 'booking-'.$booking->id,
                'resourceId' => 'room-'.$booking->room_id,
                'title' => 'Booked',
                'start' => $trip->start_date,
                'end' => $trip->end_date,
                'color' => '#dc3545',
                'extendedProps' => [
                    'type' => 'booking',
                    'booking_id' => $booking->id
                ]
            ];
        }

        // Available rooms
        foreach ($trip->boat->rooms as $room) {
            if (!$bookedRoomIds->contains($room->id)) {
                $events[] = [
                    'id' => 'available-'.$trip->id.'-'.$room->id,
                    'resourceId' => 'room-'.$room->id,
                    'start' => $trip->start_date,
                    'end' => $trip->end_date,
                    'display' => 'background',
                    'backgroundColor' => '#d1e7dd',
                    'extendedProps' => [
                        'type' => 'available',
                        'trip_id' => $trip->id,
                        'room_id' => $room->id
                    ]
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
    public function boatEvents(Boat $boat) {
    $events = [];

    $trips = $boat->trip()->with('bookings')->get();

    foreach ($trips as $trip) {

        $boatRowId = 'boat-'.$boat->id;

        // ================= OPEN TRIP =================
        if ($trip->trip_type === 'open') {
            $bookingCount = $trip->bookings->count();
            $capacity = $trip->max_bookings ?? 10;

            if ($bookingCount >= $capacity) { $color = '#198754'; $label = 'Fully Booked'; }
            elseif ($bookingCount > 2) { $color = '#fd7e14'; $label = 'Limited Seats'; }
            elseif ($bookingCount > 0) { $color = '#ffc107'; $label = 'Few Bookings'; }
            else { $color = '#6f42c1'; $label = 'Available'; }

            // OT bar on boat row
            $events[] = [
                'id'=>'open-trip-'.$trip->id,
                'resourceId'=>$boatRowId,
                'title'=>"Open Trip · $label ($bookingCount/$capacity)",
                'start'=>$trip->start_date,
                'end'=>$trip->end_date,
                'color'=>$color,
                'classNames'=>['open-trip'],
                'extendedProps'=>[
                    'type'=>'open-trip',
                    'trip_id'=>$trip->id,
                    'booking_count'=>$bookingCount,
                    'capacity'=>$capacity
                ]
            ];

            // Room-level OT
            foreach ($boat->rooms as $room) {
                $booking = $trip->booking->firstWhere('room_id',$room->id);
                if ($booking) {
                    $events[] = [
                        'id'=>'open-booking-'.$booking->id,
                        'resourceId'=>'room-'.$room->id,
                        'title'=>$booking->customer_name,
                        'start'=>$trip->start_date,
                        'end'=>$trip->end_date,
                        'color'=>'#dc3545', // OT color for booked
                        'extendedProps'=>[
                            'type'=>'booking',
                            'booking_id'=>$booking->id,
                            'room_id'=>$room->id,
                            'customer_name'=>$booking->customer_name,
                            'room_name'=>$room->room_name
                        ]
                    ];

                } else {
                    $events[] = [
                        'id'=>'open-available-'.$trip->id.'-'.$room->id,
                        'resourceId'=>'room-'.$room->id,
                        'start'=>$trip->start_date,
                        'end'=>$trip->end_date,
                        'display'=>'background',
                        'backgroundColor'=>'#d1e7dd',
                        'extendedProps'=>[
                            'type'=>'available',
                            'trip_id'=>$trip->id,
                            'room_id'=>$room->id
                        ]
                    ];
                }
            }

            continue;
        }

        // ================= PRIVATE TRIP =================
        $totalRooms = $boat->rooms->count();
        $bookedRoomIds = $trip->bookings->pluck('room_id')->unique();
        $bookedCount = $bookedRoomIds->count();
        $available = $totalRooms - $bookedCount;

        // Boat summary
        $events[] = [
            'id'=>'trip-'.$trip->id,
            'resourceId'=>$boatRowId,
            'title'=>"Total: $totalRooms | Available: $available | Booked: $bookedCount",
            'start'=>$trip->start_date,
            'end'=>$trip->end_date,
            'color'=>$bookedCount>0?'#dc3545':'#198754',
            'extendedProps'=>[
                'type'=>'trip',
                'trip_id'=>$trip->id
            ]
        ];

        // Booked rooms
        foreach ($trip->bookings as $booking) {
            $events[] = [
                'id'=>'booking-'.$booking->id,
                'resourceId'=>'room-'.$booking->room_id,
                'title'=>'Booked',
                'start'=>$trip->start_date,
                'end'=>$trip->end_date,
                'color'=>'#dc3545',
                'extendedProps'=>[
                    'type'=>'booking',
                    'booking_id'=>$booking->id,
                    'customer_name'=>$booking->customer_name
                ]
            ];
        }

        // Available rooms
        foreach ($boat->rooms as $room) {
            if (!$bookedRoomIds->contains($room->id)) {
                $events[] = [
                    'id'=>'available-'.$trip->id.'-'.$room->id,
                    'resourceId'=>'room-'.$room->id,
                    'start'=>$trip->start_date,
                    'end'=>$trip->end_date,
                    'display'=>'background',
                    'backgroundColor'=>'#d1e7dd',
                    'extendedProps'=>[
                        'type'=>'available',
                        'trip_id'=>$trip->id,
                        'room_id'=>$room->id
                    ]
                ];
            }
        }

    }

    return response()->json($events);
}


}
