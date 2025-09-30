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
     * List all guests for the current tenant (Admin side).
     */
    public function guest_index()
    {
        $company = app('tenant');

        $guests = Guest::whereHas('trip', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->get();

        return view('guests.index', compact('guests'));
    }

    /**
     * Store a new guest (Admin adding manually).
     */
    public function store(Request $request)
    {
        $company = app('tenant');

        // Verify the trip belongs to current tenant
        $trip = Trip::where('company_id', $company->id)
            ->findOrFail($request->trip_id);

        $guest = $trip->guests()->create($request->only([
            'name','gender','email','dob','passport','nationality',
            'cabin','surfLevel','boardDetails',
            'arrivalFlightDate','arrivalFlightNumber','arrivalAirport','arrivalTime','hotelPickup',
            'departureFlightDate','departureFlightNumber','departureAirport','departureTime',
            'medicalDietary','specialRequests','insuranceName','policyNumber',
            'emergencyName','emergencyRelation','emergencyPhone','guestWhatsapp','guestEmail'
        ]));

        // Handle uploads
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('guests/images', 'public');
            $guest->update(['image_path' => $imagePath]);
        }

        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('guests/pdfs', 'public');
            $guest->update(['pdf_path' => $pdfPath]);
        }

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('guests/videos', 'public');
            $guest->update(['video_path' => $videoPath]);
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
     * Public guest form (via booking token, scoped to tenant).
     */
    public function show($token)
    {
        $company = app('tenant');

        $booking = Booking::where('token', $token)
            ->where('company_id', $company->id)
            ->firstOrFail();

        return view('guests.guest_form', compact('booking'));
    }

    /**
     * Submit guest info (via trip token, scoped to tenant).
     */
    public function submit(Request $request, $token)
    {
        $company = app('tenant');

        $trip = Trip::where('guest_form_token', $token)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $trip->guests()->create([
            'name'  => $request->name,
            'email' => $request->email,
            // other guest fields here...
        ]);

        return redirect()->back()->with('success', 'Guest info submitted!');
    }

    /**
     * Admin side: view guest detail (scoped to tenant).
     */
    public function show_guest($id)
    {
        $company = app('tenant');

        $guest = Guest::whereHas('trip', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->with(['trip','booking'])->findOrFail($id);

        return view('guests.detail', compact('guest'));
    }

    /**
     * Download guest PDF (Admin side, scoped to tenant).
     */
    public function download_pdf($id)
    {
        $company = app('tenant');

        $guest = Guest::whereHas('trip', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->with('trip')->findOrFail($id);

        $pdf = PDF::loadView('guests.pdf.view', compact('guest'));
        return $pdf->download('guest-' . $guest->id . '.pdf');
    }
}
