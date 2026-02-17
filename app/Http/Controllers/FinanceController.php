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

class FinanceController extends Controller
{
    protected $tenant;

    public function __construct()
    {
        $this->tenant = app()->bound('tenant') ? app('tenant') : null;
    }

    // Finances
    public function finance_index()
    {
        $query = Slot::query();
        if ($this->tenant) {
            $query->where('company_id', $this->tenant->id);
        }
        $trips = $query->get();

        $agents = $this->tenant ? Agent::where('company_id', $this->tenant->id)->get() : Agent::all();

        return view('admin.finances.index', compact('trips', 'agents'));
    }


}
