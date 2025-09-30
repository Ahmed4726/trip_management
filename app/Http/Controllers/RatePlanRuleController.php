<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RatePlanRule;
use App\Models\RatePlan;

class RatePlanRuleController extends Controller 
{
    protected $tenant;

    public function __construct()
    {
        $this->tenant = app()->bound('tenant') ? app('tenant') : null;
    }

    public function index($ratePlanId)
    {
        $ratePlan = RatePlan::with('rules')
            ->where('tenant_id', $this->tenant->id)
            ->findOrFail($ratePlanId);

        return view('admin.rate_plan_rules.index', compact('ratePlan'));
    }

    public function create($ratePlanId)
    {
        $ratePlan = RatePlan::where('tenant_id', $this->tenant->id)
            ->findOrFail($ratePlanId);

        return view('admin.rate_plan_rules.create', compact('ratePlan'));
    }

    public function store(Request $r, $ratePlanId)
    {
        $ratePlan = RatePlan::where('tenant_id', $this->tenant->id)
            ->findOrFail($ratePlanId);

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
        $ratePlan = RatePlan::where('tenant_id', $this->tenant->id)
            ->findOrFail($ratePlanId);

        // Ensure the rule belongs to this rate plan
        if ($rule->rate_plan_id !== $ratePlan->id) {
            abort(403);
        }

        return view('admin.rate_plan_rules.edit', compact('ratePlan', 'rule'));
    }

    public function update(Request $r, $ratePlanId, RatePlanRule $rule)
    {
        $ratePlan = RatePlan::where('tenant_id', $this->tenant->id)
            ->findOrFail($ratePlanId);

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
        $ratePlan = RatePlan::where('tenant_id', $this->tenant->id)
            ->findOrFail($ratePlanId);

        if ($rule->rate_plan_id !== $ratePlan->id) {
            abort(403);
        }

        $rule->delete();

        return redirect()->route('rate-plan-rules.index', $ratePlan->id)
            ->with('success', 'Rule deleted.');
    }
}
