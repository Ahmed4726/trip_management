@extends('layouts.admin')
@section('content')
    <div class="content-wrapper">
        <div class="container mx-2 pt-3">

            <h4>Edit Booking</h4>

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

                <form method="POST" action="{{ route('admin.bookings.update', $booking->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Slot --}}
                    <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Slot</label>

                        @php
                            $currentSlot = collect($slots)->firstWhere('id', $booking->slot_id);
                            $slotName = '-';

                            if ($currentSlot) {
                                // Show multiple boats
                                if (!empty($currentSlot['boats']) && count($currentSlot['boats']) >= 1) {
                                    $slotName = collect($currentSlot['boats'])->pluck('name')->join(', ');
                                }
                                // Single boat (legacy)
                                elseif (!empty($currentSlot['boat'])) {
                                    $slotName = $currentSlot['boat']['name'];
                                }

                                // Add dates
                                $slotName .= ' | ' . \Carbon\Carbon::parse($currentSlot['start_date'])->format('d-m-Y')
                                            . ' → ' . \Carbon\Carbon::parse($currentSlot['end_date'])->format('d-m-Y');

                                // Indicate current
                                $slotName .= ' (Current)';
                            }
                        @endphp

                        {{-- Disabled input showing current slot --}}
                        <input type="text" class="form-control" value="{{ $slotName }}" disabled>

                        {{-- Hidden input to submit slot_id --}}
                        <input type="hidden" name="slot_id" value="{{ $booking->slot_id }}">
                    </div>


                    {{-- Source --}}
                    <div class="col-md-6 mb-3">
                        <label>Source</label>
                        <select name="source" id="sourceSelect" class="form-control">
                            <option value="Direct" {{ old('source', $booking->source)=='Direct'?'selected':'' }}>Direct</option>
                            <option value="Agent" {{ old('source', $booking->source)=='Agent'?'selected':'' }}>Agent</option>
                        </select>
                    </div>

                    {{-- Agent --}}
                    <div class="col-md-6 mb-3" id="agentWrapper">
                        <label>Agent</label>
                        <select name="agent_id" id="agentSelect" class="form-control" {{ old('source', $booking->source)!='Agent'?'disabled':'' }}>
                            <option value="">-- Select Agent --</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ old('agent_id', $booking->agent_id)==$agent->id?'selected':'' }}>
                                    {{ $agent->first_name }} {{ $agent->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Salesperson</label>
                        <select name="salesperson_id" class="form-control" required>
                            <option value="">-- Select Salesperson --</option>
                            @foreach($salespersons as $sp)
                                <option value="{{ $sp->id }}" {{ old('salesperson_id', $booking->salesperson_id)==$sp->id?'selected':'' }}>
                                    {{ $sp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    </div>

                    {{-- Status --}}
                    <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            @foreach(['Pending','DP Paid','Full Paid','Waiting List','Canceled'] as $status)
                                <option value="{{ $status }}" {{ old('status', $booking->status)==$status?'selected':'' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
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
                                        <option value="{{ $boat->id }}" {{ old('boat_id', $booking->boat_id)==$boat->id?'selected':'' }}>
                                            {{ $boat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Start Date</label>
                                <input type="date" name="start_date" id="inlineStartDate" class="form-control" value="{{ old('start_date', $booking->slot->start_date ?? '') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>End Date</label>
                                <input type="date" name="end_date" id="inlineEndDate" class="form-control" readonly value="{{ old('end_date', $booking->slot->end_date ?? '') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Price / Currency --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Price (Selected Currency)</label>
                            <input type="number" id="price" name="price" step="0.01" class="form-control" required value="{{ old('price', $booking->price) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Currency</label>
                            <select id="currency" name="currency" class="form-control" required>
                                @foreach($currencies as $curr)
                                    <option value="{{ $curr->id }}" data-rate="{{ $curr->rate }}"
                                        {{ old('currency', $booking->currency)==$curr->id?'selected':'' }}>
                                        {{ $curr->symbol }} - {{ $curr->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Price in USD</label>
                            <input type="number" name="price_usd" id="price_usd" class="form-control" readonly value="{{ old('price_usd', $booking->price_usd) }}">
                        </div>
                    </div>

                    {{-- Rooms & Guests --}}
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
                                    <option value="{{ $plan->id }}" {{ old('rate_plan_id', $booking->rate_plan_id)==$plan->id?'selected':'' }}>
                                        {{ $plan->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Payment Policy</label>
                            <select name="payment_policy_id" class="form-control" required>
                                @foreach($paymentPolicies as $policy)
                                    <option value="{{ $policy->id }}" {{ old('payment_policy_id', $booking->payment_policy_id)==$policy->id?'selected':'' }}>
                                        {{ $policy->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Cancellation Policy</label>
                            <select name="cancellation_policy_id" class="form-control" required>
                                @foreach($cancellationPolicies as $policy)
                                    <option value="{{ $policy->id }}" {{ old('cancellation_policy_id', $booking->cancellation_policy_id)==$policy->id?'selected':'' }}>
                                        {{ $policy->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control">{{ old('notes', $booking->notes) }}</textarea>
                    </div>

                    <button class="btn btn-success">Update Booking</button>
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
            <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
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


{{-- JS scripts same as create page --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const guests = @json($guests ?? []);
    const booking = @json($booking ?? []);
    const bookingRoomGuests = @json($bookingRoomGuests ?? []);

    const roomWrapper = document.getElementById('roomWrapper');
    const roomMessage = document.getElementById('roomMessage');
    const agentSelect = document.getElementById('agentSelect');
    const sourceSelect = document.getElementById('sourceSelect');
    const inlineSlotWrapper = document.getElementById('inlineSlotWrapper');
    const priceInput = document.getElementById('price');
    const currencySelect = document.getElementById('currency');
    const priceUsdInput = document.getElementById('price_usd');

    const choicesInstances = [];

    // -----------------------------
    // Agent toggle
    // -----------------------------
    function toggleAgent() {
        agentSelect.disabled = sourceSelect.value !== 'Agent';
        if (agentSelect.disabled) agentSelect.value = '';
    }
    toggleAgent();
    sourceSelect.addEventListener('change', toggleAgent);

    // -----------------------------
    // Render rooms with pre-selected guests
    // -----------------------------
    function renderRoomsBySlot(slot) {
        roomWrapper.innerHTML = '';
        let roomsExist = false;
        const optionalRooms = slot.slot_type === 'Private Charter';

        function addRooms(boat) {
            if (!boat.rooms || !boat.rooms.length) return;
            roomsExist = true;

            // Boat header
            const boatHeader = document.createElement('div');
            boatHeader.className = 'col-12 mb-2';
            boatHeader.innerHTML = `<strong>Boat: ${boat.name}</strong>`;
            roomWrapper.appendChild(boatHeader);

            boat.rooms.forEach(room => {
                const cap = parseInt(room.capacity || 0) + parseInt(room.extra_beds || 0);
                const div = document.createElement('div');
                div.className = 'col-md-4';
                div.innerHTML = `
                    <label class="card p-2 h-100">
                        <strong>${room.room_name}</strong><br>
                        <small class="text-muted">Max ${cap}</small>
                        <select multiple class="form-control room-guests mt-2"
                            name="guest_rooms[${room.id}][]"
                            data-cap="${cap}"
                            ${optionalRooms ? '' : ''}>
                        </select>
                        <div class="assigned-guests mt-1 text-muted"></div>
                        <div class="room-full text-danger mt-1" style="display:none;">Room is full!</div>
                    </label>
                `;
                roomWrapper.appendChild(div);

                const select = div.querySelector('select.room-guests');
                const fullMsg = div.querySelector('.room-full');

                // Prepare Choices data with pre-selected guests
                const choicesData = guests.map(g => ({
                    value: g.id,
                    label: g.name,
                    selected: bookingRoomGuests[room.id]?.includes(g.id) || false
                }));

                const choices = new Choices(select, {
                    removeItemButton: true,
                    searchEnabled: true,
                    shouldSort: false,
                    placeholder: true,
                    placeholderValue: 'Select guests',
                    choices: choicesData
                });
                choicesInstances.push(choices);

                // Render assigned guests badges and room-full logic
                function updateRoomState() {
                    const selectedIds = choices.getValue(true); // array of selected guest IDs
                    const assignedDiv = div.querySelector('.assigned-guests');
                    assignedDiv.innerHTML = '';

                    selectedIds.forEach(id => {
                        const guest = guests.find(g => g.id == id);
                        if (!guest) return;
                        const span = document.createElement('span');
                        span.className = 'badge bg-primary me-1 mb-1';
                        span.style.cursor = 'pointer';
                        span.textContent = guest.name + ' ×';
                        span.addEventListener('click', () => {
                            Swal.fire({
                                title: 'Remove Guest?',
                                text: `Are you sure you want to remove ${guest.name} from this room?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Yes, remove'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    choices.removeActiveItemsByValue(id);
                                    updateRoomState();
                                }
                            });
                        });

                        assignedDiv.appendChild(span);
                    });

                    if (selectedIds.length >= cap) {
                        select.closest('label').querySelector('.choices').style.display = 'none';
                        fullMsg.style.display = 'block';
                    } else {
                        select.closest('label').querySelector('.choices').style.display = 'block';
                        fullMsg.style.display = 'none';
                    }
                }

                // Initial render
                updateRoomState();

                // Update on changes
                select.addEventListener('change', updateRoomState);
                select.addEventListener('removeItem', updateRoomState);
            });
        }

        // Render old single boat
        if (slot.boat) addRooms(slot.boat);
        // Render multiple boats
        if (slot.boats && slot.boats.length) slot.boats.forEach(addRooms);

        roomMessage.style.display = roomsExist ? 'none' : 'block';
    }

    // -----------------------------
    // Initialize rooms on page load
    // -----------------------------
    if (booking.slot) {
        renderRoomsBySlot(booking.slot);
        inlineSlotWrapper.classList.add('d-none');
    } else {
        inlineSlotWrapper.classList.remove('d-none');
    }

    // -----------------------------
    // Guest Modal Add
    // -----------------------------
    $('#guestForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        $.ajax({
            url: '{{ route("admin.guests.store") }}',
            method: 'POST',
            data: form.serialize(),
            success: function (guest) {
                guests.push(guest);

                // Add new guest to all Choices selects dynamically
                choicesInstances.forEach(instance => {
                    instance.setChoices([{
                        value: guest.id,
                        label: guest.name,
                        selected: false,
                        disabled: false
                    }], 'value', 'label', false);
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
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });

    // -----------------------------
    // Price / USD calculation
    // -----------------------------
    function updateUSD() {
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
