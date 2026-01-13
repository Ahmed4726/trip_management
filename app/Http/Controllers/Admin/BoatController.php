<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use Illuminate\Http\Request;

class BoatController extends Controller
{
    public function index()
    {
        $boats = Boat::withCount('rooms')->get();
        return view('admin.boat.index', compact('boats'));
    }

    public function create()
    {
        return view('admin.boat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:boats,name',
            'max_capacity' => 'required|integer|min:1',
        ]);

        Boat::create([
            'name' => $request->name,
            'max_capacity' => $request->max_capacity,
            'total_rooms' => 0,
        ]);

        return redirect()->route('admin.boats.index')
            ->with('success', 'Boat created successfully.');
    }

    public function edit(Boat $boat)
    {
        return view('admin.boat.edit', compact('boat'));
    }

    public function update(Request $request, Boat $boat)
    {
        $request->validate([
            'name' => 'required|unique:boats,name,' . $boat->id,
            'max_capacity' => 'required|integer|min:1',
        ]);

        $boat->update($request->only('name', 'max_capacity'));

        return redirect()->route('admin.boats.index')
            ->with('success', 'Boat updated successfully.');
    }

    public function destroy(Boat $boat)
    {
        if (!$boat->canBeDeleted()) {
            return back()->with(
                'error',
                'Boat cannot be deleted because it has bookings.'
            );
        }

        $boat->delete();

        return back()->with('success', 'Boat deleted.');
    }
}
