<div class="d-flex justify-content-left mb-3">

</div>
@php
    $arrival = $arrival ?? null;
    $departure = $departure ?? null;
@endphp

<div class="p-3">

    <h4>
        {{ isset($bookingGuest->guest->first_name) ? $bookingGuest->guest->first_name . ' ' . $bookingGuest->guest->last_name : 'Guest' }}
        @if($bookingGuest->is_lead_guest)
            <span class="badge bg-primary">Lead Guest</span>
        @endif

        <a href="{{ route('admin.bookings.guests.pdf', [$booking->id, $bookingGuest->id]) }}"
            target="_blank"
            class="btn btn-danger btn-sm text-end">
                PDF Preview
            </a>
    </h4>

    <hr>

    <!-- NAV -->
    <ul class="nav nav-tabs">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile">Profile</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#travel">Travel</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#medical">Medical</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#food">Food</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#drink">Drinks</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#housekeeping">Housekeeping</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#service">Service</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#diving">Diving</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#surfing">Surfing</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#documents">Documents</button></li>
    </ul>

    <div class="tab-content mt-3">
        <!-- PROFILE -->
        <div class="tab-pane fade show active" id="profile">
            <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/profile') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control" placeholder="Enter first name" value="{{ isset($bookingGuest->guest->first_name) ? $bookingGuest->guest->first_name : '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control" placeholder="Enter last name" value="{{ isset($bookingGuest->guest->last_name) ? $bookingGuest->guest->last_name : '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email address" value="{{ isset($bookingGuest->guest->email) ? $bookingGuest->guest->email : '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Enter phone number" value="{{ isset($bookingGuest->guest->phone) ? $bookingGuest->guest->phone : '' }}">
                    </div>
                </div>
                <button class="btn btn-primary">Save Profile</button>
            </form>
        </div>

        <!-- TRAVEL -->
        <div class="tab-pane fade" id="travel">
            <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/travel') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Direction</label>
                        <select name="direction" class="form-control">
                            <option value="">Select direction</option>
                            <option value="arrival">Arrival</option>
                            <option value="departure">Departure</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Type</label>
                        <select name="travel_type" id="travel_type" class="form-control">
                            <option value="">Select travel type</option>
                            <option value="flight">Flight</option>
                            <option value="hotel">Hotel</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control" placeholder="Select date">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Time</label>
                        <input type="time" name="time" class="form-control" placeholder="Select time">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Pickup Required</label>
                        <select name="pickup_required" class="form-control">
                            <option value="">Select</option>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Dropoff Required</label>
                        <select name="dropoff_required" class="form-control">
                            <option value="">Select</option>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>

                <hr>

                {{-- ✈️ FLIGHT --}}
                <div id="flight_fields">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Airline</label>
                            <input type="text" name="airline" class="form-control" placeholder="e.g., Emirates, Qatar Airways">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Flight Number</label>
                            <input type="text" name="flight_number" class="form-control" placeholder="e.g., EK101">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Airport</label>
                            <input type="text" name="airport" class="form-control" placeholder="e.g., JFK, LAX, LHR">
                        </div>
                    </div>
                </div>

                {{-- 🏨 HOTEL --}}
                <div id="hotel_fields" style="display:none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Hotel Name</label>
                            <input type="text" name="location_name" class="form-control" placeholder="e.g., Hilton Hotel">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Hotel Address</label>
                            <textarea name="location_address" class="form-control" placeholder="e.g., 123 Main Street, City, Country"></textarea>
                        </div>
                    </div>
                </div>

                {{-- 📍 OTHER --}}
                <div id="other_fields" style="display:none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Location / Details</label>
                            <input type="text" name="location_name" class="form-control" placeholder="Enter location or details">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" placeholder="Add any travel notes or special requests"></textarea>
                    </div>
                </div>

                <button class="btn btn-primary">Save Travel</button>
            </form>

            <hr>

            {{-- RECORDS --}}
            <h5>Travel Records</h5>

            @forelse($bookingGuest->travelDetails ?? [] as $travel)
                <div class="card mb-2 p-2">
                    <strong>{{ ucfirst($travel->direction) }} - {{ ucfirst($travel->travel_type) }}</strong>
                    <div class="small text-muted">{{ $travel->date ? $travel->date->format('Y-m-d') : '' }} {{ $travel->time ? $travel->time->format('h:i A') : '' }}</div>

                    @if($travel->travel_type == 'flight')
                        <div>Airline: {{ $travel->airline }}</div>
                        <div>Flight: {{ $travel->flight_number }}</div>
                        <div>Airport: {{ $travel->airport }}</div>
                    @endif

                    @if($travel->travel_type == 'hotel')
                        <div>Hotel: {{ $travel->location_name }}</div>
                        <div>Address: {{ $travel->location_address }}</div>
                    @endif

                    @if($travel->travel_type == 'other')
                        <div>Details: {{ $travel->location_name }}</div>
                    @endif

                    <div>Pickup: {{ $travel->pickup_required ? 'Yes' : 'No' }} | Dropoff: {{ $travel->dropoff_required ? 'Yes' : 'No' }}</div>

                    @if($travel->notes)
                        <div class="text-muted">{{ $travel->notes }}</div>
                    @endif
                </div>
            @empty
                <div class="text-center text-muted py-3">No travel records found.</div>
            @endforelse
        </div>

        {{-- ================= MEDICAL ================= --}}
        <div class="tab-pane fade" id="medical">
            <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/medical') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Medical Conditions</label>
                        <textarea class="form-control" name="medical_conditions" placeholder="e.g., Diabetes, Asthma">{{ optional($bookingGuest->medical)->medical_conditions }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Medications</label>
                        <textarea class="form-control" name="medications" placeholder="e.g., Insulin, Aspirin">{{ optional($bookingGuest->medical)->medications }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Food Allergy</label>
                        <select class="form-control" name="food_allergy_flag">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->medical)->food_allergy_flag == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->medical)->food_allergy_flag == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Food Allergy Details</label>
                        <textarea class="form-control" name="food_allergy_details" placeholder="Describe food allergies in detail">{{ optional($bookingGuest->medical)->food_allergy_details }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Other Allergies</label>
                        <textarea class="form-control" name="other_allergies" placeholder="e.g., Pollen, Pet, Medication">{{ optional($bookingGuest->medical)->other_allergies }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Motion Sickness</label>
                        <select class="form-control" name="motion_sickness">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->medical)->motion_sickness == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->medical)->motion_sickness == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Physical Limitations</label>
                        <textarea class="form-control" name="physical_limitations" placeholder="e.g., Mobility issues, Back pain">{{ optional($bookingGuest->medical)->physical_limitations }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Mobility Notes</label>
                        <textarea class="form-control" name="mobility_notes" placeholder="e.g., Wheelchair access needed">{{ optional($bookingGuest->medical)->mobility_notes }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Special Assistance Required</label>
                        <select class="form-control" name="special_assistance_required">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->medical)->special_assistance_required == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->medical)->special_assistance_required == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Emergency Contact Name</label>
                        <input class="form-control" type="text" name="emergency_contact_name" placeholder="Full name" value="{{ optional($bookingGuest->medical)->emergency_contact_name }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Emergency Contact Relationship</label>
                        <input class="form-control" type="text" name="emergency_contact_relationship" placeholder="e.g., Spouse, Parent, Sibling" value="{{ optional($bookingGuest->medical)->emergency_contact_relationship }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Emergency Contact Phone</label>
                        <input class="form-control" type="text" name="emergency_contact_phone" placeholder="Phone number" value="{{ optional($bookingGuest->medical)->emergency_contact_phone }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Emergency Contact Email</label>
                        <input class="form-control" type="email" name="emergency_contact_email" placeholder="Email address" value="{{ optional($bookingGuest->medical)->emergency_contact_email }}">
                    </div>
                </div>
                <button class="btn btn-primary">Save Medical</button>
            </form>
        </div>

        {{-- ================= FOOD ================= --}}
        <div class="tab-pane fade" id="food">
            <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/food') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Dietary Type</label>
                        <input class="form-control" type="text" name="dietary_type" placeholder="e.g., Vegan, Vegetarian" value="{{ optional($bookingGuest->foodPreference)->dietary_type }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Allergy</label>
                        <select class="form-control" name="allergy_flag">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->foodPreference)->allergy_flag == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->foodPreference)->allergy_flag == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Allergy Details</label>
                        <textarea class="form-control" name="allergy_details" placeholder="Describe allergies in detail">{{ optional($bookingGuest->foodPreference)->allergy_details }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Dislikes</label>
                        <textarea class="form-control" name="dislikes" placeholder="Foods the guest dislikes">{{ optional($bookingGuest->foodPreference)->dislikes }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Favorite Foods</label>
                        <textarea class="form-control" name="favorite_foods" placeholder="Foods the guest enjoys">{{ optional($bookingGuest->foodPreference)->favorite_foods }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Breakfast Preference</label>
                        <input class="form-control" type="text" name="breakfast_preference" placeholder="e.g., Continental, Full English" value="{{ optional($bookingGuest->foodPreference)->breakfast_preference }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Lactose Intolerant</label>
                        <select class="form-control" name="lactose_intolerant">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->foodPreference)->lactose_intolerant == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->foodPreference)->lactose_intolerant == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Gluten Free</label>
                        <select class="form-control" name="gluten_free">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->foodPreference)->gluten_free == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->foodPreference)->gluten_free == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Halal</label>
                        <select class="form-control" name="halal">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->foodPreference)->halal == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->foodPreference)->halal == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Vegetarian</label>
                        <select class="form-control" name="vegetarian">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->foodPreference)->vegetarian == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->foodPreference)->vegetarian == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Vegan</label>
                        <select class="form-control" name="vegan">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->foodPreference)->vegan == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->foodPreference)->vegan == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Pescatarian</label>
                        <select class="form-control" name="pescatarian">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->foodPreference)->pescatarian == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->foodPreference)->pescatarian == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Kosher</label>
                        <select class="form-control" name="kosher">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->foodPreference)->kosher == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->foodPreference)->kosher == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Snack Preference</label>
                        <input class="form-control" type="text" name="snack_preference" placeholder="e.g., Fruit, Nuts, Cookies" value="{{ optional($bookingGuest->foodPreference)->snack_preference }}">
                    </div>
                </div>
                <button class="btn btn-success">Save Food</button>
            </form>
        </div>

        {{-- ================= DRINK ================= --}}
        <div class="tab-pane fade" id="drink">
            <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/drink') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Drinks Alcohol</label>
                        <select class="form-control" name="drinks_alcohol">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->drinkPreference)->drinks_alcohol == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->drinkPreference)->drinks_alcohol == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Wine Preference</label>
                        <input class="form-control" type="text" name="wine_preference" placeholder="e.g., Red, White, Rosé" value="{{ optional($bookingGuest->drinkPreference)->wine_preference }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Spirits Preference</label>
                        <input class="form-control" type="text" name="spirits_preference" placeholder="e.g., Vodka, Whiskey, Rum" value="{{ optional($bookingGuest->drinkPreference)->spirits_preference }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Cocktail Preference</label>
                        <input class="form-control" type="text" name="cocktail_preference" placeholder="e.g., Mojito, Margarita" value="{{ optional($bookingGuest->drinkPreference)->cocktail_preference }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Beer Preference</label>
                        <input class="form-control" type="text" name="beer_preference" placeholder="e.g., Lager, Stout, IPA" value="{{ optional($bookingGuest->drinkPreference)->beer_preference }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Coffee Preference</label>
                        <input class="form-control" type="text" name="coffee_preference" placeholder="e.g., Espresso, Cappuccino, Black" value="{{ optional($bookingGuest->drinkPreference)->coffee_preference }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Tea Preference</label>
                        <input class="form-control" type="text" name="tea_preference" placeholder="e.g., Black, Green, Herbal" value="{{ optional($bookingGuest->drinkPreference)->tea_preference }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Soft Drink Preference</label>
                        <input class="form-control" type="text" name="soft_drink_preference" placeholder="e.g., Cola, Juice, Lemonade" value="{{ optional($bookingGuest->drinkPreference)->soft_drink_preference }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Water Preference</label>
                        <input class="form-control" type="text" name="water_preference" placeholder="e.g., Still, Sparkling, Mineral" value="{{ optional($bookingGuest->drinkPreference)->water_preference }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Drink Notes</label>
                        <textarea class="form-control" name="drink_notes" placeholder="Add any additional drink preferences or notes">{{ optional($bookingGuest->drinkPreference)->drink_notes }}</textarea>
                    </div>
                </div>
                <button class="btn btn-primary">Save Drinks</button>
            </form>
        </div>

        {{-- ================= HOUSEKEEPING ================= --}}
        <div class="tab-pane fade" id="housekeeping">
            <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/housekeeping') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Room ID</label>
                        {{-- {{{ $bookingGuest->rooms->room_name }}} --}}
                        <input class="form-control" type="text" name="room_id" placeholder="e.g., 101, Suite A" value="{{ optional($bookingGuest->rooms)->room_name }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Bed Setup Preference</label>
                        <select class="form-control" name="bed_setup_preference">
                            <option value="">Select</option>
                            <option value="double" {{ optional($bookingGuest->housekeeping)->bed_setup_preference == 'double' ? 'selected' : '' }}>King bed</option>
                            <option value="twin" {{ optional($bookingGuest->housekeeping)->bed_setup_preference == 'twin' ? 'selected' : '' }}>Two twin beds</option>
                            <option value="other" {{ optional($bookingGuest->housekeeping)->bed_setup_preference == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Pillow Preference</label>
                        <input class="form-control" type="text" name="pillow_preference" placeholder="e.g., Soft, Firm, Extra pillows" value="{{ optional($bookingGuest->housekeeping)->pillow_preference }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Towel Change Preference</label>
                        <input class="form-control" type="text" name="towel_change_preference" placeholder="e.g., Daily, Every other day" value="{{ optional($bookingGuest->housekeeping)->towel_change_preference }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Bathroom Assignment Notes</label>
                        <textarea class="form-control" name="bathroom_assignment_notes" placeholder="e.g., Ensuite bathroom preferred">{{ optional($bookingGuest->housekeeping)->bathroom_assignment_notes }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Cleaning Notes</label>
                        <textarea class="form-control" name="cleaning_notes" placeholder="e.g., No scented cleaners, Extra cleaning needed">{{ optional($bookingGuest->housekeeping)->cleaning_notes }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Baby Cot Required</label>
                        <select class="form-control" name="baby_cot_required">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->housekeeping)->baby_cot_required == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->housekeeping)->baby_cot_required == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Umbrella Required</label>
                        <select class="form-control" name="umbrella_required">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->housekeeping)->umbrella_required == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->housekeeping)->umbrella_required == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Beach Setup Required</label>
                        <select class="form-control" name="beach_setup_required">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->housekeeping)->beach_setup_required == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->housekeeping)->beach_setup_required == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Room Comfort Notes</label>
                        <textarea class="form-control" name="room_comfort_notes" placeholder="e.g., Temperature preference, Noise concerns">{{ optional($bookingGuest->housekeeping)->room_comfort_notes }}</textarea>
                    </div>
                </div>
                <button class="btn btn-warning">Save Housekeeping</button>
            </form>
        </div>

        {{-- ================= SERVICE ================= --}}
        <div class="tab-pane fade" id="service">
            <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/service') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>VIP Level</label>
                        <input class="form-control" type="text" name="vip_level" placeholder="e.g., Gold, Silver, Platinum" value="{{ optional($bookingGuest->serviceNote)->vip_level }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Celebration Type</label>
                        <input class="form-control" type="text" name="celebration_type" placeholder="e.g., Honeymoon, Birthday, Anniversary" value="{{ optional($bookingGuest->serviceNote)->celebration_type }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Celebration Notes</label>
                        <textarea class="form-control" name="celebration_notes" placeholder="Special requests for the celebration">{{ optional($bookingGuest->serviceNote)->celebration_notes }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Beach Dining Requested</label>
                        <select class="form-control" name="beach_dining_requested">
                            <option value="">Select</option>
                            <option value="0" {{ optional($bookingGuest->serviceNote)->beach_dining_requested == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ optional($bookingGuest->serviceNote)->beach_dining_requested == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Excursion Requests</label>
                        <textarea class="form-control" name="excursion_requests" placeholder="e.g., Snorkeling, Island tours, Water sports">{{ optional($bookingGuest->serviceNote)->excursion_requests }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Activity Preferences</label>
                        <textarea class="form-control" name="activity_preferences" placeholder="e.g., Yoga, Spa, Cultural activities">{{ optional($bookingGuest->serviceNote)->activity_preferences }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Guest Handling Notes</label>
                        <textarea class="form-control" name="guest_handling_notes" placeholder="e.g., Prefers privacy, Extra attentive service">{{ optional($bookingGuest->serviceNote)->guest_handling_notes }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Internal Service Notes</label>
                        <textarea class="form-control" name="internal_service_notes" placeholder="Internal notes for staff (not visible to guest)">{{ optional($bookingGuest->serviceNote)->internal_service_notes }}</textarea>
                    </div>
                </div>
                <button class="btn btn-success">Save Service</button>
            </form>
        </div>

        {{-- ================= DIVING ================= --}}
        <div class="tab-pane fade" id="diving">
            <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/diving') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                <label>Is Diver</label>
                <select class="form-control" name="is_diver">
                    <option value="">Select</option>
                    <option value="0" {{ optional($bookingGuest->diving)->is_diver == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ optional($bookingGuest->diving)->is_diver == '1' ? 'selected' : '' }}>Yes</option>
                </select>
                </div>
                <div class="col-md-6 mb-3">
                <label>Certification Agency</label>
                <input class="form-control" type="text" name="certification_agency" placeholder="e.g., PADI, SSI, NAUI" value="{{ optional($bookingGuest->diving)->certification_agency }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>Certification Level</label>
                <input class="form-control" type="text" name="certification_level" placeholder="e.g., PADI Open Water, Advanced" value="{{ optional($bookingGuest->diving)->certification_level }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>Certification Number</label>
                <input class="form-control" type="text" name="certification_number" placeholder="Certification card number" value="{{ optional($bookingGuest->diving)->certification_number }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>Logged Dives</label>
                <input class="form-control" type="number" name="logged_dives" placeholder="Number of dives" value="{{ optional($bookingGuest->diving)->logged_dives }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>Last Dive Date</label>
                <input class="form-control" type="date" name="last_dive_date" value="{{ optional($bookingGuest->diving)->last_dive_date ? optional($bookingGuest->diving)->last_dive_date->format('Y-m-d') : '' }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>Dive Insurance</label>
                <select class="form-control" name="dive_insurance">
                    <option value="">Select</option>
                    <option value="0" {{ optional($bookingGuest->diving)->dive_insurance == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ optional($bookingGuest->diving)->dive_insurance == '1' ? 'selected' : '' }}>Yes</option>
                </select>
                </div>
                <div class="col-md-6 mb-3">
                <label>Insurance Provider</label>
                <input class="form-control" type="text" name="insurance_provider" placeholder="e.g., DAN, AQABA" value="{{ optional($bookingGuest->diving)->insurance_provider }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>Equipment Rental Required</label>
                <select class="form-control" name="equipment_rental_required">
                    <option value="">Select</option>
                    <option value="0" {{ optional($bookingGuest->diving)->equipment_rental_required == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ optional($bookingGuest->diving)->equipment_rental_required == '1' ? 'selected' : '' }}>Yes</option>
                </select>
                </div>
                <div class="col-md-6 mb-3">
                <label>Wetsuit Size</label>
                <input class="form-control" type="text" name="wetsuit_size" placeholder="e.g., Small, Medium, Large" value="{{ optional($bookingGuest->diving)->wetsuit_size }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>Fin Size</label>
                <input class="form-control" type="text" name="fin_size" placeholder="e.g., XS, S, M, L, XL" value="{{ optional($bookingGuest->diving)->fin_size }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>BCD Size</label>
                <input class="form-control" type="text" name="bcd_size" placeholder="e.g., S, M, L, XL" value="{{ optional($bookingGuest->diving)->bcd_size }}">
                </div>
                <div class="col-md-12 mb-3">
                <label>Diving Medical Notes</label>
                <textarea class="form-control" name="diving_medical_notes" placeholder="e.g., Any medical conditions relevant to diving">{{ optional($bookingGuest->diving)->diving_medical_notes }}</textarea>
                </div>
                <div class="col-md-12 mb-3">
                <label>Diving Notes</label>
                <textarea class="form-control" name="diving_notes" placeholder="e.g., Preferred dive sites, depth preference">{{ optional($bookingGuest->diving)->diving_notes }}</textarea>
                </div>
            </div>
            <button class="btn btn-primary">Save Diving</button>
            </form>
        </div>

        {{-- ================= SURFING (UPDATED) ================= --}}
        <div class="tab-pane fade" id="surfing">
            <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/surfing') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                <label>Is Surfer</label>
                <select class="form-control" name="is_surfer">
                    <option value="">Select</option>
                    <option value="0" {{ optional($bookingGuest->surfing)->is_surfer == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ optional($bookingGuest->surfing)->is_surfer == '1' ? 'selected' : '' }}>Yes</option>
                </select>
                </div>
                <div class="col-md-6 mb-3">
                <label>Surf Level</label>
                <select class="form-control" name="surf_level">
                    <option value="">Select</option>
                    <option value="beginner" {{ optional($bookingGuest->surfing)->surf_level == 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="intermediate" {{ optional($bookingGuest->surfing)->surf_level == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="advanced" {{ optional($bookingGuest->surfing)->surf_level == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    <option value="professional" {{ optional($bookingGuest->surfing)->surf_level == 'professional' ? 'selected' : '' }}>Professional</option>
                </select>
                </div>
                <div class="col-md-6 mb-3">
                <label>Bringing Own Board</label>
                <select class="form-control" name="bringing_own_board">
                    <option value="">Select</option>
                    <option value="0" {{ optional($bookingGuest->surfing)->bringing_own_board == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ optional($bookingGuest->surfing)->bringing_own_board == '1' ? 'selected' : '' }}>Yes</option>
                </select>
                </div>
                <div class="col-md-6 mb-3">
                <label>Board Count</label>
                <input class="form-control" type="number" name="board_count" placeholder="Number of boards" value="{{ optional($bookingGuest->surfing)->board_count }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>Board Type</label>
                <input class="form-control" type="text" name="board_type" placeholder="e.g., Shortboard, Longboard, Funboard" value="{{ optional($bookingGuest->surfing)->board_type }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>Board Length</label>
                <input class="form-control" type="text" name="board_length" placeholder="e.g., 5'8\", 6'0\"" value="{{ optional($bookingGuest->surfing)->board_length }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>Board Width</label>
                <input class="form-control" type="text" name="board_width" placeholder="e.g., 19\", 19.5\"" value="{{ optional($bookingGuest->surfing)->board_width }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>Board Volume</label>
                <input class="form-control" type="text" name="board_volume" placeholder="e.g., 28L, 30L" value="{{ optional($bookingGuest->surfing)->board_volume }}">
                </div>
                <div class="col-md-6 mb-3">
                <label>Rental Required</label>
                <select class="form-control" name="rental_required">
                    <option value="">Select</option>
                    <option value="0" {{ optional($bookingGuest->surfing)->rental_required == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ optional($bookingGuest->surfing)->rental_required == '1' ? 'selected' : '' }}>Yes</option>
                </select>
                </div>
                <div class="col-md-6 mb-3">
                <label>Coaching Required</label>
                <select class="form-control" name="coaching_required">
                    <option value="">Select</option>
                    <option value="0" {{ optional($bookingGuest->surfing)->coaching_required == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ optional($bookingGuest->surfing)->coaching_required == '1' ? 'selected' : '' }}>Yes</option>
                </select>
                </div>
                <div class="col-md-6 mb-3">
                <label>Photo/Video Interest</label>
                <select class="form-control" name="photo_video_interest">
                    <option value="">Select</option>
                    <option value="0" {{ optional($bookingGuest->surfing)->photo_video_interest == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ optional($bookingGuest->surfing)->photo_video_interest == '1' ? 'selected' : '' }}>Yes</option>
                </select>
                </div>
                <div class="col-md-12 mb-3">
                <label>Surfing Notes</label>
                <textarea class="form-control" name="surfing_notes" placeholder="e.g., Preferred break, conditions preference">{{ optional($bookingGuest->surfing)->surfing_notes }}</textarea>
                </div>
            </div>
            <button class="btn btn-primary">Save Surfing</button>
            </form>
        </div>

        {{-- ================= DOCUMENTS ================= --}}
        <div class="tab-pane fade" id="documents">
            <form method="POST" enctype="multipart/form-data" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/documents') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Document File</label>
                        <input type="file" class="form-control" name="file_path" placeholder="Select a document file" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Document Type</label>
                        <select class="form-control" name="document_type" required>
                            <option value="">Select Type</option>
                            <option value="passport">Passport</option>
                            <option value="insurance">Insurance</option>
                            <option value="diving_license">Diving License</option>
                            <option value="waiver">Waiver</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" placeholder="Add any notes about this document"></textarea>
                    </div>
                </div>
                <button class="btn btn-dark">Upload Document</button>
            </form>

            <hr>

            {{-- DOCUMENTS LIST GROUPED BY TYPE --}}
            @php
                $documentsByType = $bookingGuest->documents->groupBy('document_type');
            @endphp

            @foreach($documentsByType as $type => $docs)
                <h5 class="mt-3 text-primary text-uppercase">{{ $type }}</h5>
                <div class="list-group mb-3">
                    @foreach($docs as $doc)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $doc->original_filename }}</strong><br>
                                <small>{{ $doc->mime_type }} | {{ number_format($doc->file_size / 1024, 2) }} KB</small>
                                @if($doc->notes)
                                    <div class="text-muted small">{{ $doc->notes }}</div>
                                @endif
                            </div>
                            <div>
                                <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-sm btn-primary">View</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

    </div>

</div>

<script>
    document.getElementById('travel_type').addEventListener('change', function() {
        document.getElementById('flight_fields').style.display = this.value === 'flight' ? 'block' : 'none';
        document.getElementById('hotel_fields').style.display = this.value === 'hotel' ? 'block' : 'none';
        document.getElementById('other_fields').style.display = this.value === 'other' ? 'block' : 'none';
    });
</script>
