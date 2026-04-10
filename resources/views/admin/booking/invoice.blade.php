<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $payment->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 20px; }
        .details, .payments { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .details td, .payments th, .payments td { border: 1px solid #ccc; padding: 8px; }
        .payments th { background: #f0f0f0; }
        .totals { text-align: right; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Booking Invoice</h2>
        <p>Invoice #: {{ $payment->invoice_number }}</p>
        <p>Date: {{ \Carbon\Carbon::parse($payment->paid_at)->format('d-m-Y') }}</p>
    </div>

    <table class="details">
        <tr>
            <td><strong>Booking ID</strong></td>
            <td>{{ $booking->id }}</td>
        </tr>
        <tr>
            <td><strong>Guest Name</strong></td>
            <td>{{ $booking->guest_name }}</td>
        </tr>
        <tr>
            <td><strong>Boat</strong></td>
            <td>{{ $booking->boat->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Room</strong></td>
            <td>{{ $booking->room->room_name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Booking Dates</strong></td>
            <td>
                @if($booking->slot)
                    {{ $booking->slot->start_date->format('d-m-Y') }} → {{ $booking->slot->end_date->format('d-m-Y') }}
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    <h4>Payment Details</h4>
    <table class="payments">
        <thead>
            <tr>
                <th>#</th>
                <th>Amount</th>
                <th>Paid At</th>
                <th>Method</th>
            </tr>
        </thead>
        <tbody>
            @foreach($booking->payments as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ number_format($p->amount,2) }}</td>
                <td>{{ \Carbon\Carbon::parse($p->paid_at)->format('d-m-Y') }}</td>
                <td>{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p><strong>Total Price: </strong> {{ number_format($booking->price_usd,2) }} USD</p>
        <p><strong>Total Paid: </strong> {{ number_format($booking->amount_paid,2) }} USD</p>
        <p><strong>Balance Due: </strong> {{ number_format($booking->balance_due,2) }} USD</p>
    </div>
</body>
</html>
