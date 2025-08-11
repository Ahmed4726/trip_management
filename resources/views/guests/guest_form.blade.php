<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trip Management | Guest Form</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="{{ asset('plugins/bs-stepper/css/bs-stepper.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
    <div class="navbar-brand">
        {{-- <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
        <span class="brand-text font-weight-light">Trip management</span>
    </div>
    </div>
  </nav>
  <!-- /.navbar -->
        @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif
  <!-- Content Wrapper -->
  <div class="content-wrapper mb-4">
    <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="card mt-3 mb-5 card-default">
          <div class="card-header bg-primary">
            <h3 class="card-title">Guest information</h3>
          </div>
<form action="{{ route('guest.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
<div class="card-body p-0">
  <div class="bs-stepper">
    <div class="bs-stepper-header" role="tablist">
      <div class="step" data-target="#logins-part">
        <button type="button" class="step-trigger" role="tab" aria-controls="logins-part" id="logins-part-trigger">
          <span class="bs-stepper-circle bg-success">1</span>
          <span class="bs-stepper-label">Guest Info</span>
        </button>
      </div>
      <div class="line"></div>
      <div class="step" data-target="#personal-part">
        <button type="button" class="step-trigger" role="tab" aria-controls="personal-part" id="personal-part-trigger">
          <span class="bs-stepper-circle bg-success">2</span>
          <span class="bs-stepper-label">Travel Details</span>
        </button>
      </div>
      <div class="line"></div>
      <div class="step" data-target="#confirmation-part">
        <button type="button" class="step-trigger" role="tab" aria-controls="confirmation-part" id="confirmation-part-trigger">
          <span class="bs-stepper-circle bg-success">3</span>
          <span class="bs-stepper-label">Preferences & Health</span>
        </button>
      </div>
      <div class="line"></div>
      <div class="step" data-target="#guest-part">
        <button type="button" class="step-trigger" role="tab" aria-controls="guest-part" id="guest-part-trigger">
          <span class="bs-stepper-circle bg-success">4  </span>
          <span class="bs-stepper-label">File Uploads & Confirmation</span>
        </button>
      </div>
    </div>

    <div class="bs-stepper-content">
<input type="hidden" name="trip_id" id="tripIdInput">
<input type="hidden" name="otherGuests" id="otherGuests" value="1">

<!-- Step 1 -->
<div id="logins-part" class="content" role="tabpanel" aria-labelledby="logins-part-trigger">
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="name">Name</label>
      <input type="text" name="name" class="form-control" id="name" placeholder="Enter full name">
    </div>
    <div class="form-group col-md-6">
      <label for="gender">Gender</label>
      <select class="form-control" name="gender" id="gender">
        <option value="">Select gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
      </select>
    </div>
  </div>

  <div class="form-row">

      <div class="form-group col-md-6">
      <label for="exampleInputEmail1">Email address</label>
      <input type="email" class="form-control" name="email" id="exampleInputEmail1" placeholder="Enter email">
    </div>
    {{-- <div class="form-group col-md-6">
      <label for="exampleInputPassword1">Password</label>
      <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
    </div> --}}
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="dob">Date of Birth</label>
      <input type="date" class="form-control" name="dob" id="dob">
    </div>
    <div class="form-group col-md-6">
      <label for="passport">Passport Number</label>
      <input type="text" class="form-control" name="passport" id="passport" placeholder="Enter passport number">
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="nationality">Nationality</label>
      <input type="text" class="form-control" name="nationality" id="nationality" placeholder="Enter nationality">
    </div>
    <div class="form-group col-md-6">
      <label for="cabin">Cabin Type</label>
      <select class="form-control" name="cabin" id="cabin">
        <option value="">Select cabin type</option>
        <option value="standard">Standard</option>
        <option value="deluxe">Deluxe</option>
        <option value="suite">Suite</option>
      </select>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="surfLevel">Surf Level</label>
      <select class="form-control" name="surfLevel" id="surfLevel">
        <option value="">Select level</option>
        <option value="beginner">Beginner</option>
        <option value="intermediate">Intermediate</option>
        <option value="advanced">Advanced</option>
      </select>
    </div>
    <div class="form-group col-md-6">
      <label for="boardDetails">Board Details</label>
      <textarea class="form-control" name="boardDetails" id="boardDetails" rows="2" placeholder="Describe your board preferences"></textarea>
    </div>
  </div>

    <button type="button" class="btn btn-primary" onclick="stepper.next()">Next</button>
</div>



     <!-- Step 2 -->




      <!-- Step 3 -->
      <div id="personal-part" class="content" role="tabpanel" aria-labelledby="personal-part-trigger">
      <!-- Step 3 content (updated) -->
<div class="form-row">
  <div class="col-md-12">
    <h5>PICK UP / ARRIVAL</h5>
  </div>
  <div class="form-group col-md-6">
    <label for="arrivalFlightDate">Flight Date</label>
    <input type="date" class="form-control" name="arrivalFlightDate" id="arrivalFlightDate">
  </div>
  <div class="form-group col-md-6">
    <label for="arrivalFlightNumber">Flight Number</label>
    <input type="text" class="form-control" name="arrivalFlightNumber" id="arrivalFlightNumber" placeholder="Enter flight number">
  </div>
  <div class="form-group col-md-6">
    <label for="arrivalAirport">Arrival Airport</label>
    <input type="text" class="form-control" name="arrivalAirport" id="arrivalAirport" placeholder="Enter arrival airport">
  </div>
  <div class="form-group col-md-6">
    <label for="arrivalTime">Arrival Time</label>
    <input type="time" class="form-control" name="arrivalTime" id="arrivalTime">
  </div>
  <div class="form-group col-md-12">
    <label for="hotelPickup">Alternatively Hotel Pick up - Hotel Name and Address</label>
    <textarea class="form-control" name="hotelPickup" id="hotelPickup" rows="2" placeholder="Enter hotel name and address"></textarea>
  </div>

  <div class="col-md-12 mt-4">
    <h5>DROP OFF / DEPARTURE</h5>
  </div>
  <div class="form-group col-md-6">
    <label for="departureFlightDate">Flight Date</label>
    <input type="date" class="form-control" name="departureFlightDate" id="departureFlightDate">
  </div>
  <div class="form-group col-md-6">
    <label for="departureFlightNumber">Flight Number</label>
    <input type="text" class="form-control" name="departureFlightNumber" id="departureFlightNumber" placeholder="Enter flight number">
  </div>
  <div class="form-group col-md-6">
    <label for="departureAirport">Departure Airport</label>
    <input type="text" class="form-control" name="departureAirport" id="departureAirport" placeholder="Enter departure airport">
  </div>
  <div class="form-group col-md-6">
    <label for="departureTime">Departure Time</label>
    <input type="time" class="form-control" name="departureTime" id="departureTime">
  </div>
</div>

        <button type="button" class="btn btn-primary" onclick="stepper.previous()">Previous</button>
        <button type="button" class="btn btn-primary" onclick="stepper.next()">Next</button>
      </div>

      <!-- Step 4 -->
      <div id="confirmation-part" class="content" role="tabpanel" aria-labelledby="confirmation-part-trigger">
        <!-- Step 4 content -->
<div class="form-row">
  <!-- Preferences -->
  <div class="col-md-12">
    <h5>Preferences</h5>
  </div>
    <div class="form-group col-md-6">
    <label for="medicalDietary">Medical/Dietary Requirements</label>
    <textarea class="form-control" name="medicalDietary" id="medicalDietary" rows="2" placeholder="Enter any medical or dietary requirements"></textarea>
  </div>
  <div class="form-group col-md-6">
    <label for="specialRequests">Special Requests</label>
    <textarea class="form-control" name="specialRequests" id="specialRequests" rows="2" placeholder="Enter any special requests"></textarea>
  </div>

  <!-- Insurance -->
  <div class="col-md-12 mt-3">
    <h5>Insurance</h5>
  </div>
  <div class="form-group col-md-6">
    <label for="insuranceName">Insurance Name</label>
    <input type="text" class="form-control" name="insuranceName" id="insuranceName" placeholder="Enter insurance company name">
  </div>
  <div class="form-group col-md-6">
    <label for="policyNumber">Policy Number</label>
    <input type="text" class="form-control" name="policyNumber" id="policyNumber" placeholder="Enter policy number">
  </div>

  <!-- Emergency Contact -->
  <div class="col-md-12 mt-3">
    <h5>Emergency Contact</h5>
  </div>
  <div class="form-group col-md-4">
    <label for="emergencyName">Name</label>
    <input type="text" class="form-control" name="emergencyName" id="emergencyName" placeholder="Contact name">
  </div>
  <div class="form-group col-md-4">
    <label for="emergencyRelation">Relation</label>
    <input type="text" class="form-control" name="emergencyRelation" id="emergencyRelation" placeholder="Relation">
  </div>
  <div class="form-group col-md-4">
    <label for="emergencyPhone">Phone</label>
    <input type="tel" class="form-control" name="emergencyPhone" id="emergencyPhone" placeholder="Phone number">
  </div>

  <!-- Guest Contact -->
  <div class="col-md-12 mt-3">
    <h5>Guest Contact</h5>
  </div>
  <div class="form-group col-md-6">
    <label for="guestWhatsapp">WhatsApp</label>
    <input type="text" class="form-control" name="guestWhatsapp" id="guestWhatsapp" placeholder="WhatsApp number">
  </div>
  <div class="form-group col-md-6">
    <label for="guestEmail">Email</label>
    <input type="email" class="form-control" name="guestEmail" id="guestEmail" placeholder="Email address">
  </div>
</div>

        <button type="button" class="btn btn-primary" onclick="stepper.previous()">Previous</button>
        <button type="button" class="btn btn-primary" onclick="stepper.next()">Next</button>
      </div>

      <!-- Step 5 -->
      <div id="guest-part" class="content" role="tabpanel" aria-labelledby="guest-part-trigger">
   <div class="form-row">
  <div class="form-group col-md-4">
    <label for="imageUpload">Upload Image</label>
    <input type="file" class="form-control-file" name="imageUpload" id="imageUpload" accept="image/*" onchange="previewFile(event, 'image')">
    <div id="imagePreview" class="mt-2"></div>
  </div>

  <div class="form-group col-md-4">
    <label for="pdfUpload">Upload PDF</label>
    <input type="file" class="form-control-file" name="pdfUpload" id="pdfUpload" accept="application/pdf" onchange="previewFile(event, 'pdf')">
    <div id="pdfPreview" class="mt-2"></div>
  </div>

  <div class="form-group col-md-4">
    <label for="videoUpload">Upload Video</label>
    <input type="file" class="form-control-file" name="videoUpload" id="videoUpload" accept="video/*" onchange="previewFile(event, 'video')">
    <div id="videoPreview" class="mt-2"></div>
  </div>
</div>
        <button type="button" class="btn btn-primary" onclick="stepper.previous()">Previous</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
    </div>
  </div>
</div>

</form>


          <!-- <div class="card-footer">
            Visit <a href="https://github.com/Johann-S/bs-stepper/#how-to-use-it">bs-stepper documentation</a> for more examples and information.
          </div> -->
        </div>
      </div>
    </div>
    </div>
  </div>
    <!-- Main Footer -->
  <footer class="main-footer fixed-bottom mt-5">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2025 <a href="https://127.0.0.1">Trip Management</a>.</strong> All rights reserved.
  </footer>
</div>

<!-- Scripts -->
<script src="{{ asset('plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
<script>
 //form steps

  document.addEventListener('DOMContentLoaded', function () {
    window.stepper = new Stepper(document.querySelector('.bs-stepper'))
    const urlParams = new URLSearchParams(window.location.search);
    const tripId = urlParams.get('trip_id');

    if (tripId) {
        const tripIdInput = document.getElementById('tripIdInput');
        if (tripIdInput) {
            tripIdInput.value = tripId;
        }
    }
  });
  //add guest-form
function addGuestForm() {
    const container = document.getElementById('guestContainer');
    const firstForm = container.querySelector('.guest-form');
    const clone = firstForm.cloneNode(true);

    // Clear all input/select/textarea values in the cloned form
    clone.querySelectorAll('input, select, textarea').forEach(el => el.value = '');

    // Add remove button if not already present
    if (!clone.querySelector('.remove-btn')) {
      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'btn btn-sm btn-danger remove-btn mb-3';
      removeBtn.textContent = 'Remove This Guest';
      removeBtn.onclick = () => clone.remove();
      clone.appendChild(removeBtn);
    }

    container.appendChild(clone);
  }

    //file upload
  function previewFile(event, type) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById(`${type}Preview`);
    previewContainer.innerHTML = ''; // clear previous

    if (!file) return;

    let preview;

    if (type === 'image') {
      preview = document.createElement('img');
      preview.src = URL.createObjectURL(file);
      preview.style.maxWidth = '100%';
      preview.style.maxHeight = '150px';
    } else if (type === 'pdf') {
      preview = document.createElement('embed');
      preview.src = URL.createObjectURL(file);
      preview.type = 'application/pdf';
      preview.width = '100%';
      preview.height = '150px';
    } else if (type === 'video') {
      preview = document.createElement('video');
      preview.src = URL.createObjectURL(file);
      preview.controls = true;
      preview.style.width = '100%';
      preview.style.maxHeight = '150px';
    }

    const deleteBtn = document.createElement('button');
    deleteBtn.innerText = 'X';
    deleteBtn.className = 'btn btn-sm btn-danger mt-2 mx-2';
    deleteBtn.onclick = function () {
      document.getElementById(`${type}Upload`).value = ''; // reset input
      previewContainer.innerHTML = ''; // remove preview
    };

    previewContainer.appendChild(preview);
    previewContainer.appendChild(deleteBtn);
  }
</script>
</body>
</html>
