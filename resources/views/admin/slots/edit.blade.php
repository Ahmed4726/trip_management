@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h4>Edit Slot</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.slots.update', $slot->id) }}">
@csrf
@method('PUT')

<div class="mb-3">
    <label>Template (optional)</label>
    <select id="templateSelect" class="form-control" name="template_id">
        <option value="">-- Select Template --</option>
        @foreach($templates as $template)
            <option value="{{ $template->id }}"
                data-json='@json($template)'
                {{ old('template_id', $slot->created_from_template_id) == $template->id ? 'selected' : '' }}>
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
                <option value="{{ $type }}"
                    {{ old('slot_type', $slot->slot_type) == $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label>Status</label>
        <select name="status" class="form-control" required>
            @foreach(['Available','On-Hold','Blocked'] as $status)
                <option value="{{ $status }}"
                    {{ old('status', $slot->status) == $status ? 'selected' : '' }}>
                    {{ $status }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Vessels</label>
        <select name="boats_allowed[]" id="boatsAllowed" class="form-control" multiple required>
            @foreach($boats as $boat)
                <option value="{{ $boat->id }}"
                    {{ in_array($boat->id, old('boats_allowed', $slot->boats->pluck('id')->toArray())) ? 'selected' : '' }}>
                    {{ $boat->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label>Region</label>
        <select name="region_id" class="form-control" required>
            @foreach($regions as $region)
                <option value="{{ $region->id }}"
                    {{ old('region_id', $slot->region_id) == $region->id ? 'selected' : '' }}>
                    {{ $region->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Departure Port</label>
        <select name="departure_port_id" class="form-control" required>
            @foreach($ports as $port)
                <option value="{{ $port->id }}"
                    {{ old('departure_port_id', $slot->departure_port_id) == $port->id ? 'selected' : '' }}>
                    {{ $port->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label>Arrival Port</label>
        <select name="arrival_port_id" class="form-control" required>
            @foreach($ports as $port)
                <option value="{{ $port->id }}"
                    {{ old('arrival_port_id', $slot->arrival_port_id) == $port->id ? 'selected' : '' }}>
                    {{ $port->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Start Date</label>
        <input type="date"
               name="start_date"
               id="startDate"
               class="form-control"
               value="{{ old('start_date', optional($slot->start_date)->format('Y-m-d')) }}"
               required>
    </div>

    <div class="col-md-3">
        <label>Duration (Nights)</label>
        <input type="number"
               name="duration_nights"
               id="durationNights"
               class="form-control"
               value="{{ old('duration_nights', $slot->duration_nights) }}"
               min="0"
               required>
    </div>

    <div class="col-md-3">
        <label>End Date</label>
        <input type="date"
               name="end_date"
               id="endDate"
               class="form-control"
               value="{{ old('end_date', optional($slot->end_date)->format('Y-m-d')) }}"
               readonly>
    </div>
</div>


<div class="mb-3">
    <label>Notes</label>
    <textarea name="notes" class="form-control">{{ old('notes', $slot->notes) }}</textarea>
</div>

<button class="btn btn-primary">Update Slot</button>
<a href="{{ route('admin.slots.index') }}" class="btn btn-secondary">Cancel</a>

</form>
</div>
</div>

<!-- Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){

    const vesselsSelect = document.getElementById('boatsAllowed');
    const vesselsChoices = new Choices(vesselsSelect, {
        removeItemButton: true,
        searchEnabled: true,
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Select vessels'
    });

    const startInput = document.getElementById('startDate');
    const durationInput = document.getElementById('durationNights');
    const endInput = document.getElementById('endDate');

    function calculateEndDate(){
        if(!startInput.value || durationInput.value === '') return;
        const startDate = new Date(startInput.value);
        const nights = parseInt(durationInput.value);
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + nights);
        endInput.valueAsDate = endDate;
    }

    startInput.addEventListener('change', calculateEndDate);
    durationInput.addEventListener('input', calculateEndDate);
    calculateEndDate();

    // Template auto-fill
    document.getElementById('templateSelect').addEventListener('change', function () {
        const opt = this.selectedOptions[0];
        if (!opt || !opt.dataset.json) return;
        const data = JSON.parse(opt.dataset.json);

        if(data.region_id) document.querySelector('[name=region_id]').value = data.region_id;

        vesselsChoices.removeActiveItems();
        if(data.vessels_allowed?.length){
            data.vessels_allowed.forEach(id => vesselsChoices.setChoiceByValue(String(id)));
        }

        if(data.departure_ports?.length)
            document.querySelector('[name=departure_port_id]').value = data.departure_ports[0];

        if(data.arrival_ports?.length)
            document.querySelector('[name=arrival_port_id]').value = data.arrival_ports[0];

        if(data.duration_nights){
            durationInput.value = data.duration_nights;
            calculateEndDate();
        }

        if(data.product_type)
            document.querySelector('[name=slot_type]').value = data.product_type;
    });
});
</script>
@endsection
