  {{-- resources/views/public/widget.blade.php --}}
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background: #f9f9f9;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      }
      .booking-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
        margin: 20px auto;
        padding: 25px;
        max-width: 800px;
      }
      .trip-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 15px;
        color: #0077b6;
      }
      .trip-meta {
        margin-bottom: 20px;
      }
      .policy-box {
        margin-top: 20px;
      }
      .btn-book, .btn-embed {
        background: #0077b6;
        color: white;
        border-radius: 8px;
        padding: 12px 25px;
        font-size: 1.1rem;
        transition: 0.3s;
        margin-top: 10px;
      }
      .btn-book:hover, .btn-embed:hover {
        background: #005f86;
      }
    </style>
  </head>
  <body>

  <div class="container">
    <div class="booking-card">
      {{-- Trip Title --}}
      <h2 class="trip-title">{{ $trip->title ?? 'Trip Booking' }}</h2>

      {{-- Trip Info --}}
      <div class="trip-meta">
        <p><strong>Boat:</strong> {{ $boatName ?? 'N/A' }}</p>
        <p><strong>Region:</strong> {{ $trip->region ?? 'N/A' }}</p>
        <p><strong>Type:</strong> {{ ucfirst($trip->trip_type) }}</p>
        <p><strong>Guests Allowed:</strong> {{ $trip->guests }}</p>
        <p><strong>Dates:</strong> {{ \Carbon\Carbon::parse($trip->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($trip->end_date)->format('M d, Y') }}</p>
        <p><strong>Price:</strong> ${{ $trip->price }}</p>
      </div>

      {{-- Policies Accordion --}}
      <div class="accordion policy-box" id="policyAccordion">
        {{-- Payment Policy --}}
        @if($trip->paymentPolicy)
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingPayment">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePayment">
              💳 Payment Policy: {{ $trip->paymentPolicy->name }}
            </button>
          </h2>
          <div id="collapsePayment" class="accordion-collapse collapse show">
            <div class="accordion-body">
              <ul>
                <li><strong>Down Payment:</strong> {{ $trip->paymentPolicy->dp_percent }}%</li>
                <li><strong>Balance Due:</strong> {{ $trip->paymentPolicy->balance_days_before_start }} days before start</li>
                <li><strong>Grace Period:</strong> {{ $trip->paymentPolicy->grace_days }} days</li>
                <li><strong>Auto Cancel if DP Overdue:</strong> {{ $trip->paymentPolicy->auto_cancel_if_dp_overdue ? 'Yes' : 'No' }}</li>
              </ul>
            </div>
          </div>
        </div>
        @endif

        {{-- Cancellation Policy --}}
        @if($trip->cancellationPolicy)
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingCancel">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCancel">
              ❌ Cancellation Policy: {{ $trip->cancellationPolicy->name }}
            </button>
          </h2>
          <div id="collapseCancel" class="accordion-collapse collapse">
            <div class="accordion-body">
              @if($trip->cancellationPolicy->rules && count($trip->cancellationPolicy->rules))
                <ul>
                  @foreach($trip->cancellationPolicy->rules as $rule)
                    <li>
                      {{ $rule->days_from }}–{{ $rule->days_to }} days before start: 
                      Penalty {{ $rule->penalty_percent }}%, 
                      Refundable: {{ $rule->refundable ? 'Yes' : 'No' }}
                    </li>
                  @endforeach
                </ul>
              @else
                <p>No cancellation rules defined.</p>
              @endif
            </div>
          </div>
        </div>
        @endif
      </div>

      {{-- Booking Form --}}
  {{-- Booking OR Waitlist --}}
  @if($trip->status === 'sold' || \Carbon\Carbon::parse($trip->end_date)->isPast())
    {{-- Waitlist Form --}}
    <div class="policy-box">
      <h5>Join Waiting List</h5>
    <form action="{{ route('public.waitlist') }}" method="POST" class="mt-3">
      @csrf
      <input type="hidden" name="trip_id" value="{{ $tripId }}">
      <input type="hidden" name="company_id" value="{{ $company_id }}">

      <div class="mb-3">
          <label class="form-label">Your Name</label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required>
          @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
      </div>

      <div class="mb-3">
          <label class="form-label">Your Email</label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" required>
          @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
      </div>

      <div class="mb-3">
          <label class="form-label">Party Size</label>
          <input type="number" name="party_size" class="form-control @error('party_size') is-invalid @enderror" min="1" required>
          @error('party_size')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
      </div>

      <div class="mb-3">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror">
          @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
      </div>

      <div class="mb-3">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"></textarea>
          @error('notes')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
      </div>

      {{-- reCAPTCHA --}}
      <!-- Add your captcha widget here -->

      <button type="submit" class="btn btn-warning w-100">Join Waiting List</button>
  </form>

    </div>
  @else
    {{-- Booking Form --}}
    <form action="{{ route('public.prebook') }}" method="POST" class="mt-4">
      @csrf
      <input type="hidden" name="trip_id" value="{{ $trip->id }}">

      <div class="mb-3">
        <label for="name" class="form-label">Your Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Your Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="guests" class="form-label">Number of Guests</label>
        <input type="number" name="guests" min="1" max="{{ $trip->guests }}" class="form-control" required>
        <div class="form-text">Maximum allowed guests: {{ $trip->guests }}</div>
      </div>

      <button type="submit" class="btn btn-book w-100">Pre-Book Now</button>
    </form>
  @endif


      {{-- Copy Embed Code --}}
      <button class="btn btn-embed w-100" onclick="copyEmbed()">Copy Widget Embed Code</button>
      <input type="text" id="embedCode" class="form-control mt-2" style="display:none;" readonly>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  function copyEmbed() {
    const url = "{{ request()->fullUrl() }}"; // Current widget URL
    const iframeCode = `<iframe src="${url}" width="800" height="800" style="border:0;"></iframe>`;
    const embedInput = document.getElementById('embedCode');
    embedInput.style.display = 'block';
    embedInput.value = iframeCode;
    embedInput.select();
    embedInput.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("Embed code copied! You can paste it in Elementor or any site.");
  }
  </script>

  </body>
  </html>
