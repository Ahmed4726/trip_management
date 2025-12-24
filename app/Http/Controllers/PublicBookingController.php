<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Booking;
use App\Models\Company;
use Illuminate\Http\Request;

class PublicBookingController extends Controller
{
    // Show the iframe widget (calendar or list view)
public function widget(Request $request)
{
    $tripId  = $request->query('trip');
    $company = $request->query('company');
    $boatSlug = $request->query('boat');

    $trip = Trip::with([
        'paymentPolicy',
        'cancellationPolicy.rules'
    ])->findOrFail($tripId);

    $company_id = Company::where('name', $company)->first();

    // If trip has a boat relation or column, get the name
    $boatName = $trip->boat_name ?? ($trip->boat['name'] ?? $boatSlug ?? 'N/A');

    return view('public.widget', compact('trip', 'company', 'boatName', 'company_id','tripId'));
}


    // Fetch single availability details
    public function availability($id)
    {
        $trip = Trip::findOrFail($id);
        return response()->json([
            'id' => $trip->id,
            'boat' => $trip->boat,
            'title' => $trip->title,
            'start_date' => $trip->start_date,
            'end_date' => $trip->end_date,
            'trip_type' => $trip->trip_type,
            'status' => $trip->status,
            'available_rooms' => $trip->guests, // you can adjust this
        ]);
    }

    // Handle prebooking submission
    public function prebooking(Request $request)
    {
        $request->validate([
            'company' => 'required',
            'availability_id' => 'required|exists:trips,id',
            'trip_type' => 'required',
            'rooms' => 'required|array',
            'lead_guest.name' => 'required|string',
            'lead_guest.email' => 'required|email',
            'lead_guest.phone' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $trip = Trip::findOrFail($request->availability_id);

        $booking = Booking::create([
            'company_id' => $request->company,
            'trip_id' => $trip->id,
            'status' => 'pre_booking',
            'trip_type' => $request->trip_type,
            'rooms' => $request->rooms,
            'lead_guest' => $request->lead_guest,
            'notes' => $request->notes,
            'source' => $request->source ?? 'direct'
        ]);

        return response()->json([
            'success' => true,
            'booking_id' => $booking->id
        ]);
    }
}
