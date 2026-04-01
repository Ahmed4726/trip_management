<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Crew Guest Sheet</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            margin: 0;
            color: #000;
        }
/* PAGE MARGINS (IMPORTANT) */
        @page {
            margin: 0px 25px 80px 25px; /* top = header space, bottom = footer space */
        }
        .container {
            padding: 20px 25px;
        }

        /* HEADER (ONLY FIRST PAGE) */
        .header-image {
            width: 100%;
            height: 140px;
        }

        .header-image img {
            width: 100%;
            height: 140px;
            object-fit: cover;
        }

        .title-area {
            text-align: center;
            margin-top: 10px;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 13px;
            margin-top: 4px;
        }

        .divider {
            border-top: 2px solid #000;
            margin: 12px 0 18px;
        }

        /* SECTIONS */
        .section {
            margin-top: 20px;
        }

        .section-title {
            font-weight: bold;
            font-size: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 13px;
        }

        th {
            background: #f0f0f0;
            text-align: center;
        }

        .row-table td {
            width: 50%;
            vertical-align: top;
            padding: 12px;
        }

        .line {
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 100%;
            height: 18px;
        }

        .box {
            border: 1px solid #000;
            padding: 12px;
        }

        /* PAGE BREAK */
        .page-break {
            page-break-before: always;
        }

        .boat-info {
            text-align: center;
            margin-bottom: 10px;
        }

    

    /* ================= FOOTER (REPEATS) ================= */
        .footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
           
        }

        .footer img {
            height: 45px;
        }
  
    </style>
</head>

<body>
<!-- ================= FOOTER ================= -->
<div class="footer">
    <img src="{{ public_path('images/logo.png') }}">
</div>
<div class="container">

    <!-- ================= HEADER (FIRST PAGE ONLY) ================= -->
    <div class="header-image">
        <img src="{{ public_path('images/header.jpg') }}">
    </div>

    <div class="title-area">
        <div class="title">- Cruise Departure Guest Sheet -</div>
        <div class="subtitle">
            Please fill in carefully the details to prepare your departure smoothly
        </div>
    </div>

    <div class="divider"></div>

    <!-- BOAT INFO -->
    <div class="boat-info">
        <strong>{{ $slot->boat->name }}/{{ $slot->slot_type }}/{{ $slot->boat->region }}</strong><br>

        <strong>{{ $slot->start_date->format('d-m-Y') }} → {{ $slot->end_date->format('d-m-Y') }}</strong>
    </div>
      <div class="boat-info">
       Number of Guests: <br>
       Sales: 
    </div>

    <!-- GUEST TABLE -->
    <div class="section">
        <div class="section-title">Guest Details</div>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Room(s)</th>
                    <th>Diet</th>
                    <th>Allergies</th>
                    <th>Equipment</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                    @foreach($booking->guests()->get() as $guest)
                    <tr>
                        <td>{{ $guest->name ?? '-' }}</td>
                        <td>{{ $guest->rooms_list ?? '-' }}</td>
                        <td>{{ $guest->dietary_requirements ?? '-' }}</td>
                        <td>{{ $guest->allergies ?? '-' }}</td>
                        <td>{{ $guest->equipment_sizes ?? '-' }}</td>
                        <td>{{ $guest->operational_notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ================= FORCE NEW PAGE ================= -->
    <div class="page-break"></div>

    <!-- TRAVEL + CONTACT (STARTS NEW PAGE) -->
    <div class="section">
        <table class="row-table">
            <tr>
                <td>
                    <div class="section-title">Travel Information</div>

                    <strong>Arrival Details</strong><br><br>
                    Pickup Time/Date: <span class="line"></span><br><br>
                    Hotel/Airport: <span class="line"></span><br><br>
                    Flight Number: <span class="line"></span><br><br>

                    <strong>Departure Details</strong><br><br>
                    Pickup Time/Date: <span class="line"></span><br><br>
                    Hotel/Airport: <span class="line"></span><br><br>
                    Flight Number: <span class="line"></span>
                </td>

                <td>
                    <div class="section-title">Contact / Emergency</div>

                    Name: <span class="line"></span><br><br>
                    Phone: <span class="line"></span><br><br>
                    Email: <span class="line"></span><br><br>
                    Emergency Contact: <span class="line"></span>
                </td>
            </tr>
        </table>
    </div>

    <!-- FOOD + DRINK -->
    <div class="section">
        <table class="row-table">
            <tr>
                <td>
                    <div class="section-title">Food Preferences</div>
                    <div class="line" style="height:60px;"></div>
                </td>

                <td>
                    <div class="section-title">Drink Preferences</div>
                    <div class="line" style="height:60px;"></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- EXTRA -->
    <div class="section box">
        <div class="section-title">Extra Services</div>
        <div class="line" style="height:60px;"></div>
    </div>

  

</div>

</body>
</html>