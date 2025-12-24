<?php

namespace App\Http\Controllers;

use App\Models\CancellationPolicy;
use App\Models\Boat;
use App\Models\Agent;
use App\Models\Company;
use App\Models\PaymentPolicy;
use App\Models\RatePlan;
use App\Models\Trip;
use App\Models\Booking;
use Illuminate\Http\Request;

class TripController extends Controller
{
    // Trips list
    public function trip_index()
    {
        $query = Trip::query();

        if (!auth()->user()->hasRole('admin')) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $trips = $query->get();
        $agents = Agent::all();
        $tripTypes = Trip::select('trip_type')->distinct()->pluck('trip_type');

        $boats = Boat::withCount('rooms')->get(); // add rooms_count for display

        return view('admin.trips.index', compact('trips','agents','tripTypes','boats'));
    }

public function create_trip()
{
    $company_id = null;

    if (auth()->user()->hasRole('admin')) {
        $companies = Company::all();
    } else {
        $companies = null;
        $company_id = auth()->user()->company_id;
    }

    // Fetch data filtered by company_id
    $agents = $company_id ? Agent::where('company_id', $company_id)->get() : Agent::all();
    $ratePlans = $company_id ? RatePlan::where('company_id', $company_id)->get() : RatePlan::all();
    $paymentPolicies = $company_id ? PaymentPolicy::where('company_id', $company_id)->get() : PaymentPolicy::all();
    $cancellationPolicies = $company_id ? CancellationPolicy::where('company_id', $company_id)->get() : CancellationPolicy::all();

    $boats = Boat::withCount('rooms')->get(); // add rooms_count for display

    return view('admin.trips.create', compact(
        'agents',
        'ratePlans',
        'paymentPolicies',
        'cancellationPolicies',
        'companies',
        'company_id',
        'boats'
    ));
}


    // Filtering for calendar resources
public function filter(Request $request)
{
    $query = Trip::query()->with('bookings');

    if (!auth()->user()->hasRole('admin')) {
        $query->where('company_id', auth()->user()->company_id);
    }

    if ($request->boat) $query->where('boat_id', $request->boat); // Assuming you have boat_id column
    if ($request->status) $query->where('status', $request->status);
    if ($request->start_date) $query->whereDate('start_date', '>=', $request->start_date);
    if ($request->end_date) $query->whereDate('end_date', '<=', $request->end_date);

    $trips = $query->get();

    // Fetch boats dynamically from trips
    $boats = $trips->pluck('boat')->unique()->map(fn($boat) => [
        'id' => 'boat-' . $boat->id,
        'title' => $boat->name . ' (' . ($boat->rooms_count ?? $boat->rooms->count()) . ' rooms)'
    ])->values();

    $events = [];
    foreach ($trips as $trip) {
        $events[] = [
            'id' => 'trip-' . $trip->id,
            'resourceId' => 'boat-' . $trip->boat->id,
            'title' => $trip->title,
            'start' => $trip->start_date,
            'end' => $trip->end_date,
            'color' => match($trip->status) {
                'draft' => '#6c757d',
                'published' => '#007bff',
                'active' => '#28a745',
                'completed' => '#20c997',
                'cancelled' => '#dc3545',
                default => '#17a2b8',
            },
            'extendedProps' => [
                'trip_id' => $trip->id,
                'status' => $trip->status,
                'occupancy' => $trip->occupancy_percent ?? 0,
            ]
        ];

        foreach ($trip->bookings as $booking) {
            $events[] = [
                'id' => 'booking-' . $booking->id,
                'resourceId' => 'boat-' . $trip->boat->id,
                'title' => 'Booking #' . $booking->id,
                'start' => $booking->start_date,
                'end' => $booking->end_date,
                'display' => 'list-item',
                'color' => match($booking->status) {
                    'pre_booking' => '#ffc107',
                    'confirmed' => '#28a745',
                    'active' => '#17a2b8',
                    'completed' => '#20c997',
                    'cancelled' => '#dc3545',
                    default => '#6c757d',
                },
                'extendedProps' => [
                    'status' => $booking->status,
                    'lead_guest' => $booking->lead_guest,
                    'rooms' => $booking->rooms,
                    'pax' => $booking->pax,
                    'dp_status' => $booking->dp_status,
                ]
            ];
        }
    }

    return response()->json([
        'resources' => $boats,
        'events' => $events
    ]);
}


