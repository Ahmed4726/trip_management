<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boat;
use App\Models\Agent;
use App\Models\Company;
use App\Models\Trip;
use App\Models\Booking;
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


        return view('admin.bookings.index', compact('bookings','boats'));
    }

    // ==========================
    // CREATE
    // ==========================
    public function create_booking()
    {
        if (auth()->user()->hasRole('admin')) {
            $companies = Company::all();
            $companyId = null; // Admin sees all
        } else {
            $companies = null;
            $companyId = auth()->user()->company_id; // Non-admin sees only their company
        }

        $agents = $companyId 
            ? Agent::where('company_id', $companyId)->get() 
            : Agent::all();

        $trips = $companyId
            ? Trip::where('company_id', $companyId)->get()
            : Trip::all();


        $boats = Boat::withCount('rooms')->get(); // add rooms_count for display

        return view('admin.bookings.create', compact('agents', 'trips', 'companies', 'companyId','boats'));
    }


    // ==========================
    // STORE
    // ==========================
    public function store_booking(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'customer_name' => 'required|string|max:255',
            'guests' => 'nullable|integer|min:1',
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
            'company_id' => 'required'
        ]);

        // Default booking_status
        if (empty($validated['booking_status'])) {
            $validated['booking_status'] = 'pending';
        }

        // Generate unique token
        $validated['token'] = Str::random(32);

        // 🔑 Add company_id if tenant exists
        if ($this->tenant) {
            $validated['company_id'] = $this->tenant->id;
        }

        $booking = Booking::create($validated);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking created successfully.');
    }

    // ==========================
    // SHOW
    // ==========================
    public function show_booking($id)
    {
        $booking = Booking::with(['trip', 'agent'])
            ->when($this->tenant, function ($q) {
                $q->where('company_id', $this->tenant->id);
            })
            ->findOrFail($id);

        return view('admin.bookings.detail', compact('booking'));
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
        $booking = Booking::when($this->tenant, function ($q) {
            $q->where('company_id', $this->tenant->id);
        })->findOrFail($id);

        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully.');
    }

       /**
     * Return available rooms for an existing trip
     */
    public function availableRoomsForTrip(Trip $trip)
    {
        // Get all rooms of the trip's boat
        $allRooms = $trip->boat->rooms;

        // Get rooms already booked in this trip
        $bookedRoomIds = Booking::where('trip_id', $trip->id)
                                ->pluck('guests'); // assuming 'guests' stores room IDs
        // Filter available rooms
        $availableRooms = $allRooms->whereNotIn('id', $bookedRoomIds);

        return response()->json([
            'rooms' => $availableRooms->map(fn($room)=>[
                'id' => $room->id,
                'name' => $room->room_name,
                'capacity' => $room->capacity,
                'price_per_day' => $room->price_per_night,
            ])
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

        if(!$boatId || !$startDate || !$endDate){
            return response()->json(['rooms'=>[]]);
        }

        $boat = Boat::with('rooms')->find($boatId);
        if(!$boat) return response()->json(['rooms'=>[]]);

        // Get room IDs already booked in overlapping trips
        $bookedRoomIds = Booking::whereHas('trip', function($q) use($startDate, $endDate){
            $q->where(function($q2) use($startDate, $endDate){
                $q2->whereBetween('start_date', [$startDate, $endDate])
                   ->orWhereBetween('end_date', [$startDate, $endDate])
                   ->orWhere(function($q3) use($startDate, $endDate){
                        $q3->where('start_date', '<=', $startDate)
                           ->where('end_date', '>=', $endDate);
                   });
            });
        })->pluck('guests'); // assuming guests column stores room IDs

        // Filter available rooms
        $availableRooms = $boat->rooms->whereNotIn('id', $bookedRoomIds);

        return response()->json([
            'rooms' => $availableRooms->map(fn($room)=>[
                'id' => $room->id,
                'name' => $room->room_name,
                'capacity' => $room->capacity,
                'price_per_day' => $room->price_per_night,
            ])
        ]);
    }



}
