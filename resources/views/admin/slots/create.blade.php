@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h4>Create Slot</h4>

<form method="POST" action="{{ route('admin.slots.store') }}">
@csrf

<div class="mb-3">
    <label>Template (optional)</label>
    <select id="templateSelect" class="form-control" name="template_id">
        <option value="">-- Select Template --</option>
        @foreach($templates as $template)
            <option value="{{ $template->id }}" data-json='@json($template)'>
                {{ $template->product_name }} ({{ $template->product_type }})
            </option>
        @endforeach
    </select>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Slot Type</label>
        <select name="slot_type" class="form-control" required>
            @foreach(['Open Trip','Private Charter','Maintenance','Docking','Crossing'] as $type)
                <option value="{{ $type }}" {{ old('slot_type')==$type?'selected':'' }}>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label>Status</label>
        <select name="status" class="form-control" required>
            @foreach(['Available','On Hold','Blocked'] as $status)
                <option value="{{ $status }}" {{ old('status')==$status?'selected':'' }}>{{ $status }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Boat</label>
        <select name="boat_id" class="form-control" id="boatSelect" required>
            <option value="">-- Select Boat --</option>
            @foreach($boats as $boat)
                <option value="{{ $boat->id }}" {{ old('boat_id')==$boat->id?'selected':'' }}>{{ $boat->name }} ({{ $boat->rooms->count() }} rooms)</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label>Region</label>
        <select name="region_id" class="form-control" required>
            @foreach($regions as $region)
                <option value="{{ $region->id }}" {{ old('region_id')==$region->id?'selected':'' }}>{{ $region->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Departure Port</label>
        <select name="departure_port_id" class="form-control" required>
            @foreach($ports as $port)
                <option value="{{ $port->id }}" {{ old('departure_port_id')==$port->id?'selected':'' }}>{{ $port->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label>Arrival Port</label>
        <select name="arrival_port_id" class="form-control" required>
            @foreach($ports as $port)
                <option value="{{ $port->id }}" {{ old('arrival_port_id')==$port->id?'selected':'' }}>{{ $port->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Start Date</label>
        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
    </div>
    <div class="col-md-6">
        <label>End Date</label>
        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
    </div>
</div>

<div class="mb-3">
    <label>Available Rooms</label>
    <select name="rooms[]" class="form-control" multiple id="roomsSelect">
        <!-- rooms dynamically filtered by boat -->
    </select>
</div>
<div class="row">
<div class="col-md-6 mb-3">
    <label>Notes</label>
    <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
</div>
{{-- Company --}}
@if (Auth::user()->hasRole('admin'))
    <div class="col-md-6 mb-3">
        <label>Company</label>
        <select name="company_id" class="form-control" required>
            @foreach($companies as $company)
                <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        </select>
    </div>
@endif
</div>
<button class="btn btn-success">Create Slot</button>
<a href="{{ route('admin.slots.index') }}" class="btn btn-secondary">Cancel</a>
</form>

</div>
</div>

<script>
const boats = @json($boats);
const roomsSelect = document.getElementById('roomsSelect');
document.getElementById('boatSelect').addEventListener('change', function() {
    const boatId = parseInt(this.value);
    roomsSelect.innerHTML = '';
    roomsSelect.multiple = true;

    if (!boatId) return;
    const boat = boats.find(b => b.id === boatId);
    boat.rooms.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.id;
        opt.textContent = r.room_name;
        opt.selected = true;
        roomsSelect.appendChild(opt);
    });
});

// Template auto-fill
document.getElementById('templateSelect').addEventListener('change', function () {
    const opt = this.selectedOptions[0];
    if (!opt || !opt.dataset.json) return;
    const data = JSON.parse(opt.dataset.json);

    document.querySelector('[name=region_id]').value = data.region_id;
    if (data.vessels_allowed.length) {
        document.querySelector('[name=boat_id]').value = data.vessels_allowed[0];
        document.querySelector('#boatSelect').dispatchEvent(new Event('change'));
    }
    if (data.departure_ports.length) document.querySelector('[name=departure_port_id]').value = data.departure_ports[0];
    if (data.arrival_ports.length) document.querySelector('[name=arrival_port_id]').value = data.arrival_ports[0];
    document.querySelector('[name=slot_type]').value = data.product_type;

    const startInput = document.querySelector('[name=start_date]');
    const endInput = document.querySelector('[name=end_date]');
    if (startInput.value) {
        const start = new Date(startInput.value);
        start.setDate(start.getDate() + data.duration_days);
        endInput.valueAsDate = start;
    }
});
</script>
@endsection