    // Events API
public function events(Request $request)
{
    // Join boats table to get boat name
    $query = Trip::query()
        ->join('boats', 'trips.boat_id', '=', 'boats.id')
        ->select('trips.*', 'boats.name as boat_name');

    if (!auth()->user()->hasRole('admin')) {
        $query->where('trips.company_id', auth()->user()->company_id);
    }

    if ($request->filled('boat')) {
        $query->where('trips.boat_id', $request->boat);
    }
    if ($request->filled('status')) {
        $query->where('trips.status', $request->status);
    }
    if ($request->filled('start_date')) {
        $query->whereDate('trips.start_date', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $query->whereDate('trips.end_date', '<=', $request->end_date);
    }

    $trips = $query->get();

    $events = [];

    foreach ($trips as $trip) {
        $events[] = [
            't_id' => $trip->id,
            'id' => 'trip-' . $trip->id,
            'resourceId' => 'boat-' . $trip->boat_id, // use boat_id
            'title' => $trip->title,
            'start' => $trip->start_date,
            'end' => $trip->end_date,
            'status' => $trip->status,
            'region' => $trip->region,
            'trip_type' => $trip->trip_type,
            'guests' => $trip->guests,
            'price' => $trip->price,
            'notes' => $trip->notes,
            'extendedProps' => [
                'trip_id' => $trip->id,
                'boat' => $trip->boat_name ?? $trip->boat, // string
            ]
        ];
    }

    return response()->json($events);
}



    // Store new trip
    public function store_trip(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'status' => 'required|string',
            'trip_type' => 'required|string',
            'boat_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'guests' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'rate_plan_id' => 'required|exists:rate_plans,id',
            'payment_policy_id' => 'required|exists:payment_policies,id',
            'cancellation_policy_id' => 'required|exists:cancellation_policies,id',
            'notes' => 'nullable|string',
            'company_id' => 'required'
        ]);

        if (!auth()->user()->hasRole('admin')) {
            $validated['company_id'] = auth()->user()->company_id;
        }

        $trip = Trip::create($validated);

        $paymentPolicy = PaymentPolicy::find($request->payment_policy_id);

        $total = $request->price;
        $dp_amount = round($total * $paymentPolicy->dp_percent / 100, 2);
        $balance_due_date = now()->parse($request->start_date)
            ->subDays($paymentPolicy->balance_days_before_start);

        $trip->update([
            'pricing_snapshot_json' => json_encode([
                'total' => $total,
                'dp_amount' => $dp_amount
            ]),
            'payment_policy_snapshot_json' => json_encode($paymentPolicy),
            'cancellation_policy_snapshot_json' => json_encode(
                CancellationPolicy::find($request->cancellation_policy_id)
            ),
            'dp_amount' => $dp_amount,
            'balance_due_date' => $balance_due_date,
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip created successfully.');
    }

    public function show($id)
    {
        $trip = Trip::with(['agent', 'guestList.otherGuests'])->findOrFail($id);
        return view('admin.trips.detail', compact('trip'));
    }

    public function update_trip(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        $trip->update([
            'title'            => $request->title,
            'region'           => $request->region,
            'status'           => $request->status,
            // 'trip_type'        => $request->trip_type,   
            'leading_guest_id' => $request->leading_guest_id,
            'notes'            => $request->notes,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'guests'           => $request->guests,
            'price'            => $request->price,
            'boat'             => $request->boat,
            'agent_id'         => $request->agent_id,
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip updated successfully.');
    }

    public function destroy_trip($id)
    {
        $trip = Trip::findOrFail($id);
        $trip->delete();

        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully.');
    }

    public function getRooms($tripId)
    {
        $trip = Trip::findOrFail($tripId);

        preg_match('/\((\d+)\s*rooms?\)/i', $trip->boat, $matches);
        $totalRooms = isset($matches[1]) ? (int)$matches[1] : 0;

        $booked = Booking::where('trip_id', $tripId)->pluck('guests')->toArray();

        $availableRooms = array_values(array_diff(range(1, $totalRooms), $booked));

        return response()->json([
            'rooms' => $availableRooms
        ]);
    }
}
