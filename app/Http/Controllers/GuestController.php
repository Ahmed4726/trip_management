<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    // INDEX
    public function index()
    {
        $guests = Guest::latest()->simplePaginate(10);
        return view('admin.guests.index', compact('guests'));
    }

    // CREATE
    public function create()
    {
        return view('admin.guests.create');
    }

    // STORE
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

        Guest::create($data);

        return redirect()
            ->route('admin.guests.index')
            ->with('success', 'Guest created successfully');
    }

    // EDIT
    public function edit(Guest $guest)
    {
        return view('admin.guests.edit', compact('guest'));
    }

    // UPDATE
    public function update(Request $request, Guest $guest)
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

        $guest->update($data);

        return redirect()
            ->route('admin.guests.index')
            ->with('success', 'Guest updated successfully');
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();

        return redirect()
            ->route('admin.guests.index')
            ->with('success', 'Guest deleted successfully');
    }

}
