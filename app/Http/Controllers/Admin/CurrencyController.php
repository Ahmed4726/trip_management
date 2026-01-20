<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::latest()->get();
        return view('admin.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('admin.currencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'symbol' => 'required|string|max:5',
            'code' => 'required|string|max:5|unique:currencies,code',
        ]);

        Currency::create($request->all());

        return redirect()->route('admin.currencies.index')->with('success', 'Currency added successfully.');
    }

    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'symbol' => 'required|string|max:5',
            'code' => 'required|string|max:5|unique:currencies,code,' . $currency->id,
        ]);

        $currency->update($request->all());

        return redirect()->route('admin.currencies.index')->with('success', 'Currency updated successfully.');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();
        return redirect()->route('admin.currencies.index')->with('success', 'Currency deleted successfully.');
    }
}
