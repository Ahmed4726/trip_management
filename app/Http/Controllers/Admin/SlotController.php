<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Slot, Boat, Company, Room, Region, Port, Template};
use Illuminate\Http\Request;

class SlotController extends Controller
{
    public function index()
    {
        $slots = Slot::with(['boat','template','region','departurePort','arrivalPort'])
            ->orderBy('start_date')
            ->get();
            $companies = auth()->user()->hasRole('admin')
                ? Company::all()
                : Company::where('id', auth()->user()->company_id)->get();

        return view('admin.slots.index', compact('slots', 'companies'));
    }

    public function create()
    {
        return view('admin.slots.create', [
            'boats' => Boat::with('rooms')->get(),
            'regions' => Region::orderBy('name')->get(),
            'ports' => Port::orderBy('name')->get(),
            'templates' => Template::orderBy('product_name')->get(),
            'companies' => auth()->user()->hasRole('admin')
                ? Company::all()
                : Company::where('id', auth()->user()->company_id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'slot_type' => 'required',
            'boats_allowed' => 'required|array|min:1',
            'boats_allowed.*' => 'exists:boats,id',
            'region_id' => 'required|exists:regions,id',
            'departure_port_id' => 'required|exists:ports,id',
            'arrival_port_id' => 'required|exists:ports,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $status = $request->status;
        if (in_array($request->slot_type,['Maintenance','Docking'])) $status='Blocked';

        if(in_array($status,['Blocked','On-Hold']) && empty($request->notes))
            return back()->withErrors(['notes'=>'Notes are required for this status.'])->withInput();

        // Create Slot
        $slot = Slot::create([
            'slot_type' => $request->slot_type,
            'status' => $status,
            'region_id' => $request->region_id,
            'departure_port_id' => $request->departure_port_id,
            'arrival_port_id' => $request->arrival_port_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
            'created_from_template_id' => $request->template_id ?? null,
            'duration_nights' => $request->duration_nights ?? null,
            'company_id' => $request->company_id,
        ]);

        // Attach selected vessels
        $slot->boats()->sync($request->boats_allowed); // assuming Slot has many-to-many with Boat

        return redirect()->route('admin.slots.index')->with('success','Slot created successfully.');
    }


    public function edit(Slot $slot)
    {
        $slot->load([
            'boats',
            'template',
            'region',
            'departurePort',
            'arrivalPort',
        ]);

        return view('admin.slots.edit', [
            'slot' => $slot,
            'boats' => Boat::with('rooms')->get(),
            'regions' => Region::orderBy('name')->get(),
            'ports' => Port::orderBy('name')->get(),
            'templates' => Template::orderBy('product_name')->get(),
            'companies' => auth()->user()->hasRole('admin')
                ? Company::all()
                : Company::where('id', auth()->user()->company_id)->get(),
        ]);
    }


    public function update(Request $request, Slot $slot)
    {
        // dd($request->all());
        $status = $request->status;
        if (in_array($request->slot_type,['Maintenance','Docking'])) {
            $status = 'Blocked';
        }

        if (in_array($status,['Blocked','On-Hold']) && empty($request->notes)) {
            return back()->withErrors([
                'notes' => 'Notes are required for this status.'
            ])->withInput();
        }

        $slot->update([
            'slot_type' => $request->slot_type,
            'status' => $status,
            'region_id' => $request->region_id,
            'departure_port_id' => $request->departure_port_id,
            'arrival_port_id' => $request->arrival_port_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'duration_nights' => $request->duration_nights ?? null,
            'notes' => $request->notes,
            'created_from_template_id' => $request->template_id ?? null,
            'company_id' => $request->company_id,
        ]);

        // ✅ Sync vessels correctly
        $slot->boats()->sync($request->boats_allowed);

        return redirect()
            ->route('admin.slots.index')
            ->with('success','Slot updated successfully.');
    }


    public function destroy(Slot $slot)
    {
        if ($slot->bookings()->exists())
            return back()->with('error','Cannot delete slot with bookings.');

        $slot->delete();
        return back()->with('success','Slot deleted successfully.');
    }
}
