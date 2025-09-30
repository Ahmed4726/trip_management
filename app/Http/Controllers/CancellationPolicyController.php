<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CancellationPolicy;
use Illuminate\Support\Facades\Auth;

class CancellationPolicyController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('admin')) {
            $policies = CancellationPolicy::all();
        } else {
            $policies = CancellationPolicy::where('company_id', Auth::user()->company_id)->get();
        }

        return view('admin.cancellation_policies.index', compact('policies'));
    }

    public function create()
    {
        return view('admin.cancellation_policies.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $r->all();

        // Ensure company_id is attached
        if (!Auth::user()->hasRole('admin')) {
            $data['company_id'] = Auth::user()->company_id;
        }

        CancellationPolicy::create($data);

        return redirect()->route('cancellation-policies.index')
            ->with('success', 'Cancellation Policy created.');
    }

    public function edit(CancellationPolicy $policy)
    {
        // Restrict company users from editing other company’s policies
        if (!Auth::user()->hasRole('admin') && $policy->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized access.');
        }

        return view('admin.cancellation_policies.edit', compact('policy'));
    }

    public function update(Request $r, CancellationPolicy $policy)
    {
        if (!Auth::user()->hasRole('admin') && $policy->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized access.');
        }

        $r->validate([
            'name' => 'required|string|max:255',
        ]);

        $policy->update($r->all());

        return redirect()->route('cancellation-policies.index')
            ->with('success', 'Cancellation Policy updated.');
    }

    public function destroy(CancellationPolicy $policy)
    {
        if (!Auth::user()->hasRole('admin') && $policy->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized access.');
        }

        $policy->delete();

        return redirect()->route('cancellation-policies.index')
            ->with('success', 'Cancellation Policy deleted.');
    }
}
