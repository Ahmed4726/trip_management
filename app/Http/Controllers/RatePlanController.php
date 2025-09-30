<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RatePlan;

class RatePlanController extends Controller
{
    public function index()
    {
        $query = RatePlan::query();

        // If not admin, restrict by company
        if (!auth()->user()->hasRole('admin')) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $plans = $query->get();

        return view('admin.rate_plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.rate_plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|max:3',
            'base_price_type' => 'required|in:per_room,charter',
        ]);

        $data = $request->all();

        // Add company_id for non-admins
        if (!auth()->user()->hasRole('admin')) {
            $data['company_id'] = auth()->user()->company_id;
        }

        RatePlan::create($data);

        return redirect()->route('rate-plans.index')->with('success', 'Rate Plan created.');
    }

    public function edit(RatePlan $ratePlan)
    {
        // Only allow editing if admin or same company
        if (!auth()->user()->hasRole('admin') && $ratePlan->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        return view('admin.rate_plans.edit', compact('ratePlan'));
    }

    public function update(Request $request, RatePlan $ratePlan)
    {
        // Only allow updating if admin or same company
        if (!auth()->user()->hasRole('admin') && $ratePlan->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|max:3',
            'base_price_type' => 'required|in:per_room,charter',
        ]);

        $ratePlan->update($request->all());

        return redirect()->route('rate-plans.index')->with('success', 'Rate Plan updated.');
    }

    public function destroy(RatePlan $ratePlan)
    {
        // Only allow deleting if admin or same company
        if (!auth()->user()->hasRole('admin') && $ratePlan->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $ratePlan->delete();

        return back()->with('success', 'Rate Plan deleted.');
    }
}
