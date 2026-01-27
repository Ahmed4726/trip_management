@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h4>Edit Template</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.templates.update', $template->id) }}">
@csrf
@method('PUT')

<div class="row mb-3">
    <div class="col-md-6">
        <label>Product Name</label>
        <input type="text" name="product_name" class="form-control"
               value="{{ old('product_name', $template->product_name) }}" required>
    </div>
    <div class="col-md-6">
        <label>Product Type</label>
        <select name="product_type" class="form-control" required>
            @foreach(['Open Trip','Private Charter','Surf Charter','Dive Charter','Ops Block'] as $type)
                <option value="{{ $type }}"
                    {{ old('product_type', $template->product_type) == $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label>Region</label>
        <select name="region_id" class="form-control" required>
            @foreach($regions as $region)
                <option value="{{ $region->id }}"
                    {{ old('region_id', $template->region_id) == $region->id ? 'selected' : '' }}>
                    {{ $region->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-8">
        <label>Vessels Allowed</label>
        <select name="vessels_allowed[]" id="vesselsAllowed" class="form-control" multiple required>
            @foreach($boats as $boat)
                <option value="{{ $boat->id }}"
                    {{ in_array($boat->id, old('vessels_allowed', $template->vessels_allowed ?? [])) ? 'selected' : '' }}>
                    {{ $boat->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <label>Duration Days</label>
        <input type="number" name="duration_days" class="form-control" min="1"
               value="{{ old('duration_days', $template->duration_days) }}" required>
    </div>
    <div class="col-md-3">
        <label>Duration Nights</label>
        <input type="number" name="duration_nights" class="form-control" min="0"
               value="{{ old('duration_nights', $template->duration_nights) }}" required>
    </div>
    <div class="col-md-3">
        <label>Min Bookings</label>
        <input type="number" name="min_bookings" class="form-control" min="0"
               value="{{ old('min_bookings', $template->min_bookings) }}">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Departure Ports</label>
        <select name="departure_ports[]" class="form-control" multiple>
            @foreach($ports as $port)
                <option value="{{ $port->id }}"
                    {{ in_array($port->id, old('departure_ports', $template->departure_ports ?? [])) ? 'selected' : '' }}>
                    {{ $port->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label>Arrival Ports</label>
        <select name="arrival_ports[]" class="form-control" multiple>
            @foreach($ports as $port)
                <option value="{{ $port->id }}"
                    {{ in_array($port->id, old('arrival_ports', $template->arrival_ports ?? [])) ? 'selected' : '' }}>
                    {{ $port->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="mb-3">
    <label>Inclusions</label>
    <textarea name="inclusions" class="form-control">{{ old('inclusions', $template->inclusions) }}</textarea>
</div>

<div class="mb-3">
    <label>Exclusions</label>
    <textarea name="exclusions" class="form-control">{{ old('exclusions', $template->exclusions) }}</textarea>
</div>

<div class="mb-3">
    <label>Obligatory Surcharges</label>
    <textarea name="obligatory_surcharges" class="form-control">{{ old('obligatory_surcharges', $template->obligatory_surcharges) }}</textarea>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Experience Level</label>
        <select name="experience_level" class="form-control">
            @foreach(['Beginner','Intermediate','Advanced'] as $level)
                <option value="{{ $level }}"
                    {{ old('experience_level', $template->experience_level) == $level ? 'selected' : '' }}>
                    {{ $level }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label>Requirements Description</label>
        <input type="text" name="requirements_description" class="form-control"
               value="{{ old('requirements_description', $template->requirements_description) }}">
    </div>
</div>

<div class="mb-3">
    <label>Public Comment</label>
    <textarea name="public_comment" class="form-control">{{ old('public_comment', $template->public_comment) }}</textarea>
</div>

<div class="mb-3">
    <label>Internal Comment</label>
    <textarea name="internal_comment" class="form-control">{{ old('internal_comment', $template->internal_comment) }}</textarea>
</div>

<button class="btn btn-primary">Update Template</button>
<a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>

<!-- Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    new Choices('#vesselsAllowed', {
        removeItemButton: true,
        searchEnabled: true,
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Select vessels'
    });
});
</script>
@endsection
