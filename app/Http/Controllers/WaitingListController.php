<?php

// app/Http/Controllers/Public/WaitingListController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WaitingList;
use App\Models\Trip;
use Illuminate\Support\Facades\Mail;

class WaitingListController extends Controller
{
    public function store(Request $request)
    {
        // dd($request);

        $trip = Trip::where('id',$request->trip_id)->first();
// dd($trip);
        $entry = WaitingList::create([
            'company_id'     => $trip->company_id,
            'availability_id'=> $trip->id,
            'party_size'     => $request->party_size,
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'notes'          => $request->notes,
            'source'         => 'widget',
        ]);

        // Email guest
        Mail::to($entry->email)->send(new \App\Mail\WaitingListGuestMail($entry));

        // Email office
        Mail::to(config('mail.office_email'))->send(new \App\Mail\WaitingListOfficeMail($entry));

        return back()->with('success', 'You have joined the waiting list! We’ll notify you if a spot opens.');
    }


    public function index(Request $request)
    {
        $query = WaitingList::with(['availability','company']);

        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->availability_id) {
            $query->where('availability_id', $request->availability_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $waitingLists = $query->latest()->paginate(20);

        return view('admin.waiting_lists.index', compact('waitingLists'));
    }

    public function notify(WaitingList $waitingList)
    {
        // send email to guest
        Mail::send('emails.waitinglist.notify', ['waitingList'=>$waitingList], function($m) use ($waitingList) {
            $m->to($waitingList->email)->subject('Spot Available!');
        });

        $waitingList->update(['status' => 'notified']);

        return back()->with('success','Guest notified successfully.');
    }

    public function convertToBooking(WaitingList $waitingList)
    {
        // Prefill booking form
        return redirect()->route('admin.bookings.create', [
            'waiting_list_id' => $waitingList->id
        ]);
    }

    public function markConverted(WaitingList $waitingList)
    {
        $waitingList->update(['status' => 'converted']);
        return back()->with('success','Marked as converted.');
    }



}
