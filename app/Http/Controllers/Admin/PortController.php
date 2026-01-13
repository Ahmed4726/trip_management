<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Port;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index()
    {
        $ports = Port::orderBy('name')->get();
        return view('admin.ports.index', compact('ports'));
    }

    public function create()
    {
        return view('admin.ports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:ports,name',
        ]);

        Port::create($request->only('name'));

        return redirect()->route('admin.ports.index')
            ->with('success', 'Port created successfully.');
    }

    public function edit(Port $port)
    {
        return view('admin.ports.edit', compact('port'));
    }

    public function update(Request $request, Port $port)
    {
        $request->validate([
            'name' => 'required|unique:ports,name,' . $port->id,
        ]);

        $port->update($request->only('name'));

        return redirect()->route('admin.ports.index')
            ->with('success', 'Port updated successfully.');
    }

    public function destroy(Port $port)
    {
        if (
            $port->departureSlots()->exists() ||
            $port->arrivalSlots()->exists()
        ) {
            return back()->with(
                'error',
                'Port cannot be deleted because it is used in slots.'
            );
        }

        $port->delete();

        return back()->with('success', 'Port deleted.');
    }
}
