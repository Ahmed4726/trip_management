<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CancellationPolicyRule;
use App\Models\CancellationPolicy;
use Illuminate\Support\Facades\Auth;

class CancellationPolicyRuleController extends Controller 
{
    protected function authorizePolicy($policyId)
    {
        $policy = CancellationPolicy::findOrFail($policyId);

        if (!Auth::user()->hasRole('admin') && $policy->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized access.');
        }

        return $policy;
    }

    public function index($policyId)
    {
        $policy = $this->authorizePolicy($policyId)->load('rules');

        return view('admin.cancellation_policy_rules.index', compact('policy'));
    }

    public function create($policyId)
    {
        $policy = $this->authorizePolicy($policyId);

        return view('admin.cancellation_policy_rules.create', compact('policy'));
    }

    public function store(Request $r, $policyId)
    {
        $policy = $this->authorizePolicy($policyId);

        $r->validate([
            'days_from'       => 'required|integer|min:0',
            'days_to'         => 'required|integer|min:0',
            'penalty_percent' => 'required|integer|min:0|max:100',
            'refundable'      => 'nullable|boolean',
        ]);

        CancellationPolicyRule::create(array_merge(
            $r->all(),
            ['cancellation_policy_id' => $policy->id]
        ));

        return redirect()
            ->route('cancellation-policy-rules.index', $policyId)
            ->with('success', 'Rule added.');
    }

    public function edit($policyId, CancellationPolicyRule $rule)
    {
        $policy = $this->authorizePolicy($policyId);

        if ($rule->cancellation_policy_id !== $policy->id) {
            abort(403, 'Unauthorized access.');
        }

        return view('admin.cancellation_policy_rules.edit', compact('policy', 'rule'));
    }

    public function update(Request $r, $policyId, CancellationPolicyRule $rule)
    {
        $policy = $this->authorizePolicy($policyId);

        if ($rule->cancellation_policy_id !== $policy->id) {
            abort(403, 'Unauthorized access.');
        }

        $r->validate([
            'days_from'       => 'required|integer|min:0',
            'days_to'         => 'required|integer|min:0',
            'penalty_percent' => 'required|integer|min:0|max:100',
            'refundable'      => 'nullable|boolean',
        ]);

        $rule->update($r->all());

        return redirect()
            ->route('cancellation-policy-rules.index', $policyId)
            ->with('success', 'Rule updated.');
    }

    public function destroy($policyId, CancellationPolicyRule $rule)
    {
        $policy = $this->authorizePolicy($policyId);

        if ($rule->cancellation_policy_id !== $policy->id) {
            abort(403, 'Unauthorized access.');
        }

        $rule->delete();

        return redirect()
            ->route('cancellation-policy-rules.index', $policyId)
            ->with('success', 'Rule deleted.');
    }
}
