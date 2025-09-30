<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RatePlan;

class RatePlanController extends Controller
{
    protected $tenant;

    public function __construct()
    {
        // If tenant is resolved via middleware, set it
        $this->tenant = app()->bound('tenant') ? app('tenant') : null;
    }

    public function index()
    {
        // Fetch only rate plans belonging to the tenant
        $plans = RatePlan::where('tenant_id', $this->tenant->id)->get();
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

        RatePlan::create(array_merge(
            $request->all(),
            ['tenant_id' => $this->tenant->id]
        ));

        return redirect()->route('rate-plans.index')->with('success', 'Rate Plan created.');
    }

    public function edit(RatePlan $ratePlan)
    {
        // Make sure the rate plan belongs to tenant
        if ($ratePlan->tenant_id !== $this->tenant->id) {
            abort(403);
        }

        return view('admin.rate_plans.edit', compact('ratePlan'));
    }

    public function update(Request $request, RatePlan $ratePlan)
    {
        // Make sure the rate plan belongs to tenant
        if ($ratePlan->tenant_id !== $this->tenant->id) {
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
        // Make sure the rate plan belongs to tenant
        if ($ratePlan->tenant_id !== $this->tenant->id) {
            abort(403);
        }

        $ratePlan->delete();

        return back()->with('success', 'Rate Plan deleted.');
    }
}
