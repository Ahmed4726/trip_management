<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guest;
use App\Models\Agent;
use App\Models\Company;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\Slot;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    protected $tenant;

    public function __construct()
    {
        $this->tenant = app()->bound('tenant') ? app('tenant') : null;
    }

    // Agents list
    public function index_agent()
    {
        if ($this->tenant) {
            $agents = Agent::with('slots')->where('company_id', $this->tenant->id)->get();
            $allTrips = Slot::where('company_id', $this->tenant->id)->get();
        } else {
            $agents = Agent::with('slots')->get();
            $allTrips = Slot::all();
        }

        $agent = Agent::with('slots')->get();
        $allTrips = Slot::all();

        return view('admin.agents.index', compact('agent', 'agents', 'allTrips'));
    }

    public function assignTrips(Request $request, $agentId)
    {
        $agent = Agent::findOrFail($agentId);

        // Tenant check
        if ($this->tenant && $agent->company_id != $this->tenant->id) {
            abort(403, 'Unauthorized');
        }

        $tripIds = $request->input('trips', []);

        // Only allow trips belonging to the tenant
        if ($this->tenant) {
            $tripIds = Slot::whereIn('id', $tripIds)->where('company_id', $this->tenant->id)->pluck('id')->toArray();
        }

        // Remove old assignments
        DB::table('agent_slot')->where('agent_id', $agentId)->delete();

        // Insert new assignments
        $insertData = [];
        foreach ($tripIds as $tripId) {
            $insertData[] = [
                'agent_id' => $agentId,
                'slot_id'  => $tripId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            DB::table('agent_slot')->insert($insertData);
        }

        return redirect()->back()->with('success', 'Slots assigned successfully.');
    }

    public function create_agent()
    {
        if ($this->tenant) {
            $companies = Company::where('id', $this->tenant->id)->get();
        } else {
            $companies = Company::all();
        }

        return view('admin.agents.create', compact('companies'));
    }

    public function store_agent(Request $request)
    {
        $companyId = $this->tenant ? $this->tenant->id : $request->company_id;

        $agent = Agent::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'commission' => $request->commission,
            'company_id' => $companyId,
        ]);

        return redirect()->route('agents.index')->with('success', 'Agent created successfully.');
    }

    public function filter_agent(Request $request)
    {
        $query = Agent::query();

        if ($this->tenant) {
            $query->where('company_id', $this->tenant->id);
        }

        if ($request->name) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->name . '%')
                  ->orWhere('last_name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->email) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        $agents = $query->with('trips')->get();

        $html = '';
        foreach ($agents as $index => $agent) {
            $html .= '
            <tr>
                <td>' . ($index + 1) . '</td>
                <td>' . $agent->first_name . ' ' . $agent->last_name . '</td>
                <td>' . $agent->email . '</td>
                <td>' . $agent->phone . '</td>
                <td>' . $agent->commission . '</td>
                <td>';
                    if ($agent->trips->count()) {
                        $html .= '<ul>';
                        foreach ($agent->trips as $trip) {
                            $html .= '<li>' . $trip->title . '</li>';
                        }
                        $html .= '</ul>';
                    } else {
                        $html .= '<span class="text-muted">No trips</span>';
                    }
                $html .= '</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editUserModal' . $agent->id . '"
                        data-id="' . $agent->id . '"
                        data-first_name="' . $agent->first_name . '"
                        data-last_name="' . $agent->last_name . '"
                        data-email="' . $agent->email . '"
                        data-phone="' . $agent->phone . '"
                        data-commission="' . $agent->commission . '">
                        Edit
                    </button>
                    <form action="' . route('agents.destroy', $agent->id) . '" method="POST" class="d-inline">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>';
        }

        return response()->json(['html' => $html]);
    }

    public function update_agent(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);

        if ($this->tenant && $agent->company_id != $this->tenant->id) {
            abort(403, 'Unauthorized');
        }

        $companyId = $this->tenant ? $this->tenant->id : $request->company_id;

        $agent->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'commission' => $request->commission,
            'company_id' => $companyId,
        ]);

        return redirect()->route('agents.index')->with('success', 'Agent updated successfully.');
    }

    public function destroy_agent($id)
    {
        $agent = Agent::findOrFail($id);

        if ($this->tenant && $agent->company_id != $this->tenant->id) {
            abort(403, 'Unauthorized');
        }

        $agent->delete();

        return redirect()->route('agents.index')->with('success', 'Agent deleted successfully.');
    }
}
