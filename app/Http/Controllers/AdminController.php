<?php

namespace App\Http\Controllers;
use App\Models\Trip;
use App\Models\Booking;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Guest;
use App\Models\Agent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{

 public function dashboard()
{
    $agentsCount = Agent::count();
    $tripsCount = Trip::count();
    return view('dashboard', compact('agentsCount','tripsCount'));
}

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
        $agents = Agent::with('trips')->get();
        $allTrips = Trip::all();

        return view('admin.agents.index', compact('agents', 'allTrips'));
    }


    public function assignTrips(Request $request, $agentId)
    {

        // dd($request);

        $tripIds = $request->input('trips', []);

        // Remove old assignments for this agent
        DB::table('agent_trip')->where('agent_id', $agentId)->delete();

        // Insert new assignments
        $insertData = [];
        foreach ($tripIds as $tripId) {
            $insertData[] = [
                'agent_id' => $agentId,
                'trip_id'  => $tripId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            DB::table('agent_trip')->insert($insertData);
        }

        return redirect()->back()->with('success', 'Trips assigned successfully.');
    }


    // public function index_agent()
    // {
    //    $agents = Agent::with('trips')->get();
    //     return view('admin.agents.index',compact('agents'));
    // }

    public function create_agent()
    {
        return view('admin.agents.create');
    }

    public function store_agent(Request $request)
    {
        $agent = Agent::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'commission' => $request->commission,
            'company' => $request->company,
        ]);
        return redirect()->route('agents.index')->with('success', 'Agent created successfully.');
    }

    public function filter_agent(Request $request)
    {
        $query = Agent::query();

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
            // Note: You can optionally include modal HTML here if needed
        }

        return response()->json(['html' => $html]);
    }


    public function update_agent(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);

        $agent->update([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'email'      => $request->email,
        'phone'      => $request->phone,
        'commission' => $request->commission,
        'company' => $request->company,
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
        $tripTypes = Trip::select('trip_type')->distinct()->pluck('trip_type');
        return view('admin.trips.index',compact('trips','agents', 'tripTypes'));
    }

    public function create_trip()
    {
         $agents = Agent::all();

        return view('admin.trips.create',compact('agents'));
    }

    public function filter(Request $request)
    {
        $query = Trip::query();

        if ($request->boat) $query->where('boat', $request->boat);
        if ($request->region) $query->where('region', $request->region);
        if ($request->status) $query->where('status', $request->status);
        if ($request->start_date) $query->whereDate('start_date', '>=', $request->start_date);
        if ($request->end_date) $query->whereDate('end_date', '<=', $request->end_date);

        $trips = $query->with('agent')->get();
        $tripTypes = Trip::select('trip_type')->distinct()->pluck('trip_type');
        $agents = Agent::all();

        // Render full HTML (inline Blade string)
        $html = '';
        foreach ($trips as $index => $trip) {
            $html .= '
            <tr>
                <td>' . ($index + 1) . '</td>
                <td>' . $trip->title . '</td>
                <td>' . $trip->region . '</td>
                <td>' . $trip->status . '</td>
                <td>' . $trip->start_date . '</td>
                <td>' . $trip->end_date . '</td>
                <td>$' . $trip->price . '</td>
               
                <td class="text-center">
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-sm btn-primary mx-2" data-toggle="modal" data-target="#editTripModal' . $trip->id . '">
                            Edit
                        </button>
                        <form action="' . route('trips.destroy', $trip->id) . '" method="POST">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            ';
            // Optional: Add modals here as string if needed
        }

        return response()->json(['html' => $html]);
    }



    public function store_trip(Request $request)
    {
       

        Trip::create([
            'title'            => $request->title,
            'region'           => $request->region,
            'status'           => $request->status,
            'trip_type'        => $request->trip_type,
            'leading_guest_id' => $request->leading_guest_id,
            'notes'            => $request->notes,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'guests'           => $request->guests,
            'price'            => $request->price,
            'boat'             => $request->boat,
            'agent_id'         => $request->agent_id,
         
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip created successfully.');
    }

    public function show($id)
    {
        $trip = Trip::with(['agent', 'guestList.otherGuests'])->findOrFail($id);
        return view('admin.trips.detail', compact('trip'));
    }



    public function update_trip(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        $trip->update([
            'title'            => $request->title,
            'region'           => $request->region,
            'status'           => $request->status,
            'trip_type'        => $request->trip_type,
            'leading_guest_id' => $request->leading_guest_id,
            'notes'            => $request->notes,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'guests'           => $request->guests,
            'price'            => $request->price,
            'boat'             => $request->boat,
            'agent_id'         => $request->agent_id,
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip updated successfully.');
    }


    public function destroy_trip($id)
    {
        $trip = Trip::findOrFail($id);
        $trip->delete();

        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully.');
    }

    // app/Http/Controllers/TripController.php

// public function getRooms($tripId)
// {
//     $trip = Trip::findOrFail($tripId);

//     // Extract number of rooms from boat name e.g. "Samara 1 (5 rooms)"
//     preg_match('/\((\d+)\s*rooms?\)/i', $trip->boat, $matches);
//     $rooms = isset($matches[1]) ? (int)$matches[1] : 0;

//     return response()->json(['rooms' => $rooms]);
// }


public function getRooms($tripId)
{
    $trip = Trip::findOrFail($tripId);

    // Extract total rooms from boat name
    preg_match('/\((\d+)\s*rooms?\)/i', $trip->boat, $matches);
    $totalRooms = isset($matches[1]) ? (int)$matches[1] : 0;

    // Already booked guest numbers
    $booked = Booking::where('trip_id', $tripId)->pluck('guests')->toArray();

    // Available = total minus booked
    $availableRooms = array_values(array_diff(range(1, $totalRooms), $booked));

    return response()->json([
        'rooms' => $availableRooms
    ]);
}


    // Finances
    public function finance_index()
    {
        $trips = Trip::all();
        $agents = Agent::all();
        return view('admin.finances.index',compact('trips','agents'));
    }

     public function create_finance()
    {
         $agents = Agent::all();
        return view('admin.trips.create',compact('agents'));
    }

    public function store_finance(Request $request)
    {

    return redirect()->route('trips.index')->with('success', 'Trip created successfully.');
}

public function update_finance(Request $request, $id)
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


    return redirect()->route('trips.index')->with('success', 'Trip updated successfully.');
}


public function destroy_finance($id)
{
    $trip = Trip::findOrFail($id);
    $trip->delete();

    return redirect()->route('trips.index')->with('success', 'Trip deleted successfully.');
}

    //Guests
         public function guest_index()
{
    $guests = Guest::all();

    return view('guests.index', compact('guests'));
}

     // Bookings
public function booking_index(Request $request)
{
    $bookings = Booking::with(['trip', 'agent'])
        ->when($request->customer_name, function ($q) use ($request) {
            $q->where('customer_name', 'like', '%' . $request->customer_name . '%');
        })
        ->when($request->status, function ($q) use ($request) {
            $q->where('booking_status', $request->status);
        })
        ->when($request->start_date, function ($q) use ($request) {
            $q->whereHas('trip', function ($q2) use ($request) {
                $q2->whereDate('start_date', '>=', $request->start_date);
            });
        })
        ->when($request->end_date, function ($q) use ($request) {
            $q->whereHas('trip', function ($q2) use ($request) {
                $q2->whereDate('end_date', '<=', $request->end_date);
            });
        })
        ->latest()
        ->get();

    if ($request->ajax()) {
        $html = '';
        if ($bookings->count()) {
            foreach ($bookings as $index => $booking) {
                $html .= '
                <tr>
                    <td>'.($index+1).'</td>
                    <td>'.($booking->customer_name ?? "—").'</td>
                    <td>'.($booking->booking_status ?? "—").'</td>
                    <td>'.(optional($booking->agent)->first_name.' '.optional($booking->agent)->last_name).'</td>
                    <td>'.($booking->trip->start_date ?? "—").'</td>
                    <td>'.($booking->trip->end_date ?? "—").'</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="copyText('.$booking->id.')">Copy Link</button>
                        <span id="linkText'.$booking->id.'" class="d-none">'.route("guest.form",$booking->token).'</span>
                    </td>
                    <td>'.($booking->source ?? "—").'</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <a href="'.route("bookings.show",$booking->id).'" class="btn btn-sm btn-success mx-2">View</a>
                            <a href="'.route("bookings.edit",$booking->id).'" class="btn btn-sm btn-primary mx-2">Edit</a>
                            <form action="'.route("bookings.destroy",$booking->id).'" method="POST" onsubmit="return confirm(\'Are you sure?\')">
                                '.csrf_field().method_field("DELETE").'
                                <button type="submit" class="btn btn-sm btn-danger mx-2">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>';
            }
        } else {
            $html .= '<tr><td colspan="11" class="text-center">No Bookings available</td></tr>';
        }
        return response()->json(['html' => $html]);
    }

    return view('admin.bookings.index', compact('bookings'));
}


    public function create_booking()
    {
         $agents = Agent::get();
         $trips = Trip::get();

        return view('admin.bookings.create',compact('agents','trips'));
    }

    


   public function store_booking(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'customer_name' => 'required|string|max:255',
            'guests' => 'required|integer|min:1',
            'source' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:255',
            'booking_status' => 'nullable|in:pending,confirmed,cancelled',
            'pickup_location_time' => 'nullable|string|max:255',
            'addons' => 'nullable|string|max:255',
            'room_preference' => 'nullable|in:single,double,suite',
            'agent_id' => 'nullable|exists:agents,id',
            'comments' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Generate unique token
        $validated['token'] = Str::random(32);

        $booking = Booking::create($validated);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking created successfully. Share this link with the user: ' . route('guest.form', $booking->token));
    }

    public function show_booking($id)
    {
        $booking = Booking::with(['trip', 'agent'])->findOrFail($id);
        return view('admin.bookings.detail', compact('booking'));
    }

    public function edit_booking($id)
    {
        $booking = Booking::findOrFail($id);
        $trips   = Trip::all();
        $agents  = Agent::all();

        return view('admin.bookings.edit', compact('booking', 'trips', 'agents'));
    }




    public function update_booking(Request $request, $id)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'customer_name' => 'required|string|max:255',
            'guests' => 'required|integer|min:1',
            'source' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:255',
            'booking_status' => 'nullable|in:pending,confirmed,cancelled',
            'pickup_location_time' => 'nullable|string|max:255',
            'addons' => 'nullable|string|max:255',
            'room_preference' => 'nullable|in:single,double,suite',
            'agent_id' => 'nullable|exists:agents,id',
            'comments' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update($validated);

        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully.');
    }


    public function destroy_booking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully.');
    }







}
