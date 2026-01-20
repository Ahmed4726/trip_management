<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Agent, Booking, Slot, Boat, BookingGuestRoom, CancellationPolicy, Company, Currency, Guest, PaymentPolicy, Port, RatePlan, Region, Room, Salesperson};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['slot','boat','room'])->orderBy('created_at','desc')->get();
        return view('admin.booking.index', compact('bookings'));
    }

  public function create()
    {
        return view('admin.booking.create', [
            'slots' => Slot::with('boat.rooms')->get(),
            'agents' => Agent::orderBy('first_name')->get(),
            'guests' => Guest::orderBy('name')->get(),

            'ratePlans' => RatePlan::with('rules')->get(),
            'paymentPolicies' => PaymentPolicy::all(),
            'cancellationPolicies' => CancellationPolicy::with('rules')->get(),

            'boats' => Boat::with('rooms')->get(), // add rooms_count for display

            'regions' => Region::all(),
            'ports' => Port::all(),
            'salespersons' => Salesperson::orderBy('name')->get(),
            'currencies' => Currency::all(), // fetch all currencies from DB


            'companies' => auth()->user()->hasRole('admin')
                ? Company::all()
                : Company::where('id', auth()->user()->company_id)->get(),
        ]);
    }

public function store(Request $request)
{
    // dd($request->rooms);
    $validator = Validator::make($request->all(), [
        'source' => 'required|in:Direct,Agent',
        'agent_id' => 'nullable|required_if:source,Agent',
        // 'rooms' => 'required|array',
        'guests' => 'required|array',
        'guest_rooms' => 'required|array',
    ]);

    if ($validator->fails()) {
        return back()
            ->withErrors($validator)
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 1: Resolve Slot
    |--------------------------------------------------------------------------
    */
    if ($request->slot_id) {
        $slot = Slot::with('boat.rooms')->findOrFail($request->slot_id);
    } else {

        $request->validate([
            'boat_id' => 'required|exists:boats,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'region_id' => 'required|exists:regions,id',
            'embarkation_port_id' => 'required|exists:ports,id',
            'disembarkation_port_id' => 'required|exists:ports,id',
        ]);

        $collision = Slot::where('boat_id', $request->boat_id)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
            })->exists();

        if ($collision) {
            return back()->withErrors([
                'start_date' => 'Slot collision detected'
            ])->withInput();
        }

        $slot = Slot::create([
            'boat_id' => $request->boat_id,
            'region_id' => $request->region_id,
            'embarkation_port_id' => $request->embarkation_port_id,
            'disembarkation_port_id' => $request->disembarkation_port_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'Available',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 2: Capacity Validation (Per Room)
    |--------------------------------------------------------------------------
    */
    $roomCounts = [];

    foreach ($request->guest_rooms as $guestId => $roomId) {
        $roomCounts[$roomId] = ($roomCounts[$roomId] ?? 0) + 1;
    }

    $rooms = Room::whereIn('id', array_keys($roomCounts))->get()->keyBy('id');

    foreach ($roomCounts as $roomId => $count) {
        $capacity = $rooms[$roomId]->capacity + $rooms[$roomId]->extra_beds;
        if ($count > $capacity) {
            return back()->withErrors([
                'guest_rooms' => "Room {$rooms[$roomId]->room_name} capacity exceeded"
            ])->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 3: Create Booking
    |--------------------------------------------------------------------------
    */
    $leadGuest = Guest::find($request->guests[0]);

    $roomIds = array_values($request->rooms);

    $booking = Booking::create([
        'company_id' => auth()->user()->company_id,
        'slot_id' => $slot->id,
        'boat_id' => $slot->boat_id,
        'room_id' => $roomIds[0],
        'guest_name' => $leadGuest->name,
        'guest_count' => count($request->guests),
        'source' => $request->source,
        'agent_id' => $request->agent_id,
        'rate_plan_id' => $request->rate_plan_id,
        'payment_policy_id' => $request->payment_policy_id,
        'cancellation_policy_id' => $request->cancellation_policy_id,
        'notes' => $request->notes,
        'status' => $request->status,
        'price' => $request->price,
        'currency' => $request->currency,
        'salesperson_id' => $request->salesperson_id,
    ]);

    /*
    |--------------------------------------------------------------------------
    | STEP 4: Attach Relations
    |--------------------------------------------------------------------------
    */
    $booking->rooms()->sync($request->rooms);
    $booking->guests()->sync($request->guests);

    foreach ($request->guest_rooms as $guestId => $roomId) {
        BookingGuestRoom::create([
            'booking_id' => $booking->id,
            'guest_id' => $guestId,
            'room_id' => $roomId,
        ]);
    }

    return redirect()
        ->route('admin.bookings.index')
        ->with('success', 'Booking created successfully');
}

   public function edit(Booking $booking)
    {
        return view('admin.booking.edit', [
            'booking' => $booking->load(['rooms', 'guests', 'slot.boat.rooms']),
            'bookingRoomIds' => $booking->rooms->pluck('id'),
            'guestRoomMapping' => $booking->guestRoomAssignments()->pluck('room_id', 'guest_id'),
            'slots' => Slot::with('boat.rooms')->get(),
            'agents' => Agent::orderBy('first_name')->get(),
            'guests' => Guest::orderBy('name')->get(),
            'ratePlans' => RatePlan::with('rules')->get(),
            'paymentPolicies' => PaymentPolicy::all(),
            'cancellationPolicies' => CancellationPolicy::with('rules')->get(),
            'boats' => Boat::withCount('rooms')->get(),
            'regions' => Region::all(),
            'currencies' => Currency::all(), // fetch all currencies from DB
            'ports' => Port::all(),
            'salespersons' => Salesperson::orderBy('name')->get(),
            'companies' => auth()->user()->hasRole('admin')
                ? Company::all()
                : Company::where('id', auth()->user()->company_id)->get(),
        ]);
    }

    /**
     * Update the booking
     */
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'source' => 'required|in:Direct,Agent',
            'agent_id' => 'nullable|required_if:source,Agent',
            'rooms' => 'required|array',
            'guests' => 'array',
        ]);

        /*
        |--------------------------------------------------------------------------
        | STEP 1: Resolve Slot (existing OR inline-created)
        |--------------------------------------------------------------------------
        */
        if ($request->slot_id) {
            // Existing slot
            $slot = Slot::with('boat.rooms')->findOrFail($request->slot_id);
        } else {
            // Inline slot creation
            $request->validate([
                'boat_id' => 'required|exists:boats,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'region_id' => 'required|exists:regions,id',
                'embarkation_port_id' => 'required|exists:ports,id',
                'disembarkation_port_id' => 'required|exists:ports,id',
            ]);

            // Collision check
            $collision = Slot::where('boat_id', $request->boat_id)
                ->where(function ($q) use ($request) {
                    $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
                })->exists();

            if ($collision) {
                return back()->withErrors([
                    'start_date' => 'Slot collision detected for this boat'
                ])->withInput();
            }

            // Create slot
            $slot = Slot::create([
                'boat_id' => $request->boat_id,
                'region_id' => $request->region_id,
                'embarkation_port_id' => $request->embarkation_port_id,
                'disembarkation_port_id' => $request->disembarkation_port_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'Available',
            ]);
            $slot->load('boat.rooms');
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2: Capacity validation
        |--------------------------------------------------------------------------
        */
        $rooms = Room::whereIn('id', $request->rooms)->get();
        $maxCapacity = $rooms->sum(fn ($r) => $r->capacity + $r->extra_bed_capacity);
        $guestCount = count($request->guests ?? []);

        if ($guestCount > $maxCapacity) {
            return back()->withErrors([
                'guests' => "Guest count exceeds room capacity ({$maxCapacity})"
            ])->withInput();
        }

        $leadGuest = Guest::find($request->guests[0] ?? null);

        /*
        |--------------------------------------------------------------------------
        | STEP 3: Update Booking
        |--------------------------------------------------------------------------
        */
        $booking->update([
            'company_id' => auth()->user()->company_id,
            'slot_id' => $slot->id,
            'boat_id' => $slot->boat_id,
            'room_id' => $request->rooms[0],
            'guest_name' => $leadGuest?->name,
            'guest_count' => $guestCount,
            'source' => $request->source,
            'agent_id' => $request->agent_id,
            'rate_plan_id' => $request->rate_plan_id,
            'payment_policy_id' => $request->payment_policy_id,
            'cancellation_policy_id' => $request->cancellation_policy_id,
            'notes' => $request->notes,
            'status' => $request->status, // keep current status
            'price' => $request->price,
            'currency' => $request->currency,
            'salesperson_id' => $request->salesperson_id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | STEP 4: Sync Rooms & Guests
        |--------------------------------------------------------------------------
        */
        $booking->rooms()->sync($request->rooms);
        $booking->guests()->sync($request->guests ?? []);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking updated successfully');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('success','Booking deleted successfully.');
    }
}
