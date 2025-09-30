<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RatePlanRule;
use App\Models\RatePlan;

class RatePlanRuleController extends Controller 
{
    /**
     * List all rules for a given rate plan.
     */
    public function index($ratePlanId)
    {
        $query = RatePlan::with('rules');

        if (!auth()->user()->hasRole('admin')) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $ratePlan = $query->findOrFail($ratePlanId);

        return view('admin.rate_plan_rules.index', compact('ratePlan'));
    }

    public function create($ratePlanId)
    {
        $query = RatePlan::query();
        if (!auth()->user()->hasRole('admin')) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $ratePlan = $query->findOrFail($ratePlanId);

        return view('admin.rate_plan_rules.create', compact('ratePlan'));
    }

    public function store(Request $r, $ratePlanId)
    {
        $query = RatePlan::query();
        if (!auth()->user()->hasRole('admin')) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $ratePlan = $query->findOrFail($ratePlanId);

        $r->validate([
            'room_id' => 'nullable|integer',
            'base_price' => 'required|numeric|min:0',
            'extra_bed_price' => 'nullable|numeric|min:0',
        ]);

        RatePlanRule::create(array_merge(
            $r->all(),
            ['rate_plan_id' => $ratePlan->id]
        ));

        return redirect()->route('rate-plan-rules.index', $ratePlan->id)
            ->with('success', 'Rule added.');
    }

    public function edit($ratePlanId, RatePlanRule $rule)
    {
        $query = RatePlan::query();
        if (!auth()->user()->hasRole('admin')) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $ratePlan = $query->findOrFail($ratePlanId);

        // Ensure the rule belongs to this rate plan
        if ($rule->rate_plan_id !== $ratePlan->id) {
            abort(403);
        }

        return view('admin.rate_plan_rules.edit', compact('ratePlan', 'rule'));
    }

    public function update(Request $r, $ratePlanId, RatePlanRule $rule)
    {
        $query = RatePlan::query();
        if (!auth()->user()->hasRole('admin')) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $ratePlan = $query->findOrFail($ratePlanId);

        if ($rule->rate_plan_id !== $ratePlan->id) {
            abort(403);
        }

        $r->validate([
            'room_id' => 'nullable|integer',
            'base_price' => 'required|numeric|min:0',
            'extra_bed_price' => 'nullable|numeric|min:0',
        ]);

        $rule->update($r->all());

        return redirect()->route('rate-plan-rules.index', $ratePlan->id)
            ->with('success', 'Rule updated.');
    }

    public function destroy($ratePlanId, RatePlanRule $rule)
    {
        $query = RatePlan::query();
        if (!auth()->user()->hasRole('admin')) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $ratePlan = $query->findOrFail($ratePlanId);

        if ($rule->rate_plan_id !== $ratePlan->id) {
            abort(403);
        }

        $rule->delete();

        return redirect()->route('rate-plan-rules.index', $ratePlan->id)
            ->with('success', 'Rule deleted.');
    }
}
