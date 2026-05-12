<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

body{
    font-family: DejaVu Sans;
    color:#222;
    font-size:12px;
}

@page{
    margin:40px 35px;
}

.section{
    margin-bottom:20px;
    page-break-inside: avoid;
}

.title{
    text-align:center;
    font-size:24px;
    font-weight:bold;
    margin-bottom:5px;
}

.subtitle{
    text-align:center;
    color:#777;
    margin-bottom:30px;
}

.card{
    border:1px solid #ddd;
    border-radius:6px;
    padding:14px;
}

.section-title{
    font-size:15px;
    font-weight:bold;
    border-bottom:1px solid #ddd;
    padding-bottom:5px;
    margin-bottom:10px;
}

table{
    width:100%;
    border-collapse:collapse;
}

td{
    padding:7px;
    vertical-align:top;
}

.label{
    width:30%;
    font-weight:bold;
    color:#555;
}

.badge{
    background:#111;
    color:#fff;
    padding:3px 8px;
    font-size:10px;
    border-radius:3px;
}

hr{
    border:none;
    border-top:1px solid #ddd;
    margin:20px 0;
}

</style>
</head>

<body>

<div class="title">
    Guest Detail Sheet
</div>

<div class="subtitle">
    Luxury Yacht Crew Document
</div>

<div class="card section">

    <div class="section-title">
        Guest Profile

        @if($bookingGuest->is_lead_guest)
            <span class="badge">Lead Guest</span>
        @endif
    </div>

    <table>
        <tr>
            <td class="label">Full Name</td>
            <td>
                {{ $bookingGuest->guest->first_name ?? '' }}
                {{ $bookingGuest->guest->last_name ?? '' }}
            </td>
        </tr>

        <tr>
            <td class="label">Email</td>
            <td>{{ $bookingGuest->guest->email ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Phone</td>
            <td>{{ $bookingGuest->guest->phone ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Room</td>
            <td>{{ optional($bookingGuest->rooms)->room_name ?? '-' }}</td>
        </tr>
    </table>

</div>

{{-- TRAVEL --}}
<div class="card section">

    <div class="section-title">Travel Details</div>

    @forelse($bookingGuest->travelDetails as $travel)

        <table>
            <tr>
                <td class="label">Direction</td>
                <td>{{ ucfirst($travel->direction) }}</td>
            </tr>

            <tr>
                <td class="label">Travel Type</td>
                <td>{{ ucfirst($travel->travel_type) }}</td>
            </tr>

            <tr>
                <td class="label">Date</td>
                <td>
                    {{ optional($travel->date)->format('d M Y') }}
                </td>
            </tr>

            <tr>
                <td class="label">Time</td>
                <td>
                    {{ optional($travel->time)->format('h:i A') }}
                </td>
            </tr>

            @if($travel->travel_type == 'flight')

                <tr>
                    <td class="label">Airline</td>
                    <td>{{ $travel->airline }}</td>
                </tr>

                <tr>
                    <td class="label">Flight Number</td>
                    <td>{{ $travel->flight_number }}</td>
                </tr>

            @endif

            @if($travel->notes)

                <tr>
                    <td class="label">Notes</td>
                    <td>{{ $travel->notes }}</td>
                </tr>

            @endif

        </table>

        <hr>

    @empty

        No travel details available.

    @endforelse

</div>

{{-- MEDICAL --}}
<div class="card section">

    <div class="section-title">Medical Information</div>

    <table>

        <tr>
            <td class="label">Medical Conditions</td>
            <td>{{ optional($bookingGuest->medical)->medical_conditions ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Medications</td>
            <td>{{ optional($bookingGuest->medical)->medications ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Allergies</td>
            <td>
                {{ optional($bookingGuest->medical)->food_allergy_details ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">Emergency Contact</td>
            <td>
                {{ optional($bookingGuest->medical)->emergency_contact_name ?? '-' }}
                <br>
                {{ optional($bookingGuest->medical)->emergency_contact_phone ?? '' }}
            </td>
        </tr>

    </table>

</div>

{{-- FOOD --}}
<div class="card section">

    <div class="section-title">Food Preferences</div>

    <table>

        <tr>
            <td class="label">Dietary Type</td>
            <td>{{ optional($bookingGuest->foodPreference)->dietary_type ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Favorite Foods</td>
            <td>{{ optional($bookingGuest->foodPreference)->favorite_foods ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Dislikes</td>
            <td>{{ optional($bookingGuest->foodPreference)->dislikes ?? '-' }}</td>
        </tr>

    </table>

</div>

{{-- DRINKS --}}
<div class="card section">

    <div class="section-title">Drink Preferences</div>

    <table>

        <tr>
            <td class="label">Wine</td>
            <td>{{ optional($bookingGuest->drinkPreference)->wine_preference ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Cocktails</td>
            <td>{{ optional($bookingGuest->drinkPreference)->cocktail_preference ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Coffee</td>
            <td>{{ optional($bookingGuest->drinkPreference)->coffee_preference ?? '-' }}</td>
        </tr>

    </table>

</div>

{{-- DIVING --}}
<div class="card section">

    <div class="section-title">Diving</div>

    <table>

        <tr>
            <td class="label">Certification</td>
            <td>
                {{ optional($bookingGuest->diving)->certification_agency ?? '-' }}
                -
                {{ optional($bookingGuest->diving)->certification_level ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">Logged Dives</td>
            <td>{{ optional($bookingGuest->diving)->logged_dives ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Equipment</td>
            <td>
                Wetsuit:
                {{ optional($bookingGuest->diving)->wetsuit_size ?? '-' }}
                |
                Fin:
                {{ optional($bookingGuest->diving)->fin_size ?? '-' }}
            </td>
        </tr>

    </table>

</div>

{{-- SURFING --}}
<div class="card section">

    <div class="section-title">Surfing</div>

    <table>

        <tr>
            <td class="label">Level</td>
            <td>{{ optional($bookingGuest->surfing)->surf_level ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Board Type</td>
            <td>{{ optional($bookingGuest->surfing)->board_type ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Board Length</td>
            <td>{{ optional($bookingGuest->surfing)->board_length ?? '-' }}</td>
        </tr>

    </table>

</div>

{{-- SERVICE --}}
<div class="card section">

    <div class="section-title">Service Notes</div>

    <table>

        <tr>
            <td class="label">VIP Level</td>
            <td>{{ optional($bookingGuest->serviceNote)->vip_level ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Celebration</td>
            <td>{{ optional($bookingGuest->serviceNote)->celebration_type ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Internal Notes</td>
            <td>{{ optional($bookingGuest->serviceNote)->internal_service_notes ?? '-' }}</td>
        </tr>

    </table>

</div>

</body>
</html>
