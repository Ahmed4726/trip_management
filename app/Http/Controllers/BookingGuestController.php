<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingGuest;
use App\Models\BookingTravelDetail;
use App\Models\GuestDocument;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingGuestController extends Controller
{
    public function index(Booking $booking)
    {
        $booking->load('bookingGuests.guest');

        return view('admin.booking.guests.index', compact('booking'));
    }

    public function show(Booking $booking, BookingGuest $bookingGuest)
    {
        $bookingGuest->load([
            'guest',
            'travelDetails',
            'medical',
            'foodPreference',
            'drinkPreference',
            'housekeeping',
            'serviceNote',
            'diving',
            'surfing',
            'documents'
        ]);

        // REMOVE old logic ❌
        // $arrival = ...
        // $departure = ...

        $travels = $bookingGuest->travelDetails->sortByDesc('date');


        return view('admin.booking.guests.show', compact(
            'booking',
            'bookingGuest',
            'travels'
        ));
    }

    // ================= PROFILE =================
    public function updateProfile(Request $request, BookingGuest $bookingGuest)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'email'      => 'nullable|email',
            'phone'      => 'nullable|string',
        ]);

        $bookingGuest->guest->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'full_name'  => trim($request->first_name . ' ' . $request->last_name),
            'email'      => $request->email,
            'phone'      => $request->phone,
        ]);

        return back()->with('success', 'Profile updated');
    }

    // ================= TRAVEL =================
    public function saveTravel(Request $request, Booking $booking, BookingGuest $bookingGuest)
    {
        $request->validate([
            'direction' => 'required|in:arrival,departure',
        ]);

        BookingTravelDetail::updateOrCreate(
            [
                'booking_id' => $booking->id,
                'booking_guest_id' => $bookingGuest->id,
                'direction' => $request->direction,
            ],
            $request->all()
        );

        return back()->with('success', 'Travel saved');
    }

    // ================= MEDICAL =================
    public function saveMedical(Request $request, Booking $booking, BookingGuest $bookingGuest)
    {
        $bookingGuest->medical()->updateOrCreate(
            ['booking_guest_id' => $bookingGuest->id],
            $request->all()
        );

        return back()->with('success', 'Medical saved');
    }

    // ================= FOOD =================
    public function saveFood(Request $request, Booking $booking, BookingGuest $bookingGuest)
    {
        $bookingGuest->foodPreference()->updateOrCreate(
            ['booking_guest_id' => $bookingGuest->id],
            $request->all()
        );

        return back()->with('success', 'Food saved');
    }

    // ================= DRINK =================
    public function saveDrink(Request $request, Booking $booking, BookingGuest $bookingGuest)
    {
        $bookingGuest->drinkPreference()->updateOrCreate(
            ['booking_guest_id' => $bookingGuest->id],
            $request->all()
        );

        return back()->with('success', 'Drink saved');
    }

    // ================= HOUSEKEEPING =================
    public function saveHousekeeping(Request $request, Booking $booking, BookingGuest $bookingGuest)
    {
        $bookingGuest->housekeeping()->updateOrCreate(
            ['booking_guest_id' => $bookingGuest->id],
            $request->all()
        );

        return back()->with('success', 'Housekeeping saved');
    }

    // ================= SERVICE =================
    public function saveService(Request $request, Booking $booking, BookingGuest $bookingGuest)
    {
        $bookingGuest->serviceNote()->updateOrCreate(
            ['booking_guest_id' => $bookingGuest->id],
            $request->all()
        );

        return back()->with('success', 'Service saved');
    }

    // ================= DIVING =================
    public function saveDiving(Request $request, Booking $booking, BookingGuest $bookingGuest)
    {
        $bookingGuest->diving()->updateOrCreate(
            ['booking_guest_id' => $bookingGuest->id],
            $request->all()
        );

        return back()->with('success', 'Diving saved');
    }

    // ================= SURFING =================
    public function saveSurfing(Request $request, Booking $booking, BookingGuest $bookingGuest)
    {
        $bookingGuest->surfing()->updateOrCreate(
            ['booking_guest_id' => $bookingGuest->id],
            $request->all()
        );

        return back()->with('success', 'Surfing saved');
    }

public function saveDocument(Request $request, Booking $booking, BookingGuest $bookingGuest)
{
    $request->validate([
        'document_type' => 'required|string|max:50',
        'file_path' => 'required|file|max:10240',
        'notes' => 'nullable|string',
    ]);

    $file = $request->file('file_path');

    // ✅ STEP 1: extract metadata FIRST (important fix)
    $originalName = $file->getClientOriginalName();
    $mimeType = $file->getMimeType();
    $size = $file->getSize();

    // ✅ STEP 2: prepare folder
    $folder = public_path('storage/guest_documents/'.$booking->id);

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    // ✅ STEP 3: generate unique filename
    $filename = time().'_'.uniqid().'_'.$originalName;

    // ✅ STEP 4: move file to public storage
    $file->move($folder, $filename);

    // ✅ STEP 5: save DB record
    GuestDocument::create([
        'guest_id' => $bookingGuest->guest_id,
        'booking_guest_id' => $bookingGuest->id,
        'booking_id' => $booking->id,

        'document_type' => $request->document_type,
        'file_path' => 'guest_documents/'.$booking->id.'/'.$filename,

        'original_filename' => $originalName,
        'mime_type' => $mimeType,
        'file_size' => $size,

        'uploaded_by' => auth()->id(),
        'uploaded_at' => now(),
        'notes' => $request->notes,
    ]);

    return back()->with('success', 'Document uploaded successfully');
}

public function guestPdf(Booking $booking, BookingGuest $bookingGuest)
{
    $bookingGuest->load([
        'guest',
        'travelDetails',
        'medical',
        'foodPreference',
        'drinkPreference',
        'housekeeping',
        'serviceNote',
        'diving',
        'surfing',
        'documents',
        'rooms',
    ]);

    $pdf = PDF::loadView('admin.booking.pdf.guest_details', compact('booking', 'bookingGuest'));

    return $pdf->stream(
        'guest-details-'.$bookingGuest->id.'.pdf'
    );
}
}
