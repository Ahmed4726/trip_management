<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'gender'     => 'required|string|max:50',
            'email'      => 'nullable|email|max:255',
            'passport'   => 'nullable|string|max:255',
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:255',
            'company_id' => 'nullable|integer',
            'dob'        => 'nullable|date',
        ]);

        $guest = Guest::create($data);

        return response()->json($guest);
    }
}
