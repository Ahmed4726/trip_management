<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boat;
use App\Models\Agent;
use App\Models\Company;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\CancellationPolicy;
use App\Models\PaymentPolicy;
use App\Models\RatePlan;
use App\Models\Room;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    protected $tenant;

    public function __construct()
    {
        // If tenant is resolved via middleware, set it
        $this->tenant = app()->bound('tenant') ? app('tenant') : null;
    }

    // ==========================
    // INDEX
    // ==========================
    public function booking_index(Request $request)
    {
        $bookings = Booking::with(['trip', 'agent'])
            ->when($this->tenant, function ($q) {
                $q->where('company_id', $this->tenant->id);
            })
            ->when($request->customer_name, function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->customer_name . '%');
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('booking_status', $request->status);
            })
            ->when($request->start_date, function ($q) use ($request) {
                $q->whereHas('trip', function ($q2) use ($request) {
                    $q2->whereDate('start_date', '>=', $request->start_date);
                });
            })
            ->when($request->end_date, function ($q) use ($request) {
                $q->whereHas('trip', function ($q2) use ($request) {
                    $q2->whereDate('end_date', '<=', $request->end_date);
                });
            })
            ->latest()
            ->get();



            $boats = Boat::with('rooms')->get();

            $rooms = Room::get();


        return view('admin.bookings.index', compact('bookings','boats', 'rooms'));
    }

    // ==========================
    // CREATE
    // ==========================
    public function create_booking()
    {
        $companyId = null;
        $companies = null;

        if (auth()->user()->hasRole('admin')) {
            $companies = Company::all();
        } else {
            $companyId = auth()->user()->company_id;
        }

        $agents = $companyId
            ? Agent::where('company_id', $companyId)->get()
            : Agent::all();

        // Fetch trips where not all rooms are booked
        $trips = Trip::with(['boat.rooms', 'bookings'])
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->get()
            ->filter(function($trip) {
                $totalRooms = $trip->boat->rooms->count();     // total rooms of the boat
                $bookedRooms = $trip->bookings->count();      // number of bookings for this trip
                return $bookedRooms < $totalRooms;            // only trips with available rooms
            });

        $boats = $companyId
            ? Boat::withCount(['rooms as available_rooms_count' => function ($q) {
                $q->whereDoesntHave('bookings');
            }])->where('company_id', $companyId)->get()
            : Boat::withCount(['rooms as available_rooms_count' => function ($q) {
                $q->whereDoesntHave('bookings');
            }])->get();

        $ratePlans = $companyId
            ? RatePlan::where('company_id', $companyId)->get()
            : RatePlan::all();

        $paymentPolicies = $companyId
            ? PaymentPolicy::where('company_id', $companyId)->get()
            : PaymentPolicy::all();

        $cancellationPolicies = $companyId
            ? CancellationPolicy::where('company_id', $companyId)->get()
            : CancellationPolicy::all();

        return view('admin.bookings.create', compact(
            'agents', 'trips', 'companies', 'companyId',
            'boats', 'ratePlans', 'paymentPolicies', 'cancellationPolicies'
        ));
    }




    // ==========================
    // STORE
    // ==========================
    public function store_booking(Request $request)
    {
        if ($request->inline_trip) {

            // Validate inline trip fields
            $tripValidated = $request->validate([
                'trip_title' => 'required|string|max:255',
                'boat_id' => 'required|exists:boats,id',
                'trip_type' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'price' => 'nullable|numeric',
                'rate_plan_id' => 'required',
                'payment_policy_id' => 'required',
                'cancellation_policy_id' => 'required',
                'notes' => 'nullable|string',
                'company_id' => 'required'
            ]);

            if (!auth()->user()->hasRole('admin')) {
                $tripValidated['company_id'] = auth()->user()->company_id;
            }

            // Create the trip
            $trip = Trip::create([
                'title' => $tripValidated['trip_title'],
                'boat_id' => $tripValidated['boat_id'],
                'trip_type' => $tripValidated['trip_type'],
                'start_date' => $tripValidated['start_date'],
                'end_date' => $tripValidated['end_date'],
                'price' => $tripValidated['price'],
                'rate_plan_id' => $tripValidated['rate_plan_id'],
                'payment_policy_id' => $tripValidated['payment_policy_id'],
                'cancellation_policy_id' => $tripValidated['cancellation_policy_id'],
                'notes' => $tripValidated['notes'] ?? null,
                'status' => $request->status,
                'company_id' => $tripValidated['company_id'],
            ]);

            // Calculate DP and balance due
            $paymentPolicy = PaymentPolicy::find($tripValidated['payment_policy_id']);
            $total = $tripValidated['price'];
            $dp_amount = round($total * $paymentPolicy->dp_percent / 100, 2);
            $balance_due_date = now()->parse($tripValidated['start_date'])
                ->subDays($paymentPolicy->balance_days_before_start);

            $trip->update([
                'pricing_snapshot_json' => json_encode([
                    'total' => $total,
                    'dp_amount' => $dp_amount
                ]),
                'payment_policy_snapshot_json' => json_encode($paymentPolicy),
                'cancellation_policy_snapshot_json' => json_encode(
                    CancellationPolicy::find($tripValidated['cancellation_policy_id'])
                ),
                'dp_amount' => $dp_amount,
                'balance_due_date' => $balance_due_date,
            ]);

            // Set trip_id for booking
            $request->merge(['trip_id' => $trip->id]);
        }


        // Now validate booking fields
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'room_id' => 'nullable',
            'customer_name' => 'required|string|max:255',
            // 'guests' => 'nullable',
            'source' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:255',
            'booking_status' => 'nullable|in:pending,confirmed,cancelled',
            'pickup_location_time' => 'nullable|string|max:255',
            'addons' => 'nullable|string|max:255',
            'room_preference' => 'nullable|in:single,double,suite',
            'agent_id' => 'nullable|exists:agents,id',
            'comments' => 'nullable|string',
            'notes' => 'nullable|string'
            // 'company_id' => 'required'
        ]);

        // dd(vars: 'ok');

        // Automatically set boat_id from trip
        $trip = Trip::findOrFail($validated['trip_id']);
        $validated['boat_id'] = $trip->boat_id;

        $validated['booking_status'] = $validated['booking_status'] ?? 'pending';
        $validated['token'] = Str::random(32);

        if (!auth()->user()->hasRole('admin')) {
            $validated['company_id'] = auth()->user()->company_id;
        }

        Booking::create($validated);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking created successfully.');
    }


    // ==========================
    // SHOW
    // ==========================
    public function show_booking($tripId)
    {
        $trip = Trip::with('boat.rooms', 'bookings.rooms')->findOrFail($tripId);

        $bookings = $trip->bookings()->with('rooms', 'boat')->get();

        // dd($bookings);
        // Compute stats for graph
        $totalRooms = $trip->boat->rooms->count();
        $bookedRooms = $bookings->count();
        $availableRooms = $totalRooms - $bookedRooms;

        return view('admin.bookings.show', compact('trip', 'bookings', 'totalRooms', 'bookedRooms', 'availableRooms'));
    }


    // ==========================
    // EDIT
    // ==========================
    public function edit_booking($id)
    {
        $booking = Booking::when($this->tenant, function ($q) {
            $q->where('company_id', $this->tenant->id);
        })->findOrFail($id);

        $trips = Trip::when($this->tenant, function ($q) {
            $q->where('company_id', $this->tenant->id);
        })->get();

        $agents = Agent::when($this->tenant, function ($q) {
            $q->where('company_id', $this->tenant->id);
        })->get();

        return view('admin.bookings.edit', compact('booking','trips','agents'));
    }

    // ==========================
    // UPDATE
    // ==========================
    public function update_booking(Request $request, $id)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'customer_name' => 'required|string|max:255',
            'guests' => 'required|integer|min:1',
            'source' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:255',
            'booking_status' => 'nullable|in:pending,confirmed,cancelled',
            'pickup_location_time' => 'nullable|string|max:255',
            'addons' => 'nullable|string|max:255',
            'room_preference' => 'nullable|in:single,double,suite',
            'agent_id' => 'nullable|exists:agents,id',
            'comments' => 'nullable|string',
            'notes' => 'nullable|string',
            'dp_paid' => 'nullable|boolean',
        ]);

        $booking = Booking::when($this->tenant, function ($q) {
            $q->where('company_id', $this->tenant->id);
        })->findOrFail($id);

        if ($request->has('dp_paid') && $request->dp_paid) {
            $validated['dp_paid'] = true;
            $validated['booking_status'] = 'confirmed';
        } else {
            $validated['dp_paid'] = false;
        }

        $booking->update($validated);

        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully.');
    }

    // ==========================
    // DESTROY
    // ==========================
    public function destroy_booking($id)
    {
        // $booking = Booking::when($this->tenant, function ($q) {
        //     $q->where('company_id', $this->tenant->id);
        // })->findOrFail($id);

        $booking = Booking::findOrFail($id);

        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully.');
    }

       /**
     * Return available rooms for an existing trip
     */
    public function availableRoomsForTrip(Trip $trip)
    {
        $allRooms = $trip->boat->rooms()->get(); // make sure it's a collection

        // Get rooms already booked in this trip
        $bookedRoomIds = Booking::where('trip_id', $trip->id)
                                ->pluck('room_id')
                                ->toArray(); // convert to array for whereNotIn

        // Filter available rooms
        $availableRooms = $allRooms->whereNotIn('id', $bookedRoomIds);

        // Return JSON
        return response()->json([
            'rooms' => $availableRooms->map(function($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->room_name,
                    'capacity' => $room->capacity,
                    'price_per_day' => $room->price_per_night,
                ];
            })->values() // reset keys
        ]);
    }


    /**
     * Return available rooms of a boat for given dates (inline trip creation)
     */
    public function availableRoomsForBoat(Request $request)
    {
        $boatId = $request->boat;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if (!$boatId || !$startDate || !$endDate) {
            return response()->json(['rooms' => []]);
        }

        $boat = Boat::with('rooms')->find($boatId);
        if (!$boat) return response()->json(['rooms' => []]);

        // Get all room IDs booked in overlapping trips
        $bookedRoomIds = Booking::whereHas('trip', function($q) use ($startDate, $endDate) {
            $q->where(function($q2) use ($startDate, $endDate) {
                $q2->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function($q3) use ($startDate, $endDate) {
                    $q3->where('start_date', '<', $endDate)
                        ->where('end_date', '>', $startDate);
                });
            });
        })->pluck('room_id');

        $availableRooms = $boat->rooms->whereNotIn('id', $bookedRoomIds);

        return response()->json([
            'rooms' => $availableRooms->map(fn($room) => [
                'id' => $room->id,
                'name' => $room->room_name,
                'capacity' => $room->capacity,
                'price_per_day' => $room->price_per_night,
            ])
        ]);
    }



    public function getTripRooms(Trip $trip)
    {
        $rooms = $trip->boat->rooms->map(function($room) use ($trip) {
            $isBooked = $trip->bookings()->where('room_id', $room->id)->exists();
            return [
                'id' => $room->id,
                'name' => $room->room_name,
                'capacity' => $room->capacity,
                'is_booked' => $isBooked,
            ];
        });

        return response()->json(['rooms' => $rooms]);
    }



