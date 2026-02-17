@extends('layouts.admin')
@section('content')
    <div class="content-wrapper">
        <div class="container mx-2 pt-3">

            <h4>Create Booking</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @foreach (['success','error'] as $msg)
                    @if(session($msg))
                        <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }}">
                            {{ session($msg) }}
                        </div>
                    @endif
                @endforeach

                <form method="POST" action="{{ route('admin.bookings.store') }}">
                    @csrf

                    {{-- Slot --}}
                    <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Slot</label>
                            <select id="slotSelect" name="slot_id" class="form-control">
                                <option value="1">-- Select Slot --</option>

                                @foreach($slots as $slot)
                                    @php
                                        $isDisabled = in_array($slot['id'], $bookedSlotIds);
                                        $slotType = $slot['slot_type'] ?? 'Open Trip';
                                    @endphp

                                    <option value="{{ $slot['id'] }}"
                                            data-json='@json($slot)'
                                            @if($isDisabled) disabled style="color: gray;" @endif
                                    >
                                        {{-- Boat names --}}
                                        @if(!empty($slot['boats']) && count($slot['boats']) >= 1)
                                            {{ collect($slot['boats'])->pluck('name')->join(', ') }}
                                        @elseif(!empty($slot['boat']))
                                            {{ $slot['boat']['name'] }}
                                        @else
                                            -
                                        @endif

                                        | {{ \Carbon\Carbon::parse($slot['start_date'])->format('d-m-Y') }}
                                        → {{ \Carbon\Carbon::parse($slot['end_date'])->format('d-m-Y') }}

                                        @if($isDisabled && $slotType === 'Private Charter')
                                            (Booked)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                    </div>

                    {{-- Source --}}
                    <div class="col-md-6 mb-3">
                        <label>Source</label>
                        <select name="source" id="sourceSelect" class="form-control">
                            <option value="Direct">Direct</option>
                            <option value="Agent">Agent</option>
                        </select>
                    </div>

                    {{-- Agent --}}
                    <div class="col-md-6 mb-3" id="agentWrapper">
                        <label>Agent</label>
                        <select name="agent_id" id="agentSelect" class="form-control" disabled>
                            <option value="">-- Select Agent --</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Salesperson</label>
                        <select name="salesperson_id" class="form-control" required>
                            <option value="">-- Select Salesperson --</option>
                            @foreach($salespersons as $sp)
                                <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Pending">Pending</option>
                            <option value="DP Paid">DP Paid</option>
                            <option value="Full Paid">Full Paid</option>
                            <option value="Waiting List">Waiting List</option>
                            <option value="Canceled">Canceled</option>
                        </select>
                    </div>
                    </div>

                    {{-- Inline Slot Creation --}}
                    <div id="inlineSlotWrapper" class="border p-3 mb-3 d-none">
                        <h5>Inline Slot Creation</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Boat</label>
                                <select name="boat_id" id="inlineBoatSelect" class="form-control">
                                    <option value="">-- Select Boat --</option>
                                    @foreach($boats as $boat)
                                        <option value="{{ $boat->id }}">{{ $boat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Start Date</label>
                                <input type="date" name="start_date" id="inlineStartDate" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>End Date</label>
                                <input type="date" name="end_date" id="inlineEndDate" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- Price / Currency --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Price (Selected Currency)</label>
                            <input type="number" id="price" name="price" step="0.01" class="form-control" required value="{{ old('price') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Currency</label>
                            <select id="currency" name="currency" class="form-control" required>
                                @foreach($currencies as $curr)
                                    <option value="{{ $curr->id }}" data-rate="{{ $curr->rate }}" {{ old('currency')==$curr->id?'selected':'' }}>
                                        {{ $curr->symbol }} - {{ $curr->name }} Rate: {{ $curr->rate }} USD
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Price in USD</label>
                            <input type="number" name="price_usd" id="price_usd" class="form-control" readonly>
                        </div>
                    </div>

                    {{-- Rooms --}}
                    <div class="mb-3">
                        <label class="fw-bold">Rooms & Guests</label>
                        <div id="roomMessage" class="text-muted small mb-2">
                            Please select a slot or create an inline slot and select a boat to see rooms.
                        </div>
                        <div id="roomWrapper" class="row g-2"></div>
                    </div>

                    {{-- Guest Modal Trigger --}}
                    <button type="button" id="addGuestBtn" class="btn btn-sm btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#guestModal">
                        + Add Guest
                    </button>

                    {{-- Rate / Payment / Cancellation --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Rate Plan</label>
                            <select name="rate_plan_id" class="form-control" required>
                                @foreach($ratePlans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Payment Policy</label>
                            <select name="payment_policy_id" class="form-control" required>
                                @foreach($paymentPolicies as $policy)
                                    <option value="{{ $policy->id }}">{{ $policy->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Cancellation Policy</label>
                            <select name="cancellation_policy_id" class="form-control" required>
                                @foreach($cancellationPolicies as $policy)
                                    <option value="{{ $policy->id }}">{{ $policy->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>

                    <button class="btn btn-success">Create Booking</button>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
        </div>
    </div>

    {{-- Guest Modal --}}
    <div class="modal fade" id="guestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form id="guestForm">@csrf
            <div class="modal-header">
            <h5 class="modal-title">Add Guest</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            <div class="mb-3"><label>First Name</label><input type="text" name="first_name" class="form-control" required></div>
            <div class="mb-3"><label>Last Name</label><input type="text" name="last_name" class="form-control" required></div>
            <div class="mb-3"><label>Gender</label><select name="gender" class="form-control" required>
                <option value="">-- Select Gender --</option><option value="Male">Male</option><option value="Female">Female</option></select></div>
            <div class="mb-3"><label>Date of Birth</label><input type="date" name="dob" class="form-control" required></div>
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"></div>
            <div class="mb-3"><label>Passport</label><input type="text" name="passport" class="form-control"></div>
            <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
            <div class="mb-3"><label>Address</label><input type="text" name="address" class="form-control"></div>
            <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Save Guest</button></div>
        </form>
        </div>
    </div>
    </div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
document.addEventListener('DOMContentLoaded', function(){

    const slots = @json($slots);
    const guests = @json($guests);
    const roomUsageBySlot = @json($roomUsageBySlot);
    const choicesInstances = []; // declare globally


    const slotSelect = document.getElementById('slotSelect');
    const roomWrapper = document.getElementById('roomWrapper');
    const roomMessage = document.getElementById('roomMessage');
    const agentSelect = document.getElementById('agentSelect');
    const sourceSelect = document.getElementById('sourceSelect');
    const inlineSlotWrapper = document.getElementById('inlineSlotWrapper');

    /* -----------------------------
       SOURCE / AGENT TOGGLE
    ------------------------------ */
    function toggleAgent(){
        agentSelect.disabled = sourceSelect.value !== 'Agent';
        if(agentSelect.disabled) agentSelect.value = '';
    }
    toggleAgent();
    sourceSelect.addEventListener('change', toggleAgent);

    /* -----------------------------
       RENDER ROOMS
    ------------------------------ */
    function renderRoomsByBoat(slot) {
        roomWrapper.innerHTML = '';

        const isPrivate = slot.slot_type === 'Private Charter';

        if (isPrivate) {
            roomMessage.textContent = 'Private Charter: room assignment is optional.';
        } else {
            roomMessage.textContent = 'Open Trip: assign any available rooms.';
        }
        roomMessage.style.display = 'block';

        function addRooms(boat) {
            if (!boat.rooms || !boat.rooms.length) return;

            const boatHeader = document.createElement('div');
            boatHeader.className = 'col-12 mb-2';
            boatHeader.innerHTML = `<strong>Boat: ${boat.name}</strong>`;
            roomWrapper.appendChild(boatHeader);

            boat.rooms.forEach(room => {
                const cap = parseInt(room.capacity || 0) + parseInt(room.extra_beds || 0);
                const usage = roomUsageBySlot?.[slot.id]?.[room.id] || {};
                const used = usage.used || 0;
                const remaining = cap - used;
                const assignedGuests = usage.guests || [];

                const div = document.createElement('div');
                div.className = 'col-md-4';
                div.innerHTML = `
                    <label class="card p-2 h-100">
                        <strong>${room.room_name}</strong><br>
                        <small class="text-muted">
                            ${remaining > 0 ? `Remaining ${remaining} of ${cap}` : 'Fully booked'}
                        </small>

                        <select
                            multiple
                            class="form-control room-guests mt-2"
                            name="guest_rooms[${room.id}][]"
                            data-cap="${cap}"
                            data-remaining="${remaining}"
                            ${(!isPrivate && remaining <= 0) ? 'disabled' : ''}
                        ></select>

                        <div class="assigned-guests mt-1 text-muted"></div>
                        <div class="room-full text-danger mt-1" style="display:${remaining <= 0 ? 'block' : 'none'};">
                            Room fully booked for this trip
                        </div>
                    </label>
                `;
                roomWrapper.appendChild(div);

                const select = div.querySelector('select.room-guests');
                const fullMsg = div.querySelector('.room-full');

                // Add all guests to select, preselect already assigned ones
                guests.forEach(g => {
                    const opt = document.createElement('option');
                    opt.value = g.id;
                    opt.text = ((g.first_name || '') + ' ' + (g.last_name || '')).trim() || g.name;

                    // Preselect if guest is already assigned
                    if (assignedGuests.includes(g.id)) {
                        opt.selected = true;
                    }

                    select.add(opt);
                });

                // Initialize Choices.js
                const choices = new Choices(select, {
                    removeItemButton: true,
                    searchEnabled: true,
                    shouldSort: false,
                    placeholder: true,
                    placeholderValue: 'Select guests'
                });

                // Disable select if fully booked
                if (!isPrivate && remaining <= 0) {
                    select.disabled = true;
                }

                choicesInstances.push(choices);

                function renderAssignedGuests() {
                    const container = div.querySelector('.assigned-guests');
                    container.innerHTML = '';

                    // Show all selected guests as badges
                    Array.from(select.selectedOptions).forEach(opt => {
                        const guestId = parseInt(opt.value, 10);
                        const isPreAssigned = assignedGuests.includes(guestId);

                        const badge = document.createElement('span');
                        badge.className = 'badge ' + (isPreAssigned ? 'bg-secondary' : 'bg-primary') + ' me-1 mb-1';
                        badge.style.cursor = 'pointer';
                        badge.textContent = opt.text + ' ×';

                        // Only allow removing if room has capacity or private
                        if (remaining > 0 || isPrivate) {
                            badge.onclick = () => {
                                Swal.fire({
                                    title: 'Remove guest?',
                                    text: opt.text,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Remove'
                                }).then(res => {
                                    if (res.isConfirmed) {
                                        choices.removeActiveItemsByValue(opt.value);
                                        updateRoomState();
                                    }
                                });
                            };
                        }

                        container.appendChild(badge);
                    });
                }

                function updateRoomState() {
                    renderAssignedGuests();

                    const selectedCount = select.selectedOptions.length;

                    if (!isPrivate && selectedCount >= remaining) {
                        select.closest('label').querySelector('.choices').style.display = 'none';
                        fullMsg.style.display = 'block';
                    } else {
                        select.closest('label').querySelector('.choices').style.display = 'block';
                        if (remaining > 0) fullMsg.style.display = 'none';
                    }
                }

                // Initial render
                updateRoomState();

                select.addEventListener('change', () => {
                    if (!isPrivate && select.selectedOptions.length > remaining) {
                        choices.removeActiveItemsByValue(
                            select.selectedOptions[select.selectedOptions.length - 1].value
                        );
                        Swal.fire({
                            icon: 'warning',
                            title: 'Room Full',
                            text: 'This room has no remaining capacity.'
                        });
                    }
                    updateRoomState();
                });
            });
        }

        if (slot.boat) addRooms(slot.boat);
        if (slot.boats && slot.boats.length) slot.boats.forEach(addRooms);
    }





    /* -----------------------------
       SLOT CHANGE
    ------------------------------ */
    slotSelect.addEventListener('change', function(){
        const slot = slots.find(s => s.id == this.value);

        if(!slot){
            // inlineSlotWrapper.classList.remove('d-none');
            roomWrapper.innerHTML = '';
            roomMessage.style.display = 'block';
            return;
        }

        inlineSlotWrapper.classList.add('d-none');
        renderRoomsByBoat(slot);
    });


   // Add Guest via modal
    $('#guestForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);

        $.ajax({
            url: '{{ route("admin.guests.store") }}',
            method: 'POST',
            data: form.serialize(),

            success: function (guest) {
                guests.push(guest);

                choicesInstances.forEach(instance => {
                    instance.setChoices(
                        [
                            {
                                value: String(guest.id),
                                label: guest.name,
                                selected: false,
                                disabled: false
                            }
                        ],
                        'value',
                        'label',
                        false // append, do not replace
                    );
                });

                $('#guestModal').modal('hide');
                form[0].reset();

                Swal.fire({
                    icon: 'success',
                    title: 'Guest Added',
                    text: `${guest.name} has been added successfully.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            },

            error: function (xhr) {
                let msg = 'Failed to create guest';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
            }
        });
    });

    roomMessage.style.display = 'block';

    /* -----------------------------
       PRICE USD
    ------------------------------ */
    const priceInput = document.getElementById('price');
    const currencySelect = document.getElementById('currency');
    const priceUsdInput = document.getElementById('price_usd');

    function updateUSD(){
        const rate = parseFloat(currencySelect.selectedOptions[0].dataset.rate);
        const price = parseFloat(priceInput.value) || 0;
        priceUsdInput.value = (price * rate).toFixed(2);
    }

    priceInput.addEventListener('input', updateUSD);
    currencySelect.addEventListener('change', updateUSD);
    updateUSD();

});
</script>



@endsection
