<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Crew Guest Sheet</title>

<style>
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 13px;
    color: #222;
}

/* PAGE */
@page {
    margin: 0px 30px 70px 30px;
}

/* HEADER */
.header {
    text-align: center;
    margin-bottom: 15px;
}

.header img {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.title {
    font-size: 20px;
    font-weight: bold;
    margin-top: 10px;
}

.subtitle {
    font-size: 12px;
    color: #666;
}

/* BOAT INFO */
.boat-box {
    text-align: center;
    margin: 15px 0;
    font-size: 13px;
}

.divider {
    border-top: 2px solid #000;
    margin: 10px 0 20px;
}

/* GUEST CARD */
.guest-card {
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 14px;
    margin-bottom: 20px;
    page-break-inside: avoid;
}

/* HEADER */
.guest-header {
    font-size: 15px;
    font-weight: bold;
    margin-bottom: 10px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}

/* GRID */
.grid {
    width: 100%;
    border-collapse: collapse;
}

.grid td {
    padding: 6px 4px;
    vertical-align: top;
}

.label {
    font-weight: bold;
    color: #555;
    width: 30%;
}

.value {
    width: 70%;
}

/* SECTION TITLE */
.section-title {
    font-size: 13px;
    font-weight: bold;
    margin-top: 12px;
    margin-bottom: 6px;
    border-bottom: 1px solid #eee;
    padding-bottom: 3px;
}

/* FOOTER */
.footer {
    position: fixed;
    bottom: -50px;
    text-align: center;
}

.footer img {
    height: 40px;
}
</style>
</head>

<body>

<!-- FOOTER -->
<div class="footer">
    <img src="{{ public_path('images/logo.png') }}">
</div>

<!-- HEADER -->
<div class="header">
    <img src="{{ public_path('images/header.jpg') }}">
    <div class="title">Cruise Guest Manifest</div>
    <div class="subtitle">Confidential Crew Document</div>
</div>

<div class="divider"></div>

<!-- BOAT INFO -->
<div class="boat-box">
    <strong>{{ $slot->boats->first()->name }}</strong> |
    {{ $slot->slot_type }} |
    {{ $slot->boats->first()->region }} <br>

    {{ $slot->start_date->format('d M Y') }} → {{ $slot->end_date->format('d M Y') }} <br>

    Total Guests:
    {{ $slot->bookings->sum(fn($b) => $b->bookingGuests->count()) }}
</div>

<div class="divider"></div>

<!-- GUESTS -->
@foreach($slot->bookings as $booking)
    @foreach($booking->bookingGuests as $bookingGuest)

        <div class="guest-card">

            <!-- NAME -->
            <div class="guest-header">
                {{ $bookingGuest->guest->name ?? 'Guest' }}
            </div>

            <!-- BASIC INFO -->
            <table class="grid">
                <tr>
                    <td class="label">Room</td>
                    <td class="value">{{ $bookingGuest->rooms->room_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Diet</td>
                    <td class="value">{{ optional($bookingGuest->foodPreference)->dietary_type ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Allergies</td>
                    <td class="value">{{ optional($bookingGuest->medical)->food_allergy_details ?? '-' }}</td>
                </tr>
            </table>

            <!-- ACTIVITIES -->
            <div class="section-title">Activities</div>
            <table class="grid">
                <tr>
                    <td class="label">Diving</td>
                    <td class="value">{{ optional($bookingGuest->diving)->experience ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Surfing</td>
                    <td class="value">{{ optional($bookingGuest->surfing)->level ?? '-' }}</td>
                </tr>
            </table>

            <!-- TRAVEL -->
            <div class="section-title">Travel</div>
            @foreach($bookingGuest->travelDetails as $travel)
                <table class="grid">
                    <tr>
                        <td class="label">Type</td>
                        <td class="value">{{ $travel->travel_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date</td>
                        <td class="value">{{ $travel->date }}</td>
                    </tr>
                    <tr>
                        <td class="label">Location</td>
                        <td class="value">{{ $travel->location_address ?? '-' }}</td>
                    </tr>
                </table>
                <br>
            @endforeach

            <!-- NOTES -->
            <div class="section-title">Crew Notes</div>
            <div>
                {{ optional($bookingGuest->serviceNote)->notes ?? '-' }}
            </div>

        </div>

    @endforeach
@endforeach

</body>
</html>
