<?php

namespace App\Http\Controllers;
use App\Models\Trip;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Agent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
         $users = User::all();
          $roles = Role::all();
        return view('admin.users.manage_users', compact('users','roles'));
    }

    public function create()  
    {
         $roles = Role::all();
        return view('admin.users.create',compact('roles'));
    }
public function store(Request $request)
{
 

    $user = User::create([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'email'      => $request->email,
        'password'   => Hash::make($request->password),
    ]);

     $role = Role::findOrFail($request->role);
    $user->assignRole($role->name);

    return redirect()->route('users.create')->with('success', 'User created successfully.');
}

public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

 

    $user->update([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'email'      => $request->email,
        'password'   => $request->filled('password') ? Hash::make($request->password) : $user->password,
    ]);

    $role = Role::findOrFail($request->role);
    $user->syncRoles([$role->name]);

    return redirect()->route('users.index')->with('success', 'User updated successfully.');
}


public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->route('users.index')->with('success', 'User deleted successfully.');
}

// Agents
   public function index_agent()  
    {
        $agents = Agent::all();
        return view('admin.agents.index',compact('agents'));
    }

     public function create_agent()  
    {
        return view('admin.agents.create');
    }

    public function store_agent(Request $request)
{
 

    $user = Agent::create([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
    ]);

    

    return redirect()->route('agents.index')->with('success', 'Agent created successfully.');
}

public function update_agent(Request $request, $id)
{
    $agent = Agent::findOrFail($id);

 

    $agent->update([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name
    ]);

   

    return redirect()->route('agents.index')->with('success', 'Agent updated successfully.');
}


public function destroy_agent($id)
{
    $agent = Agent::findOrFail($id);
    $agent->delete();

    return redirect()->route('agents.index')->with('success', 'Agent deleted successfully.');
}


// Trips
   public function trip_index()  
    {
        $trips = Trip::all();
        $agents = Agent::all();
        return view('admin.trips.index',compact('trips','agents'));
    }

     public function create_trip()  
    {
         $agents = Agent::all();
        return view('admin.trips.create',compact('agents'));
    }

public function store_trip(Request $request)
{
  
    Trip::create([
        'start_date' => $request->start_date,
        'end_date'   => $request->end_date,
        'guests'     => $request->guests,
        'price'      => $request->price,
        'boat'       => $request->boat,
        'agent_id'   => $request->agent_id,
    ]);

    return redirect()->route('trips.index')->with('success', 'Trip created successfully.');
}

public function update_trip(Request $request, $id)
{
    $trip = Trip::findOrFail($id);

 

    $trip->update([
        'start_date' => $request->start_date,
        'end_date'   => $request->end_date,
        'guests'     => $request->guests,
        'price'      => $request->price,
        'boat'       => $request->boat,
        'agent_id'   => $request->agent_id,
    ]);

   

    return redirect()->route('agents.index')->with('success', 'Agent updated successfully.');
}


public function destroy_trip($id)
{
    $trip = Trip::findOrFail($id);
    $trip->delete();

    return redirect()->route('agents.index')->with('success', 'Agent deleted successfully.');
}

}
