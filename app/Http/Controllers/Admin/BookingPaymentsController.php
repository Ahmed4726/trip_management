<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BookingPaymentsController extends Controller
{

    public function index($bookingId)
    {
        $booking = Booking::with(['installments','payments'])->findOrFail($bookingId);
        return view('admin.booking.payments', compact('booking'));
    }


    public function store(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Prevent overpayment
        if($request->amount > $booking->balance_due){
            return redirect()->back()->with('error','Payment exceeds remaining balance.');
        }

        $payment = $booking->payments()->create([
            'amount' => $request->amount,
            'paid_at' => $request->paid_at,
            'payment_method' => $request->payment_method,
            'invoice_number' => 'INV-' . now()->format('YmdHis') . '-' . $booking->id,
        ]);

        // Update booking payment status
        if ($booking->balance_due <= 0) {
            $booking->status = 'Fully Paid';
        } elseif ($booking->amount_paid >= $booking->deposit_amount) {
            $booking->status = 'Partially Paid';
        }

        $booking->save();

        return redirect()->back()->with('success','Payment recorded successfully.');
    }


    // View invoice in browser
    public function invoice(BookingPayment $payment)
    {
        $booking = $payment->booking;

        $pdf = Pdf::loadView('admin.booking.invoice', compact('payment','booking'));

        return $pdf->stream("Invoice_Payment_{$payment->id}.pdf");
    }


    // Download invoice
    public function downloadInvoice(BookingPayment $payment)
    {
        $booking = $payment->booking;

        $pdf = Pdf::loadView('admin.booking.invoice', compact('payment','booking'));

        return $pdf->download("Invoice_Payment_{$payment->id}.pdf");
    }

    public function destroy(BookingPayment $payment)
    {
        $booking = $payment->booking;

        // Delete payment
        $payment->delete();

        // Recalculate booking payment status
        if ($booking->balance_due <= 0) {
            $booking->status = 'Fully Paid';
        } elseif ($booking->amount_paid > 0) {
            $booking->status = 'Partially Paid';
        } else {
            $booking->status = 'Pending';
        }

        $booking->save();

        return redirect()->back()->with('success','Payment deleted successfully.');
    }

}
