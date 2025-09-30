<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentPolicy;
use Illuminate\Support\Facades\Auth;

class PaymentPolicyController extends Controller 
{
    protected function authorizePolicy(PaymentPolicy $policy)
    {
        if (!Auth::user()->hasRole('admin') && $policy->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function index()
    {
        if (Auth::user()->hasRole('admin')) {
            $policies = PaymentPolicy::all();
        } else {
            $policies = PaymentPolicy::where('company_id', Auth::user()->company_id)->get();
        }

        return view('admin.payment_policies.index', compact('policies')); 
    }

    public function create()
    {
        return view('admin.payment_policies.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'name'                     => 'required|string|max:255',
            'dp_percent'               => 'required|integer|min:0|max:100',
            'balance_days_before_start'=> 'required|integer|min:0',
            'auto_cancel_if_dp_overdue'=> 'nullable|boolean',
            'grace_days'               => 'nullable|integer|min:0'
        ]);

        $data = $r->all();

        if (!Auth::user()->hasRole('admin')) {
            $data['company_id'] = Auth::user()->company_id; // auto-attach
        }

        PaymentPolicy::create($data);

        return redirect()->route('payment-policies.index')
            ->with('success','Payment Policy created.');
    }

    public function edit(PaymentPolicy $policy)
    {
        $this->authorizePolicy($policy);

        return view('admin.payment_policies.edit', compact('policy')); 
    }

    public function update(Request $r, PaymentPolicy $policy)
    {
        $this->authorizePolicy($policy);

        $r->validate([
            'name'                     => 'required|string|max:255',
            'dp_percent'               => 'required|integer|min:0|max:100',
            'balance_days_before_start'=> 'required|integer|min:0',
            'auto_cancel_if_dp_overdue'=> 'nullable|boolean',
            'grace_days'               => 'nullable|integer|min:0'
        ]);

        $policy->update($r->all());

        return redirect()->route('payment-policies.index')
            ->with('success','Payment Policy updated.');
    }

    public function destroy(PaymentPolicy $policy)
    {
        $this->authorizePolicy($policy);

        $policy->delete();

        return redirect()->route('payment-policies.index')
            ->with('success','Payment Policy deleted.'); 
    }
}
