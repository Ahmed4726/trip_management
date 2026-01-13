<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Slot, Boat, Room, Region, Port, Template};
use Illuminate\Http\Request;

class SlotController extends Controller
{
    public function index()
    {
        $slots = Slot::with(['boat','template','region','departurePort','arrivalPort'])
            ->orderBy('start_date')
            ->get();


        return view('admin.slots.index', compact('slots'));
    }

    public function create()
    {
        return view('admin.slots.create', [
            'boats' => Boat::with('rooms')->get(),
            'regions' => Region::orderBy('name')->get(),
            'ports' => Port::orderBy('name')->get(),
            'templates' => Template::orderBy('product_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'slot_type' => 'required',
            'boat_id' => 'required|exists:boats,id',
            'region_id' => 'required|exists:regions,id',
            'departure_port_id' => 'required|exists:ports,id',
            'arrival_port_id' => 'required|exists:ports,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $status = $request->status;
        if (in_array($request->slot_type,['Maintenance','Docking'])) $status='Blocked';

        if(in_array($status,['Blocked','On Hold']) && empty($request->notes))
            return back()->withErrors(['notes'=>'Notes are required for this status.'])->withInput();

        Slot::create([
            'slot_type' => $request->slot_type,
            'status' => $status,
            'boat_id' => $request->boat_id,
            'region_id' => $request->region_id,
            'departure_port_id' => $request->departure_port_id,
            'arrival_port_id' => $request->arrival_port_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'available_rooms' => $request->rooms ?? [],
            'notes' => $request->notes,
            'created_from_template_id' => $request->template_id ?? null,
        ]);

        return redirect()->route('admin.slots.index')->with('success','Slot created successfully.');
    }

    public function edit(Slot $slot)
    {
        return view('admin.slots.edit', [
            'slot' => $slot,
            'boats' => Boat::with('rooms')->get(),
            'regions' => Region::orderBy('name')->get(),
            'ports' => Port::orderBy('name')->get(),
            'templates' => Template::orderBy('product_name')->get(),
        ]);
    }

    public function update(Request $request, Slot $slot)
    {
        $status = $request->status;
        if (in_array($request->slot_type,['Maintenance','Docking'])) $status='Blocked';

        if(in_array($status,['Blocked','On Hold']) && empty($request->notes))
            return back()->withErrors(['notes'=>'Notes are required for this status.'])->withInput();

        $slot->update([
            'slot_type' => $request->slot_type,
            'status' => $status,
            'boat_id' => $request->boat_id,
            'region_id' => $request->region_id,
            'departure_port_id' => $request->departure_port_id,
            'arrival_port_id' => $request->arrival_port_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'available_rooms' => $request->rooms ?? [],
            'notes' => $request->notes,
            'created_from_template_id' => $request->template_id ?? null,
        ]);

        return redirect()->route('admin.slots.index')->with('success','Slot updated successfully.');
    }

    public function destroy(Slot $slot)
    {
        if ($slot->bookings()->exists())
            return back()->with('error','Cannot delete slot with bookings.');

        $slot->delete();
        return back()->with('success','Slot deleted successfully.');
    }
}
