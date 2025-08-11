<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Guest;
class GuestController extends Controller
{

   public function store(Request $request)
    {
        // dd($request);
        // $request->validate([
        //     'name' => 'required',
        //     'gender' => 'required',
        //     'email' => 'required|email',
        //     'dob' => 'required|date',
        //     'passport' => 'required',
        //     'nationality' => 'required',
        //     'cabin' => 'required',
        //     'surfLevel' => 'required',
        //     'arrivalFlightDate' => 'required|date',
        //     'arrivalFlightNumber' => 'required',
        //     'arrivalAirport' => 'required',
        //     'arrivalTime' => 'required',
        //     'departureFlightDate' => 'required|date',
        //     'departureFlightNumber' => 'required',
        //     'departureAirport' => 'required',
        //     'departureTime' => 'required',
        // ]);

        $guest = Guest::create([
            'trip_id' => $request->trip_id,
            'name' => $request->name,
            'gender' => $request->gender,
            'email' => $request->email,
            'dob' => $request->dob,
            'passport' => $request->passport,
            'nationality' => $request->nationality,
            'cabin' => $request->cabin,
            'surfLevel' => $request->surfLevel,
            'boardDetails' => $request->boardDetails,

            'arrivalFlightDate' => $request->arrivalFlightDate,
            'arrivalFlightNumber' => $request->arrivalFlightNumber,
            'arrivalAirport' => $request->arrivalAirport,
            'arrivalTime' => $request->arrivalTime,
            'hotelPickup' => $request->hotelPickup,
            'departureFlightDate' => $request->departureFlightDate,
            'departureFlightNumber' => $request->departureFlightNumber,
            'departureAirport' => $request->departureAirport,
            'departureTime' => $request->departureTime,

            'medicalDietary' => $request->medicalDietary,
            'specialRequests' => $request->specialRequests,
            'insuranceName' => $request->insuranceName,
            'policyNumber' => $request->policyNumber,
            'emergencyName' => $request->emergencyName,
            'emergencyRelation' => $request->emergencyRelation,
            'emergencyPhone' => $request->emergencyPhone,
            'guestWhatsapp' => $request->guestWhatsapp,
            'guestEmail' => $request->guestEmail,
        ]);

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

   if ($request->has('guest_name')) {
    $guestCount = count($request->guest_name);

    for ($i = 0; $i < $guestCount; $i++) {
        $guest->otherGuests()->create([
            'name'          => $request->guest_name[$i] ?? null,
            'gender'        => $request->guest_gender[$i] ?? null,
            'email'         => $request->guest_email[$i] ?? null,
            // 'password'    => $request->guest_password[$i] ?? null, // if added later
            'dob'           => $request->guest_dob[$i] ?? null,
            'passport'      => $request->guest_passport[$i] ?? null,
            'nationality'   => $request->guest_nationality[$i] ?? null,
            'cabin'         => $request->guest_cabin[$i] ?? null,
            'surfLevel'     => $request->guest_surf[$i] ?? null,
            'boardDetails'  => $request->guest_board[$i] ?? null,
        ]);
    }
}


        return redirect()->back()->with('success', 'Guest form submitted successfully.');
    }

    public function show($token)
    {
        $booking = Booking::where('token', $token)->firstOrFail();
        return view('guests.guest_form', compact('booking'));
    }

    // public function show($token)
    // {
    //     $trip = Trip::where('guest_form_token', $token)->firstOrFail();
    //     return view('guests.guest_form', compact('trip'));
    // }

    public function submit(Request $request, $token)
    {
        $trip = Trip::where('guest_form_token', $token)->firstOrFail();

        Guest::create([
            'trip_id' => $trip->id,
            'name'    => $request->name,
            'email'   => $request->email,
            // other guest fields...
        ]);

        return redirect()->back()->with('success', 'Guest info submitted!');
    }
}
