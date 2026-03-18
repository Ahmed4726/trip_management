@extends('layouts.admin')
@section('content')
<style>
.guest-pool{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
}
.guest-card{
    padding:6px 10px;
    background:#0d6efd;
    color:white;
    border-radius:6px;
    cursor:grab;
    font-size:13px;
}
.room-dropzone{
    min-height:90px;
    border:2px dashed #d9d9d9;
    padding:10px;
    border-radius:6px;
    transition:.2s;
}
.room-dropzone.dragover{
    background:#eef6ff;
    border-color:#0d6efd;
}
.room-capacity-bar{
    height:6px;
    background:#eee;
    border-radius:4px;
    margin-top:6px;
}
.room-capacity-fill{
    height:6px;
    background:#0d6efd;
    border-radius:4px;
}
</style>

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

            {{-- Slot & Customer --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Customer (Lead Guest)</label>
                    <select id="customerSelect" name="lead_customer_id" class="form-control">
                        <option value="">Search customer...</option>
                        @foreach($guests as $g)
                            <option value="{{ $g->id }}" {{ $booking->guest_name==$g->first_name.' '.$g->last_name?'selected':'' }}>
                                {{ $g->first_name }} {{ $g->last_name }}
                                {{ $g->email ? '• '.$g->email : '' }}
                                {{ $g->phone ? '• '.$g->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

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
                        <input type="text" class="form-control" value="{{ $slotName }}" readonly>
                        <input type="hidden" name="slot_id" value="{{ $booking->slot_id }}">
                </div>

                {{-- Source / Agent --}}
                <div class="col-md-6 mb-3">
                    <label>Source</label>
                    <select name="source" id="sourceSelect" class="form-control">
                        <option value="Direct" {{ $booking->source=='Direct'?'selected':'' }}>Direct</option>
                        <option value="Agent" {{ $booking->source=='Agent'?'selected':'' }}>Agent</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3" id="agentWrapper">
                    <label>Agent</label>
                    <select name="agent_id" id="agentSelect" class="form-control" {{ $booking->source=='Agent'?'':'disabled' }}>
                        <option value="">-- Select Agent --</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ $booking->agent_id==$agent->id?'selected':'' }}>
                                {{ $agent->first_name }} {{ $agent->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Salesperson --}}
                <div class="col-md-6 mb-3">
                    <label>Salesperson</label>
                    <select name="salesperson_id" class="form-control" required>
                        <option value="">-- Select Salesperson --</option>
                        @foreach($salespersons as $sp)
                            <option value="{{ $sp->id }}" {{ $booking->salesperson_id==$sp->id?'selected':'' }}>
                                {{ $sp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Booking Status --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Status</label>
                    <select name="booking_status" class="form-control" required>
                        @foreach(['Pending','DP Paid','Full Paid','Waiting List','Canceled'] as $status)
                            <option value="{{ $status }}" {{ $booking->status==$status?'selected':'' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Inline Slot --}}
            <div id="inlineSlotWrapper" class="border p-3 mb-3">
                <h5>Inline Slot Creation</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Slot Type</label>
                        <select name="slot_type" class="form-control">
                            @foreach(['Open Trip','Private Charter','Maintenance','Docking','Crossing'] as $type)
                                <option value="{{ $type }}" {{ $booking->slot_type==$type?'selected':'' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Status</label>
                        <select name="slot_status" class="form-control">
                            @foreach(['Available','On-Hold','Blocked'] as $status)
                                <option value="{{ $status }}" {{ $booking->slot_status==$status?'selected':'' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Vessels + Region --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Vessels</label>
                        <select name="boats_allowed[]" id="inlineBoatsAllowed" class="form-control" multiple>
                            @foreach($boats as $boat)
                                <option value="{{ $boat->id }}" {{ in_array($boat->id, $bookingBoats ?? [])?'selected':'' }}>
                                    {{ $boat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Region</label>
                        <select name="region_id" class="form-control">
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ $booking->region_id==$region->id?'selected':'' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Ports --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Departure Port</label>
                        <select name="departure_port_id" class="form-control">
                            @foreach($ports as $port)
                                <option value="{{ $port->id }}" {{ $booking->departure_port_id==$port->id?'selected':'' }}>{{ $port->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Arrival Port</label>
                        <select name="arrival_port_id" class="form-control">
                            @foreach($ports as $port)
                                <option value="{{ $port->id }}" {{ $booking->arrival_port_id==$port->id?'selected':'' }}>{{ $port->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Dates --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Start Date</label>
                        <input type="date" name="start_date" id="inlineStartDate" class="form-control" value="{{ $booking->start_date }}">
                    </div>
                    <div class="col-md-3">
                        <label>Duration (Nights)</label>
                        <input type="number" name="duration_nights" id="inlineDurationNights" class="form-control" min="0" value="{{ $booking->duration_nights }}">
                    </div>
                    <div class="col-md-3">
                        <label>End Date</label>
                        <input type="date" name="end_date" id="inlineEndDate" class="form-control" readonly value="{{ $booking->end_date }}">
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mb-3">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control">{{ $booking->notes }}</textarea>
                </div>
            </div>

            {{-- Price / Deposit --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Price</label>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" value="{{ $booking->price }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Currency</label>
                    <select id="currency" name="currency" class="form-control">
                        @foreach($currencies as $curr)
                            <option value="{{ $curr->id }}" data-rate="{{ $curr->rate }}" {{ $booking->currency_id==$curr->id?'selected':'' }}>
                                {{ $curr->symbol }} - {{ $curr->name }} Rate: {{ $curr->rate }} USD
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Price in USD</label>
                    <input type="number" name="price_usd" id="price_usd" class="form-control" readonly value="{{ $booking->price_usd }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 mb-3">
                    <label>Deposit Amount</label>
                    <input type="number" name="deposit_amount" class="form-control" step="0.01" value="{{ $booking->deposit_amount }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Deposit Due Date</label>
                    <input type="date" name="deposit_due_date" class="form-control" value="{{ $booking->deposit_due_date }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Final Balance Due Date</label>
                    <input type="date" name="final_balance_due_date" class="form-control" value="{{ $booking->final_balance_due_date }}">
                </div>
            </div>

            {{-- Trip Guests --}}
            <div class="card p-3 mb-3">
                <h5>Trip Guests</h5>
                <select id="guestSelector" class="form-control">
                    <option value="">Search guest...</option>
                </select>
                <div id="tripGuestPool" class="guest-pool mt-3"></div>
                <div id="tripGuestsInputs"></div>
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
                            <option value="{{ $plan->id }}" {{ $booking->rate_plan_id==$plan->id?'selected':'' }}>{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Payment Policy</label>
                    <select name="payment_policy_id" class="form-control" required>
                        @foreach($paymentPolicies as $policy)
                            <option value="{{ $policy->id }}" {{ $booking->payment_policy_id==$policy->id?'selected':'' }}>{{ $policy->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Cancellation Policy</label>
                    <select name="cancellation_policy_id" class="form-control" required>
                        @foreach($cancellationPolicies as $policy)
                            <option value="{{ $policy->id }}" {{ $booking->cancellation_policy_id==$policy->id?'selected':'' }}>{{ $policy->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Notes --}}
            <div class="mb-3">
                <label>Notes</label>
                <textarea name="notes" class="form-control">{{ $booking->notes }}</textarea>
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
document.addEventListener('DOMContentLoaded', function() {


    // -------------------------
    // GLOBAL VARIABLES
    // -------------------------
    let tripGuests = @json($bookingGuests); // preloaded guests already on this booking
    let roomAssignments = {}; // track guests assigned to rooms in this session
    let bookingRoomGuests = @json($bookingRoomGuests ?? []);

    const slots = @json($slots);
    const guests = @json($guests);
    const roomUsageBySlot = @json($roomUsageBySlot);
    const boatsWithRooms = @json($boats); // include rooms relation

    // -------------------------
    // ELEMENTS
    // -------------------------
    const slotSelect = document.getElementById('slotSelect');
    const roomWrapper = document.getElementById('roomWrapper');
    const roomMessage = document.getElementById('roomMessage');
    const inlineSlotWrapper = document.getElementById('inlineSlotWrapper');
    const inlineStart = document.getElementById('inlineStartDate');
    const inlineDuration = document.getElementById('inlineDurationNights');
    const inlineEnd = document.getElementById('inlineEndDate');
    const vesselsSelect = document.getElementById('inlineBoatsAllowed');
    const guestSelector = document.getElementById('guestSelector');
    const agentSelect = document.getElementById('agentSelect');
    const sourceSelect = document.getElementById('sourceSelect');

    // -------------------------
    // GUEST SELECTOR
    // -------------------------
    new Choices('#customerSelect', {
        searchEnabled: true,
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Search customer...'
    });

    const guestChoices = new Choices(guestSelector,{
        searchEnabled:true,
        removeItemButton:false,
        shouldSort:false,
        placeholder:true,
        placeholderValue:'Search guest'
    });

    // Populate guest selector
    guestChoices.setChoices(
        guests.map(g=>({
            value:g.id,
            label:`${g.first_name} ${g.last_name}`
        })),
        'value','label',true
    );

    // Add "Add New Guest" option
    guestChoices.setChoices([{
        value:'add_new_guest',
        label:'➕ Add New Guest'
    }],'value','label',false);

    guestSelector.addEventListener('change', function() {
        const id = this.value;
        if(id === 'add_new_guest'){
            new bootstrap.Modal(document.getElementById('guestModal')).show();
            return;
        }
        const guest = guests.find(g => g.id == id);
        if(!guest) return;
        if(tripGuests.find(g => g.id == id)){
            Swal.fire('Guest already added');
            return;
        }
        tripGuests.push(guest);
        renderGuestPool();
    });

        // -------------------------
        // TRIP GUEST POOL
        // -------------------------
        function renderGuestPool() {
            const pool = document.getElementById('tripGuestPool');
            pool.innerHTML='';
            tripGuests.forEach(g=>{
                const div=document.createElement('div');
                div.className='guest-card';
                div.draggable=true;
                div.dataset.id=g.id;
                div.innerText=g.first_name+" "+g.last_name;
                div.addEventListener('dragstart', e=>{
                    e.dataTransfer.setData('guestId', g.id);
                });
                pool.appendChild(div);
            });
        }

        Object.values(bookingRoomGuests).flat().forEach(id=>{
        tripGuests = tripGuests.filter(g => g.id != id);
    });

    renderGuestPool(); // preload existing trip guests

        // Auto render rooms for current booking slot
    const bookingSlotId = "{{ $booking->slot_id }}";
    if(bookingSlotId){
        const slot = slots.find(s => s.id == bookingSlotId);
        console.log(slot);
        if(slot){
            renderRoomsBySlot(slot);
            toggleInlineFields(false);
        }
    }

    // -------------------------
    // INLINE SLOT TOGGLE
    // -------------------------
    function toggleInlineFields(active){
        const fields = inlineSlotWrapper.querySelectorAll('input, select, textarea');
        fields.forEach(f=>{
            if(active){
                f.setAttribute('required','required');
                f.disabled=false;
            }else{
                f.removeAttribute('required');
                f.disabled=true;
            }
        });
        inlineSlotWrapper.style.display = active ? 'block' : 'none';
    }

    toggleInlineFields(!slotSelect.value);



    // -------------------------
    // INLINE DATE CALCULATION
    // -------------------------
    const today = new Date().toISOString().split('T')[0];
    inlineStart.setAttribute('min', today);

    function calculateInlineEndDate(){
        if(!inlineStart.value || !inlineDuration.value) return;
        const startDate = new Date(inlineStart.value);
        const nights = parseInt(inlineDuration.value);
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + nights);
        inlineEnd.valueAsDate = endDate;
    }

    inlineStart.addEventListener('change', calculateInlineEndDate);
    inlineDuration.addEventListener('input', calculateInlineEndDate);
    calculateInlineEndDate();



    // -------------------------
    // SLOT SELECT CHANGE
    // -------------------------
    slotSelect.addEventListener('change', function(){
        const slotId = this.value;
        const slot = slots.find(s=>s.id==slotId);

        if(slot){
            toggleInlineFields(false);
            renderRoomsBySlot(slot);
        }else{
            toggleInlineFields(true);
            roomWrapper.innerHTML='';
            roomMessage.textContent='Please select a slot to see rooms.';
            roomMessage.style.display='block';
        }
    });

    // -------------------------
    // RENDER ROOMS FOR EDIT MODE
    // -------------------------
    function renderRoomsBySlot(slot){
        roomWrapper.innerHTML='';
        roomMessage.style.display='block';

        const isPrivate = slot.slot_type === 'Private Charter';
        roomMessage.textContent = isPrivate
            ? 'Private Charter: room assignment is optional.'
            : 'Open Trip: assign any available rooms.';

        let guestAssignments={};

        function addRooms(boat){
            if(!boat.rooms || !boat.rooms.length) return;
            const boatHeader=document.createElement('div');
            boatHeader.className='col-12 mb-2';
            boatHeader.innerHTML=`<strong>Boat: ${boat.name}</strong>`;
            roomWrapper.appendChild(boatHeader);

            boat.rooms.forEach(room=>{
                const cap = parseInt(room.capacity||0)+parseInt(room.extra_beds||0);
                const usage = roomUsageBySlot?.[slot.id]?.[room.id] || {};
                const previousGuests = (usage.guests||[]).map(id=>parseInt(id));

                let assigned = bookingRoomGuests?.[room.id] || [];
                guestAssignments[room.id] = [...previousGuests, ...assigned];

                const div = document.createElement('div');
                div.className='col-md-4 mb-3';
                div.innerHTML = `
                    <label class="card p-2 h-100">
                        <strong>${room.room_name}</strong>
                        <small class="text-muted room-capacity mt-1"></small>
                        <div class="room-dropzone mt-2"
                            data-room="${room.id}"
                            data-cap="${cap}"
                            style="min-height:90px;border:2px dashed #ccc;border-radius:6px;padding:8px;">
                        </div>
                        <input type="hidden" class="room-input" name="guest_rooms[${room.id}]">
                    </label>
                `;
                roomWrapper.appendChild(div);

                const dropzone = div.querySelector('.room-dropzone');
                const hiddenInput = div.querySelector('.room-input');
                const capacityText = div.querySelector('.room-capacity');
                const fullMsg = div.querySelector('.room-full');

                function renderGuests(){
                    dropzone.innerHTML='';
                    guestAssignments[room.id].forEach(id=>{
                        const guest = guests.find(g=>g.id==id);
                        if(!guest) return;

                        const isExisting = previousGuests.includes(id);

                        const badge = document.createElement('span');
                        badge.className='badge me-1 mb-1';
                        badge.style.background = isExisting ? '#6c757d' : '#0d6efd';
                        badge.style.cursor = isExisting ? 'not-allowed' : 'pointer';
                        badge.textContent = (guest.first_name+' '+guest.last_name).trim() + (isExisting ? '' : ' ×');

                        if(!isExisting){
                            badge.onclick=()=>{
                                Swal.fire({
                                    title:'Remove guest?',
                                    icon:'warning',
                                    showCancelButton:true
                                }).then(res=>{
                                    if(!res.isConfirmed) return;
                                    guestAssignments[room.id] = guestAssignments[room.id].filter(gid=>gid!==id);
                                    tripGuests.push(guest);
                                    renderGuestPool();
                                    updateAllRooms();
                                });
                            };
                        }

                        dropzone.appendChild(badge);
                    });

                    // Only include newly added guests in hidden input
                    hiddenInput.value = guestAssignments[room.id].filter(id=>!previousGuests.includes(id)).join(',');

                    const used = guestAssignments[room.id].length;
                    const remaining = cap-used;
                    capacityText.textContent = remaining>0 ? `Remaining ${remaining} of ${cap}` : 'Fully booked';
                    fullMsg.style.display = remaining<=0 ? 'block':'none';
                }

                dropzone.addEventListener('dragover', e=>{ e.preventDefault(); });
                dropzone.addEventListener('drop', e=>{
                    e.preventDefault();
                    const guestId=parseInt(e.dataTransfer.getData('guestId'));
                    if(!guestId) return;
                    if(guestAssignments[room.id].includes(guestId)) return;

                    if(previousGuests.length){
                        Swal.fire({icon:'warning', title:'Room already booked', text:'Guests from previous booking occupy this room.'});
                        return;
                    }

                    if(!isPrivate && guestAssignments[room.id].length >= cap){
                        Swal.fire({icon:'warning', title:'Room Full'});
                        return;
                    }

                    Object.keys(guestAssignments).forEach(rid=>{
                        if(!roomUsageBySlot?.[slot.id]?.[rid]) {
                            guestAssignments[rid] = guestAssignments[rid].filter(id=>id!==guestId);
                        }
                    });

                    guestAssignments[room.id].push(guestId);
                    tripGuests = tripGuests.filter(g=>g.id!==guestId);
                    renderGuestPool();
                    updateAllRooms();
                });

                div.updateRoomState = renderGuests;
                renderGuests();
            });
        }

        function updateAllRooms(){
            document.querySelectorAll('#roomWrapper .col-md-4').forEach(room=>{
                if(room.updateRoomState) room.updateRoomState();
            });
        }

        if(slot.boat) addRooms(slot.boat);
        if(slot.boats) slot.boats.forEach(addRooms);
        updateAllRooms();
    }

    // -------------------------
    // AGENT/SOURCE TOGGLE
    // -------------------------
    function toggleAgent(){
        agentSelect.disabled = sourceSelect.value !== 'Agent';
        if(agentSelect.disabled) agentSelect.value='';
    }
    toggleAgent();
    sourceSelect.addEventListener('change', toggleAgent);



    // -------------------------
    // PRICE USD CALCULATION
    // -------------------------
    const priceInput = document.getElementById('price');
    const currencySelect = document.getElementById('currency');
    const priceUsdInput = document.getElementById('price_usd');
    function updateUSD(){
        const rate = parseFloat(currencySelect.selectedOptions[0].dataset.rate);
        const price = parseFloat(priceInput.value)||0;
        priceUsdInput.value = (price*rate).toFixed(2);
    }
    priceInput.addEventListener('input', updateUSD);
    currencySelect.addEventListener('change', updateUSD);
    updateUSD();

});
</script>



@endsection
