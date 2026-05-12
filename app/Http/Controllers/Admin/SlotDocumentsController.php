<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slot;
use Barryvdh\DomPDF\Facade\Pdf;

class SlotDocumentsController extends Controller
{
    // Harbormaster Manifest: full name, DOB, passport, nationality
    public function harbormasterManifest(Slot $slot)
    {
        $bookings = $slot->bookings()->with('guests')->get();
        // dd($bookings);
        $pdf = PDF::loadView('admin.slots.documents.harbor_manifest', compact('slot', 'bookings'));
        return $pdf->stream("Harbormaster_Manifest_Slot_{$slot->id}.pdf");
    }

    // Crew Guest Sheet: room assignments, dietary info, allergies, equipment sizes, operational notes
    public function crewGuestSheet(Slot $slot)
    {
        $slot->load([
            'boats',
            'bookings',
            'bookings.bookingGuests.guest',
            'bookings.bookingGuests.travelDetails',
            'bookings.bookingGuests.medical',
            'bookings.bookingGuests.foodPreference',
            'bookings.bookingGuests.drinkPreference',
            'bookings.bookingGuests.housekeeping',
            'bookings.bookingGuests.serviceNote',
            'bookings.bookingGuests.diving',
            'bookings.bookingGuests.surfing',
        ]);

        $pdf = PDF::loadView('admin.slots.documents.crew_guest_sheet', compact('slot'));
        return $pdf->stream("Crew_Guest_Sheet_Slot_{$slot->id}.pdf");
    }
}
