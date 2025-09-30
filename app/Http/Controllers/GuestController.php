<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Trip;
use App\Models\Guest;
use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class GuestController extends Controller
{
    /**
     * List all guests for admin or company users.
     */
    public function guest_index()
    {
        $query = Guest::with('trip');

        // Restrict by company if not admin
        if (!auth()->user()->hasRole('admin')) {
            $query->whereHas('trip', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            });
        }

        $guests = $query->get();

        return view('guests.index', compact('guests'));
    }

    /**
     * Store a new guest (Admin adding manually).
     */
    public function store(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email',
            // Add other validation rules as needed
        ]);

        $trip = Trip::findOrFail($request->trip_id);

        // Restrict by company if not admin
        if (!auth()->user()->hasRole('admin') && $trip->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $guest = $trip->guests()->create($request->only([
            'name','gender','email','dob','passport','nationality',
            'cabin','surfLevel','boardDetails',
            'arrivalFlightDate','arrivalFlightNumber','arrivalAirport','arrivalTime','hotelPickup',
            'departureFlightDate','departureFlightNumber','departureAirport','departureTime',
            'medicalDietary','specialRequests','insuranceName','policyNumber',
            'emergencyName','emergencyRelation','emergencyPhone','guestWhatsapp','guestEmail'
        ]));

        // Handle uploads
        foreach (['image', 'pdf', 'video'] as $file) {
            if ($request->hasFile($file)) {
                $path = $request->file($file)->store("guests/{$file}s", 'public');
                $guest->update(["{$file}_path" => $path]);
            }
        }

        // Add other guests (companions)
        if ($request->has('guest_name')) {
            foreach ($request->guest_name as $i => $name) {
                $guest->otherGuests()->create([
                    'name'         => $name ?? null,
                    'gender'       => $request->guest_gender[$i] ?? null,
                    'email'        => $request->guest_email[$i] ?? null,
                    'dob'          => $request->guest_dob[$i] ?? null,
                    'passport'     => $request->guest_passport[$i] ?? null,
                    'nationality'  => $request->guest_nationality[$i] ?? null,
                    'cabin'        => $request->guest_cabin[$i] ?? null,
                    'surfLevel'    => $request->guest_surf[$i] ?? null,
                    'boardDetails' => $request->guest_board[$i] ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Guest form submitted successfully.');
    }

    /**
     * Public guest form (via booking token).
     */
    public function show($token)
    {
        $query = Booking::where('token', $token);

        if (!auth()->user()->hasRole('admin')) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $booking = $query->firstOrFail();

        return view('guests.guest_form', compact('booking'));
    }

    /**
     * Submit guest info (via trip token).
     */
    public function submit(Request $request, $token)
    {
        $trip = Trip::where('guest_form_token', $token);

        if (!auth()->user()->hasRole('admin')) {
            $trip->where('company_id', auth()->user()->company_id);
        }

        $trip = $trip->firstOrFail();

        $trip->guests()->create($request->only(['name','email'])); // Add other fields as needed

        return redirect()->back()->with('success', 'Guest info submitted!');
    }

    /**
     * Admin side: view guest detail.
     */
    public function show_guest($id)
    {
        $query = Guest::with(['trip','booking'])->where('id', $id);

        if (!auth()->user()->hasRole('admin')) {
            $query->whereHas('trip', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            });
        }

        $guest = $query->firstOrFail();

        return view('guests.detail', compact('guest'));
    }

    /**
     * Download guest PDF.
     */
    public function download_pdf($id)
    {
        $query = Guest::with('trip')->where('id', $id);

        if (!auth()->user()->hasRole('admin')) {
            $query->whereHas('trip', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            });
        }

        $guest = $query->firstOrFail();

        $pdf = PDF::loadView('guests.pdf.view', compact('guest'));
        return $pdf->download('guest-' . $guest->id . '.pdf');
    }
}
