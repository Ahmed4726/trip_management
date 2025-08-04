<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Guest;
class GuestController extends Controller
{
 public function show($token)
{
    $trip = Trip::where('guest_form_token', $token)->firstOrFail();
    return view('guests.guest_form', compact('trip'));
}

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
