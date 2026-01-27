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
        $bookings = Booking::query()
            ->with([
                'slot',
                'boat',        // single boat (legacy)
                'boats',       // multiple boats
                'room',        // single room (legacy)
                'rooms',       // multiple rooms
            ])
            ->select('bookings.*')   // 🔑 VERY IMPORTANT
            ->distinct()             // 🔑 PREVENT DUPLICATES
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.booking.index', compact('bookings'));
    }
    /**
     * Show the form for creating a new booking.
     */

    public function create()
    {
        return view('admin.booking.create', [
            'slots' => Slot::with(['boat.rooms', 'boats.rooms'])->get()->toArray(),
            'agents' => Agent::orderBy('first_name')->get(),
            'guests' => Guest::orderBy('name')->get(),

            'ratePlans' => RatePlan::with('rules')->get(),
            'paymentPolicies' => PaymentPolicy::all(),
            'cancellationPolicies' => CancellationPolicy::with('rules')->get(),

            'boats' => Boat::with('rooms')->get(),

            'regions' => Region::all(),
            'ports' => Port::all(),
            'salespersons' => Salesperson::orderBy('name')->get(),
            'currencies' => Currency::all(),

            'companies' => auth()->user()->hasRole('admin')
                ? Company::all()
                : Company::where('id', auth()->user()->company_id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        // ------------------------------
        // VALIDATION
        // ------------------------------
        $validator = Validator::make($request->all(), [
            'source' => 'required|in:Direct,Agent',
            'agent_id' => 'nullable|required_if:source,Agent',
            'guest_rooms' => 'nullable|array', // optional for Private Charter
            'guests_without_room' => 'nullable|array', // optional
            'price' => 'required|numeric',
            'currency' => 'required',
            'salesperson_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // ------------------------------
        // STEP 1: RESOLVE SLOT
        // ------------------------------
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
                return back()->withErrors(['start_date' => 'Slot collision detected'])->withInput();
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

        // ------------------------------
        // STEP 2: ROOM CAPACITY VALIDATION
        // ------------------------------
        $roomGuestCounts = [];

        if ($request->guest_rooms) {
            foreach ($request->guest_rooms as $roomId => $guestIds) {
                $guestIds = is_array($guestIds) ? $guestIds : [$guestIds];
                $roomGuestCounts[$roomId] = count($guestIds);
            }

            $rooms = Room::whereIn('id', array_keys($roomGuestCounts))
                ->get()
                ->keyBy('id');

            foreach ($roomGuestCounts as $roomId => $count) {
                if (!isset($rooms[$roomId])) {
                    return back()->withErrors([
                        'guest_rooms' => 'Invalid room selected'
                    ])->withInput();
                }

                $capacity = $rooms[$roomId]->capacity + $rooms[$roomId]->extra_beds;

                if ($count > $capacity) {
                    return back()->withErrors([
                        'guest_rooms' => "Room {$rooms[$roomId]->room_name} capacity exceeded"
                    ])->withInput();
                }
            }
        }

        // ------------------------------
        // STEP 3: COLLECT ALL GUEST IDS
        // ------------------------------
        $guestIds = collect($request->guest_rooms ?? [])
            ->flatten()
            ->merge($request->guests_without_room ?? [])
            ->unique()
            ->values()
            ->toArray();

        if (empty($guestIds)) {
            return back()->withErrors([
                'guest_rooms' => 'At least one guest is required'
            ])->withInput();
        }

        $leadGuest = Guest::find($guestIds[0]);

        if (!$leadGuest) {
            return back()->withErrors([
                'guest_rooms' => 'Invalid guest selected'
            ])->withInput();
        }

        // ------------------------------
        // STEP 4: CREATE BOOKING
        // ------------------------------
        $booking = Booking::create([
            'slot_id' => $slot->id,
            'boat_id' => $slot->boat_id,
            'room_id' => $request->guest_rooms ? array_key_first($request->guest_rooms) : null, // primary room optional
            'guest_name' => $leadGuest->name,
            'guest_count' => count($guestIds),
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
            'price_usd' => $request->price_usd,
        ]);

        // ------------------------------
        // STEP 5: ATTACH ROOMS & GUESTS
        // ------------------------------
        if ($request->guest_rooms) {
            $booking->rooms()->sync(array_keys($request->guest_rooms));

            foreach ($request->guest_rooms as $roomId => $gIds) {
                foreach ((array)$gIds as $guestId) {
                    BookingGuestRoom::create([
                        'booking_id' => $booking->id,
                        'room_id' => $roomId,
                        'guest_id' => $guestId,
                    ]);
                }
            }
        }

        // Attach guests without rooms
        if (!empty($request->guests_without_room)) {
            $booking->guests()->syncWithoutDetaching($request->guests_without_room);
        }

        // Determine boats to attach
        if ($request->slot_id) {
            // Slot selected, attach all boats in slot
            if (!empty($slot->boats) && $slot->boats->count() > 0) {
                $boatsToAttach = $slot->boats->pluck('id')->toArray();
            } else {
                $boatsToAttach = $slot->boat_id ? [$slot->boat_id] : [];
            }
        } else {
            // Inline booking, attach the selected boat
            $boatsToAttach = $request->boat_id ? [$request->boat_id] : [];
        }

        if (!empty($boatsToAttach)) {
            $booking->boats()->sync($boatsToAttach);
        }


        // ------------------------------
        // DONE
        // ------------------------------
        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking created successfully');
    }



 public function edit(Booking $booking)
{
    $booking->load(['rooms', 'guests', 'boats', 'slot.boats.rooms']);

    // Map guest IDs per room for JS
    $bookingRoomGuests = BookingGuestRoom::where('booking_id', $booking->id)
        ->get()
        ->groupBy('room_id')
        ->map(fn($items) => $items->pluck('guest_id')->toArray())
        ->toArray();

    return view('admin.booking.edit', [
        'booking' => $booking,
        'bookingRoomGuests' => $bookingRoomGuests,
        'slots' => Slot::with(['boat.rooms', 'boats.rooms'])->get()->toArray(),
        'agents' => Agent::orderBy('first_name')->get(),
        'guests' => Guest::orderBy('name')->get(),
        'ratePlans' => RatePlan::with('rules')->get(),
        'paymentPolicies' => PaymentPolicy::all(),
        'cancellationPolicies' => CancellationPolicy::with('rules')->get(),
        'boats' => Boat::with('rooms')->get(),
        'regions' => Region::all(),
        'ports' => Port::all(),
        'salespersons' => Salesperson::orderBy('name')->get(),
        'currencies' => Currency::all(),
        'companies' => auth()->user()->hasRole('admin')
            ? Company::all()
            : Company::where('id', auth()->user()->company_id)->get(),
    ]);
}


    public function update(Request $request, Booking $booking)
    {
        dd($request->all());
        $validator = Validator::make($request->all(), [
            'source' => 'required|in:Direct,Agent',
            'agent_id' => 'nullable|required_if:source,Agent',
            'guest_rooms' => 'nullable|array',
            'guests_without_room' => 'nullable|array',
            'price' => 'required|numeric',
            'currency' => 'required',
            'salesperson_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Resolve slot (existing or inline)
        if ($request->slot_id) {
            $slot = Slot::with('boat.rooms')->findOrFail($request->slot_id);
        } else {
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

        // Room capacity validation
        $roomGuestCounts = [];
        if ($request->guest_rooms) {
            foreach ($request->guest_rooms as $roomId => $guestIds) {
                $guestIds = is_array($guestIds) ? $guestIds : [$guestIds];
                $roomGuestCounts[$roomId] = count($guestIds);
            }

            $rooms = Room::whereIn('id', array_keys($roomGuestCounts))
                ->get()
                ->keyBy('id');

            foreach ($roomGuestCounts as $roomId => $count) {
                $capacity = $rooms[$roomId]->capacity + $rooms[$roomId]->extra_beds;
                if ($count > $capacity) {
                    return back()->withErrors([
                        'guest_rooms' => "Room {$rooms[$roomId]->room_name} capacity exceeded"
                    ])->withInput();
                }
            }
        }

        // Collect all guest IDs
        $guestIds = collect($request->guest_rooms ?? [])
            ->flatten()
            ->merge($request->guests_without_room ?? [])
            ->unique()
            ->values()
            ->toArray();

        if (empty($guestIds)) {
            return back()->withErrors(['guest_rooms' => 'At least one guest is required'])->withInput();
        }

        $leadGuest = Guest::find($guestIds[0]);
        if (!$leadGuest) {
            return back()->withErrors(['guest_rooms' => 'Invalid guest selected'])->withInput();
        }

        // Update booking
        $booking->update([
            'slot_id' => $slot->id,
            'boat_id' => $slot->boat_id,
            'room_id' => $request->guest_rooms ? array_key_first($request->guest_rooms) : null,
            'guest_name' => $leadGuest->name,
            'guest_count' => count($guestIds),
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
            'price_usd' => $request->price_usd,
        ]);

        // Sync rooms & guests
        $booking->rooms()->sync(array_keys($request->guest_rooms ?? []));
        BookingGuestRoom::where('booking_id', $booking->id)->delete();
        if ($request->guest_rooms) {
            foreach ($request->guest_rooms as $roomId => $gIds) {
                foreach ((array)$gIds as $guestId) {
                    BookingGuestRoom::create([
                        'booking_id' => $booking->id,
                        'room_id' => $roomId,
                        'guest_id' => $guestId,
                    ]);
                }
            }
        }

        if (!empty($request->guests_without_room)) {
            $booking->guests()->sync($request->guests_without_room);
        }

        // Attach boats
        if ($request->slot_id) {
            $boatsToAttach = $slot->boats->pluck('id')->toArray();
        } else {
            $boatsToAttach = $request->boat_id ? [$request->boat_id] : [];
        }
        if (!empty($boatsToAttach)) {
            $booking->boats()->sync($boatsToAttach);
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking updated successfully');
    }

    /**
     * Delete the booking
     */

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('success','Booking deleted successfully.');
    }
}
