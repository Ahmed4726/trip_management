<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guest;
use App\Models\Agent;
use App\Models\Company;
use App\Models\Trip;
use App\Models\Booking;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    // List bookings (Admin side, scoped to tenant)
    public function booking_index(Request $request)
    {
        $company = app('tenant');

        $bookings = Booking::with(['trip', 'agent'])
            ->where('company_id', $company->id)
            ->when($request->customer_name, fn($q) => $q->where('customer_name', 'like', '%' . $request->customer_name . '%'))
            ->when($request->status, fn($q) => $q->where('booking_status', $request->status))
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

    // Create booking (Admin)
    public function create_booking()
    {
        $company = app('tenant');
        $agents = Agent::where('company_id', $company->id)->get();
        $trips  = Trip::where('company_id', $company->id)->get();

        return view('admin.bookings.create', compact('agents', 'trips'));
    }

    // Store booking
    public function store_booking(Request $request)
    {
        $company = app('tenant');

        // If creating inline trip
        if (!$request->trip_id && $request->inline_trip) {
            $trip = Trip::create([
                'company_id' => $company->id,
                'title'      => $request->trip_title,
                'boat'       => $request->boat,
                'trip_type'  => $request->trip_type,
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date,
                'status'     => 'Booked',
                'guests'     => $request->inline_guests,
                'price'      => $request->price,
                'region'     => $request->region,
            ]);

            $request->merge(['trip_id' => $trip->id]);
        }

        $validated = $request->validate([
            'trip_id'            => 'required|exists:trips,id',
            'customer_name'      => 'required|string|max:255',
            'guests'             => $request->inline_trip ? 'nullable' : 'required|integer|min:1',
            'inline_guests'      => $request->inline_trip ? 'required|integer|min:1' : 'nullable',
            'source'             => 'required|string|max:255',
            'email'              => 'nullable|email',
            'phone_number'       => 'nullable|string|max:20',
            'nationality'        => 'nullable|string|max:255',
            'passport_number'    => 'nullable|string|max:255',
            'booking_status'     => 'nullable|in:pending,confirmed,cancelled',
            'pickup_location_time' => 'nullable|string|max:255',
            'addons'             => 'nullable|string|max:255',
            'room_preference'    => 'nullable|in:single,double,suite',
            'agent_id'           => 'nullable|exists:agents,id',
            'comments'           => 'nullable|string',
            'notes'              => 'nullable|string',
        ]);

        if (empty($validated['booking_status'])) {
            $validated['booking_status'] = 'pending';
        }

        if ($request->inline_trip) {
            $validated['guests'] = $request->inline_guests;
        }

        $validated['token'] = Str::random(32);
        $validated['company_id'] = $company->id;

        $booking = Booking::create($validated);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking created successfully. Share this link: ' . route('guest.form', $booking->token));
    }

    // Show booking
    public function show_booking($id)
    {
        $company = app('tenant');

        $booking = Booking::where('company_id', $company->id)
            ->with(['trip', 'agent'])
            ->findOrFail($id);

        return view('admin.bookings.detail', compact('booking'));
    }

    // Edit booking
    public function edit_booking($id)
    {
        $company = app('tenant');

        $booking = Booking::where('company_id', $company->id)->findOrFail($id);
        $trips   = Trip::where('company_id', $company->id)->get();
        $agents  = Agent::where('company_id', $company->id)->get();

        return view('admin.bookings.edit', compact('booking', 'trips', 'agents'));
    }

    // Update booking
    public function update_booking(Request $request, $id)
    {
        $company = app('tenant');

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
            'dp_paid' => 'nullable|boolean',
        ]);

        $booking = Booking::where('company_id', $company->id)->findOrFail($id);

        if ($request->has('dp_paid') && $request->dp_paid) {
            $validated['dp_paid'] = true;
            $validated['booking_status'] = 'confirmed';
        } else {
            $validated['dp_paid'] = false;
        }

        $booking->update($validated);

        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully.');
    }

    // Destroy booking
    public function destroy_booking($id)
    {
        $company = app('tenant');

        $booking = Booking::where('company_id', $company->id)->findOrFail($id);
        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully.');
    }

    // Rooms by boat
    public function getRoomsByBoat(Request $request)
    {
        $boatName = $request->input('boat');
        $tripType = $request->input('trip_type');

        if (!$boatName) {
            return response()->json(['rooms' => []]);
        }

        preg_match('/\((\d+)\s*rooms?\)/i', $boatName, $matches);
        $totalRooms = isset($matches[1]) ? (int)$matches[1] : 0;

        $availableRooms = range(1, $totalRooms);

        return response()->json(['rooms' => $availableRooms]);
    }
}
