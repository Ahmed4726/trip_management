<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\Boat;
use App\Models\Region;
use App\Models\Port;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::with('boats','region')->get();
        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.templates.create', [
            'boats' => Boat::all(),
            'regions' => Region::all(),
            'ports' => Port::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|unique:templates,product_name',
            'product_type' => 'required',
            'region_id' => 'required|exists:regions,id',
            'vessels_allowed' => 'required|array',
            'duration_days' => 'required|integer|min:1',
            'duration_nights' => 'required|integer|min:0',
        ]);

        $template = Template::create([
            'product_name' => $request->product_name,
            'product_type' => $request->product_type,
            'region_id' => $request->region_id,
            'duration_days' => $request->duration_days,
            'duration_nights' => $request->duration_nights,
            'departure_ports' => $request->departure_ports ?? [],
            'arrival_ports' => $request->arrival_ports ?? [],
            'min_bookings' => $request->min_bookings ?? 0,
            'default_checkin_from' => $request->default_checkin_from,
            'default_checkin_to' => $request->default_checkin_to,
            'default_checkout_from' => $request->default_checkout_from,
            'default_checkout_to' => $request->default_checkout_to,
            'vessels_allowed' => $request->vessels_allowed,
            'inclusions' => $request->inclusions ?? '',
            'exclusions' => $request->exclusions ?? '',
            'obligatory_surcharges' => $request->obligatory_surcharges ?? '',
            'experience_level' => $request->experience_level ?? '',
            'requirements_description' => $request->requirements_description ?? '',
            'public_comment' => $request->public_comment ?? '',
            'internal_comment' => $request->internal_comment ?? '',
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success','Template created successfully.');
    }

    public function edit(Template $template)
    {
        return view('admin.templates.edit', [
            'template' => $template,
            'boats' => Boat::all(),
            'regions' => Region::all(),
            'ports' => Port::all(),
        ]);
    }

    public function update(Request $request, Template $template)
    {
        $request->validate([
            'product_name' => 'required|unique:templates,product_name,' . $template->id,
            'product_type' => 'required',
            'region_id' => 'required|exists:regions,id',
            'vessels_allowed' => 'required|array',
            'duration_days' => 'required|integer|min:1',
            'duration_nights' => 'required|integer|min:0',
        ]);

        $template->update([
            'product_name' => $request->product_name,
            'product_type' => $request->product_type,
            'region_id' => $request->region_id,
            'duration_days' => $request->duration_days,
            'duration_nights' => $request->duration_nights,
            'departure_ports' => $request->departure_ports ?? [],
            'arrival_ports' => $request->arrival_ports ?? [],
            'min_bookings' => $request->min_bookings ?? 0,
            'default_checkin_from' => $request->default_checkin_from,
            'default_checkin_to' => $request->default_checkin_to,
            'default_checkout_from' => $request->default_checkout_from,
            'default_checkout_to' => $request->default_checkout_to,
            'vessels_allowed' => $request->vessels_allowed,
            'inclusions' => $request->inclusions ?? '',
            'exclusions' => $request->exclusions ?? '',
            'obligatory_surcharges' => $request->obligatory_surcharges ?? '',
            'experience_level' => $request->experience_level ?? '',
            'requirements_description' => $request->requirements_description ?? '',
            'public_comment' => $request->public_comment ?? '',
            'internal_comment' => $request->internal_comment ?? '',
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success','Template updated successfully.');
    }

    public function destroy(Template $template)
    {
        $template->delete();
        return back()->with('success','Template deleted.');
    }
}
