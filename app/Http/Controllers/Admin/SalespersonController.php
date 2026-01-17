<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salesperson;
use Illuminate\Http\Request;

class SalespersonController extends Controller
{
    public function index()
    {
        $salespeople = Salesperson::latest()->get();
        return view('admin.salespeople.index', compact('salespeople'));
    }

    public function create()
    {
        return view('admin.salespeople.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Salesperson::create($request->all());
        return redirect()->route('admin.salespeople.index')->with('success','Created');
    }

    public function edit(Salesperson $salesperson)
    {
        return view('admin.salespeople.edit', compact('salesperson'));
    }

    public function update(Request $request, Salesperson $salesperson)
    {
        $salesperson->update($request->all());
        return redirect()->route('admin.salespeople.index')->with('success','Updated');
    }

    public function destroy(Salesperson $salesperson)
    {
        $salesperson->delete();
        return back()->with('success','Deleted');
    }
}
