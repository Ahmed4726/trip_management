<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    Agent, Booking, Slot, Boat, BookingGuestRoom,
    CancellationPolicy, Company, Currency, Guest,
    PaymentPolicy, Port, RatePlan, Region, Room, Salesperson
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::query()
            ->with([
                'slot',
                'boat',
                'boats',
                'room',
                'rooms',
            ])
            ->select('bookings.*')
            ->distinct()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.booking.index', compact('bookings'));
    }

public function create()
{
    $slots = Slot::with(['boat.rooms', 'boats.rooms'])->get();

    // Slots already booked by PRIVATE CHARTER only
    $bookedSlotIds = Booking::leftJoin('slots', 'bookings.slot_id', '=', 'slots.id')
        ->whereNotNull('bookings.slot_id')
        ->where('slots.slot_type', 'Private Charter')
        ->pluck('bookings.slot_id')
        ->unique()
        ->toArray();

    // Room usage per slot (per booking) including guest IDs
    $roomUsageBySlot = [];

    foreach ($slots as $slot) {
        // Only active bookings (exclude cancelled)
        $bookingIds = Booking::where('slot_id', $slot->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('id');

        // Room usage counts per room
        $roomUsage = BookingGuestRoom::whereIn('booking_id', $bookingIds)
            ->select('room_id', DB::raw('COUNT(*) as used'))
            ->groupBy('room_id')
            ->pluck('used', 'room_id')
            ->toArray();

        // Guest IDs per room per booking
        $roomGuests = BookingGuestRoom::whereIn('booking_id', $bookingIds)
            ->get()
            ->groupBy(function ($item) {
                return $item->booking_id . '_' . $item->room_id;
            })
            ->map(function ($items) {
                return $items->pluck('guest_id')->toArray();
            })
            ->toArray();

        // Initialize room usage for this slot
        $roomUsageBySlot[$slot->id] = [];

        // Process rooms for main boat
        foreach ($slot->boat->rooms ?? [] as $room) {
            $roomId = $room->id;
            $roomUsageBySlot[$slot->id][$roomId] = [
                'used' => $roomUsage[$roomId] ?? 0,
                'guests' => [],
            ];

            // Merge guest IDs per booking
            foreach ($bookingIds as $bookingId) {
                $key = $bookingId . '_' . $roomId;
                if (isset($roomGuests[$key])) {
                    $roomUsageBySlot[$slot->id][$roomId]['guests'] = array_merge(
                        $roomUsageBySlot[$slot->id][$roomId]['guests'],
                        $roomGuests[$key]
                    );
                }
            }
        }

        // Process rooms for any additional boats linked to the slot
        if ($slot->boats) {
            foreach ($slot->boats as $boat) {
                foreach ($boat->rooms ?? [] as $room) {
                    $roomId = $room->id;
                    $roomUsageBySlot[$slot->id][$roomId] = [
                        'used' => $roomUsage[$roomId] ?? 0,
                        'guests' => [],
                    ];

                    foreach ($bookingIds as $bookingId) {
                        $key = $bookingId . '_' . $roomId;
                        if (isset($roomGuests[$key])) {
                            $roomUsageBySlot[$slot->id][$roomId]['guests'] = array_merge(
                                $roomUsageBySlot[$slot->id][$roomId]['guests'],
                                $roomGuests[$key]
                            );
                        }
                    }
                }
            }
        }
    }

    return view('admin.booking.create', [
        'slots' => $slots->toArray(),
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

        'bookedSlotIds' => $bookedSlotIds,
        'roomUsageBySlot' => $roomUsageBySlot,
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

        DB::beginTransaction();

        try {

            // ------------------------------
            // STEP 1: RESOLVE SLOT
            // ------------------------------

                $slot = Slot::with(['boat.rooms', 'boats.rooms'])
                    ->lockForUpdate()
                    ->findOrFail($request->slot_id);

                $slotType = $slot->slot_type ?? 'Open Trip';

                // ❌ Private Charter → only ONE booking
                if ($slotType === 'Private Charter'
                    && Booking::where('slot_id', $slot->id)->exists()) {
                    DB::rollBack();
                    return back()->withErrors([
                        'slot_id' => 'This Private Charter slot is already booked.'
                    ])->withInput();
                }



            // ------------------------------
            // STEP 2: SLOT-LEVEL ROOM CAPACITY VALIDATION
            // ------------------------------
            $usedRoomCapacity =
                BookingGuestRoom::whereIn(
                    'booking_id',
                    Booking::where('slot_id', $slot->id)->pluck('id')
                )
                ->select('room_id', DB::raw('COUNT(*) as used'))
                ->groupBy('room_id')
                ->pluck('used', 'room_id')
                ->toArray();

            if ($request->filled('guest_rooms')) {

                $rooms = Room::whereIn('id', array_keys($request->guest_rooms))
                    ->get()
                    ->keyBy('id');

                foreach ($request->guest_rooms as $roomId => $guestIds) {

                    $guestIds = array_filter((array) $guestIds);
                    if (empty($guestIds)) {
                        continue;
                    }

                    if (!isset($rooms[$roomId])) {
                        DB::rollBack();
                        return back()->withErrors([
                            'guest_rooms' => 'Invalid room selected'
                        ])->withInput();
                    }

                    $room = $rooms[$roomId];
                    $capacity = $room->capacity + $room->extra_beds;
                    $alreadyUsed = $usedRoomCapacity[$roomId] ?? 0;
                }
            }

            // ------------------------------
            // STEP 4: COLLECT ALL GUEST IDS
            // ------------------------------
            $guestIds = collect($request->guest_rooms ?? [])
                ->flatten()
                ->merge($request->guests_without_room ?? [])
                ->unique()
                ->values()
                ->toArray();

            if (empty($guestIds)) {
                DB::rollBack();
                return back()->withErrors([
                    'guest_rooms' => 'At least one guest is required'
                ])->withInput();
            }

            $leadGuest = Guest::find($guestIds[0]);
            if (!$leadGuest) {
                DB::rollBack();
                return back()->withErrors([
                    'guest_rooms' => 'Invalid guest selected'
                ])->withInput();
            }

            // ------------------------------
            // STEP 5: CREATE BOOKING
            // ------------------------------
            $booking = Booking::create([
                'slot_id' => $slot->id,
                'boat_id' => $slot->boat_id,
                'room_id' => $slotType === 'Private Charter'
                    ? null
                    : array_key_first($request->guest_rooms ?? []),
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
            // STEP 6: ATTACH ROOMS & GUESTS
            // ------------------------------
            if ($request->filled('guest_rooms')) {

                $booking->rooms()->sync(array_keys($request->guest_rooms));

                foreach ($request->guest_rooms as $roomId => $gIds) {
                    foreach ((array) $gIds as $guestId) {
                        BookingGuestRoom::create([
                            'booking_id' => $booking->id,
                            'room_id' => $roomId,
                            'guest_id' => $guestId,
                        ]);
                    }
                }
            }

            if (!empty($request->guests_without_room)) {
                $booking->guests()->syncWithoutDetaching($request->guests_without_room);
            }

            // ------------------------------
            // STEP 7: ATTACH BOATS
            // ------------------------------
            $boatsToAttach = $request->slot_id
                ? ($slot->boats && $slot->boats->count()
                    ? $slot->boats->pluck('id')->toArray()
                    : [$slot->boat_id])
                : ($request->boat_id ? [$request->boat_id] : []);

            if ($boatsToAttach) {
                $booking->boats()->sync($boatsToAttach);
            }

            // ------------------------------
            // STEP 8: SLOT STATUS
            // ------------------------------
            if ($slotType === 'Private Charter') {
                $slot->update(['status' => 'Booked']);
            }

            DB::commit();

            return redirect()
                ->route('admin.bookings.index')
                ->with('success', 'Booking created successfully');

        } catch (\Throwable $e) {

            DB::rollBack();
            report($e);

            return back()->withErrors([
                'error' => 'Something went wrong while creating the booking.'
            ])->withInput();
        }
    }



    public function edit(Booking $booking)
    {
        $booking->load([
            'rooms',
            'guests',
            'boats',
            'slot.boat.rooms',
            'slot.boats.rooms'
        ]);

        $bookingRoomGuests = BookingGuestRoom::where('booking_id', $booking->id)
            ->get()
            ->groupBy('room_id')
            ->map(fn($items) => $items->pluck('guest_id')->toArray())
            ->toArray();

        $slot = $booking->slot ? $booking->slot->toArray() : null;

        // Booked slots except current booking slot
        $bookedSlotIds = Booking::whereNotNull('slot_id')
            ->where('id', '!=', $booking->id)
            ->pluck('slot_id')
            ->unique()
            ->toArray();

        $slots = Slot::with(['boat.rooms', 'boats.rooms'])->get()->toArray();

        return view('admin.booking.edit', [
            'booking' => $booking,
            'bookingRoomGuests' => $bookingRoomGuests,
            'slots' => $slots,
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
            'slot' => $slot,
            'bookedSlotIds' => $bookedSlotIds,
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
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

        $slot = Slot::with('boat.rooms', 'boats.rooms')->findOrFail($request->slot_id);

            // Prevent duplicate booking
        if (Booking::where('slot_id', $slot->id)->where('id', '!=', $booking->id)->exists()) {
                return back()->withErrors([
                'slot_id' => 'This slot is already booked.'
            ])->withInput();
        }


        // Room validation + guest collection (same as store)
        $roomGuestCounts = [];
        if ($request->guest_rooms) {
            foreach ($request->guest_rooms as $roomId => $guestIds) {
                $guestIds = is_array($guestIds) ? $guestIds : [$guestIds];
                $roomGuestCounts[$roomId] = count($guestIds);
            }

            $rooms = Room::whereIn('id', array_keys($roomGuestCounts))->get()->keyBy('id');
            foreach ($roomGuestCounts as $roomId => $count) {
                $capacity = $rooms[$roomId]->capacity + $rooms[$roomId]->extra_beds;
                if ($count > $capacity) {
                    return back()->withErrors([
                        'guest_rooms' => "Room {$rooms[$roomId]->room_name} capacity exceeded"
                    ])->withInput();
                }
            }
        }

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
            $boatsToAttach = !empty($slot->boats) && $slot->boats->count() > 0
                ? $slot->boats->pluck('id')->toArray()
                : ($slot->boat_id ? [$slot->boat_id] : []);
        } else {
            $boatsToAttach = $request->boat_id ? [$request->boat_id] : [];
        }

        if (!empty($boatsToAttach)) {
            $booking->boats()->sync($boatsToAttach);
        }

        // Mark slot as booked
        $slot->update(['status' => 'Booked']);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking updated successfully');
    }

    public function destroy(Booking $booking)
    {
        // Prevent deletion if slot already started
        // if ($booking->slot && Carbon::parse($booking->slot->start_date)->isPast()) {
        //     return back()->withErrors(['error' => 'Cannot delete booking because the slot has already started.']);
        // }

        $booking->delete();
        return back()->with('success', 'Booking deleted successfully.');
    }
}
