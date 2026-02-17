<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Booking;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Guest;
use App\Models\Agent;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $tenant;

    public function __construct()
    {
        // If tenant is resolved via middleware, set it
        $this->tenant = app()->bound('tenant') ? app('tenant') : null;
    }

    public function index()
    {
        if ($this->tenant) {
            $users = User::where('company_id', $this->tenant->id)->get();
            $roles = Role::all();
            $companies = Company::where('id', $this->tenant->id)->get();
        } else {
            $users = User::all();
            $roles = Role::all();
            $companies = Company::all();
        }

        return view('admin.users.manage_users', compact('users', 'roles', 'companies'));
    }

    public function create()
    {
        $roles = Role::all();

        if ($this->tenant) {
            $companies = Company::where('id', $this->tenant->id)->get();
        } else {
            $companies = Company::all();
        }

        return view('admin.users.create', compact('roles', 'companies'));
    }

    public function store(Request $request)
    {
        $companyId = $this->tenant ? $this->tenant->id : $request->company_id;

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8',
            'role'       => 'required|exists:roles,id',
            'company_id' => $this->tenant ? 'nullable' : 'required|exists:companies,id',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'company_id' => $companyId,
        ]);

        $role = Role::findOrFail($request->role);
        $user->assignRole($role->name);

        return redirect()->route('users.create')->with('success', 'User created successfully.');
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Tenant security check
        if ($this->tenant && $user->company_id != $this->tenant->id) {
            abort(403, 'Unauthorized');
        }

        $companyId = $this->tenant ? $this->tenant->id : $request->company_id;

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'password'   => 'nullable|string|min:8',
            'role'       => 'required|exists:roles,id',
            'company_id' => $this->tenant ? 'nullable' : 'required|exists:companies,id',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'company_id' => $companyId,
            'password'   => $request->filled('password')
                                ? Hash::make($request->password)
                                : $user->password,
        ]);

        $role = Role::findOrFail($request->role);
        $user->syncRoles([$role->name]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Only allow deletion if tenant matches (or admin)
        if ($this->tenant && $user->company_id != $this->tenant->id) {
            abort(403, 'Unauthorized');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