public function getEvents(Request $request)
{
    $query = Trip::with('boat.rooms', 'bookings');

    if ($request->boat_id) {
        $query->where('boat_id', $request->boat_id);
    }

    $trips = $query->get();
    $events = [];

    foreach ($trips as $trip) {
        $totalRooms = $trip->boat->rooms->count();           // total rooms
        $bookedRooms = $trip->bookings->count();            // rooms booked
        $availableRooms = $totalRooms - $bookedRooms;       // rooms left

        $tripStatus = $availableRooms > 0 ? 'Available' : 'Fully Booked';

        // dd($trip->end_date);
        $events[] = [
            'id' => $trip->id,
            'title' => $trip->title,
            'start' => $trip->start_date,
            'end' => \Carbon\Carbon::parse($trip->end_date)->addDay()->format('Y-m-d'), // add 1 day for FC display
            'color' => $availableRooms > 0 ? '#34d399' : '#f87171', // green if available, red if fully booked
            'extendedProps' => [
                'trip_id' => $trip->id,
                'boat_name' => $trip->boat->name,
                'total_rooms' => $totalRooms,
                'booked' => $bookedRooms,
                'available' => $availableRooms,
                'start_date' => $trip->start_date,
                'end_date' => $trip->end_date,
            ]
        ];
    }

    return response()->json($events);
}




}
