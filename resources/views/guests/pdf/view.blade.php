<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Guest Details</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 20px; }
        h2 { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 20px; border: 1px solid #ccc; border-radius: 6px; }
        .section-header { padding: 8px 12px; font-weight: bold; color: #fff; }
        .section-body { padding: 12px; }
        dl { display: flex; flex-wrap: wrap; margin: 0; }
        dt, dd { width: 50%; margin: 0; padding: 6px 0; }
        dt { font-weight: bold; }
        .bg-primary { background: #007bff; }
        .bg-info { background: #17a2b8; }
        .bg-warning { background: #ffc107; color: #000; }
        .bg-secondary { background: #6c757d; }
        .bg-success { background: #28a745; }
        .bg-danger { background: #dc3545; }
        .bg-dark { background: #343a40; }
    </style>
</head>
<body>

    <h2>Guest Details</h2>

    {{-- Guest Info --}}
    <div class="section">
        <div class="section-header bg-primary">Guest Information</div>
        <div class="section-body">
            <dl>
                <dt>Trip</dt>
                <dd>{{ $guest->trip->title ?? 'N/A' }}</dd>

                <dt>Name</dt>
                <dd>{{ $guest->name ?? '-' }}</dd>

                <dt>Gender</dt>
                <dd>{{ ucfirst($guest->gender) ?? '-' }}</dd>

                <dt>Email</dt>
                <dd>{{ $guest->email ?? '-' }}</dd>

                <dt>Date of Birth</dt>
                <dd>{{ $guest->dob ?? '-' }}</dd>

                <dt>Passport</dt>
                <dd>{{ $guest->passport ?? '-' }}</dd>

                <dt>Nationality</dt>
                <dd>{{ $guest->nationality ?? '-' }}</dd>

                <dt>Cabin</dt>
                <dd>{{ $guest->cabin ?? '-' }}</dd>

                <dt>Surf Level</dt>
                <dd>{{ $guest->surfLevel ?? '-' }}</dd>

                <dt>Board Details</dt>
                <dd>{{ $guest->boardDetails ?? '-' }}</dd>
            </dl>
        </div>
    </div>

    {{-- Arrival Info --}}
    <div class="section">
        <div class="section-header bg-info">Arrival Information</div>
        <div class="section-body">
            <dl>
                <dt>Date</dt>
                <dd>{{ $guest->arrivalFlightDate ?? '-' }}</dd>

                <dt>Flight Number</dt>
                <dd>{{ $guest->arrivalFlightNumber ?? '-' }}</dd>

                <dt>Airport</dt>
                <dd>{{ $guest->arrivalAirport ?? '-' }}</dd>

                <dt>Time</dt>
                <dd>{{ $guest->arrivalTime ?? '-' }}</dd>

                <dt>Hotel Pickup</dt>
                <dd>{{ $guest->hotelPickup ?? '-' }}</dd>
            </dl>
        </div>
    </div>

    {{-- Departure Info --}}
    <div class="section">
        <div class="section-header bg-warning">Departure Information</div>
        <div class="section-body">
            <dl>
                <dt>Date</dt>
                <dd>{{ $guest->departureFlightDate ?? '-' }}</dd>

                <dt>Flight Number</dt>
                <dd>{{ $guest->departureFlightNumber ?? '-' }}</dd>

                <dt>Airport</dt>
                <dd>{{ $guest->departureAirport ?? '-' }}</dd>

                <dt>Time</dt>
                <dd>{{ $guest->departureTime ?? '-' }}</dd>
            </dl>
        </div>
    </div>

    {{-- Other Info --}}
    <div class="section">
        <div class="section-header bg-secondary">Additional Information</div>
        <div class="section-body">
            <dl>
                <dt>Medical / Dietary</dt>
                <dd>{{ $guest->medicalDietary ?? '-' }}</dd>

                <dt>Special Requests</dt>
                <dd>{{ $guest->specialRequests ?? '-' }}</dd>
            </dl>
        </div>
    </div>

    {{-- Insurance --}}
    <div class="section">
        <div class="section-header bg-success">Insurance</div>
        <div class="section-body">
            <dl>
                <dt>Insurance Name</dt>
                <dd>{{ $guest->insuranceName ?? '-' }}</dd>

                <dt>Policy Number</dt>
                <dd>{{ $guest->policyNumber ?? '-' }}</dd>
            </dl>
        </div>
    </div>

    {{-- Emergency Contact --}}
    <div class="section">
        <div class="section-header bg-danger">Emergency Contact</div>
        <div class="section-body">
            <dl>
                <dt>Name</dt>
                <dd>{{ $guest->emergencyName ?? '-' }}</dd>

                <dt>Relation</dt>
                <dd>{{ $guest->emergencyRelation ?? '-' }}</dd>

                <dt>Phone</dt>
                <dd>{{ $guest->emergencyPhone ?? '-' }}</dd>
            </dl>
        </div>
    </div>

    {{-- Guest Contact --}}
    <div class="section">
        <div class="section-header bg-dark">Guest Contact</div>
        <div class="section-body">
            <dl>
                <dt>WhatsApp</dt>
                <dd>{{ $guest->guestWhatsapp ?? '-' }}</dd>

                <dt>Email</dt>
                <dd>{{ $guest->guestEmail ?? '-' }}</dd>
            </dl>
        </div>
    </div>

</body>
</html>
