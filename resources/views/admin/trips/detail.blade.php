@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <h2 class="mb-4">Trip Details</h2>

        <ul class="nav nav-tabs" id="tripTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab">Trip Info</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="guests-tab" data-toggle="tab" href="#guests" role="tab">Guests</a>
            </li>
        </ul>

        <div class="tab-content border border-top-0 p-3" id="tripTabContent">
            <!-- Trip Info Tab -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Availability Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-6"><strong>Title:</strong> {{ $trip->title }}</div>
                            <div class="col-md-6"><strong>Region:</strong> {{ $trip->region }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6"><strong>Status:</strong> {{ $trip->status }}</div>
                            <div class="col-md-6"><strong>Type:</strong> {{ $trip->trip_type }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6"><strong>Start Date:</strong> {{ $trip->start_date }}</div>
                            <div class="col-md-6"><strong>End Date:</strong> {{ $trip->end_date }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6"><strong>Price:</strong> ${{ $trip->price }}</div>
                            <div class="col-md-6"><strong>Boat:</strong> {{ $trip->boat }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <!-- <strong>Guests Count:</strong> {{ is_iterable($trip->guestList) ? $trip->guestList->count() : 0 }} -->
                                <div class="col-md-6"><strong>No Of Guests:</strong> {{ $trip->guests }}</div>
                            </div>
                            <!-- <div class="col-md-6"><strong>Leading Guest ID:</strong> {{ $trip->leading_guest_id }}</div> -->
                        <div class="col-md-6"><strong>Notes:</strong> {{ $trip->notes }}</div>
                        </div>
                        <div class="row mb-2">
                            <!-- <div class="col-md-6">
                                <strong>Agent:</strong>
                                {{ $trip->agent ? $trip->agent->first_name . ' ' . $trip->agent->last_name : '-' }}
                            </div> -->
                            
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guests Tab -->
            <div class="tab-pane fade" id="guests" role="tabpanel">
                @if($trip->guestList->isEmpty())
                    <p>No guests added yet.</p>
                @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>Passport</th>
                            <th>Nationality</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trip->guestList as $guest)
                            <tr>
                                <td>{{ $guest->name }}</td>
                                <td>{{ $guest->email }}</td>
                                <td>{{ $guest->gender }}</td>
                                <td>{{ $guest->dob }}</td>
                                <td>{{ $guest->passport }}</td>
                                <td>{{ $guest->nationality }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#otherGuestsModal{{ $guest->id }}">
                                        View Other Guests
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                @foreach ($trip->guestList as $guest)
                <!-- Other Guests Modal -->
                <div class="modal fade" id="otherGuestsModal{{ $guest->id }}" tabindex="-1" role="dialog" aria-labelledby="otherGuestsModalLabel{{ $guest->id }}" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Guests of {{ $guest->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        @if($guest->otherGuests->isEmpty())
                            <p>No other guests found for this guest.</p>
                        @else
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Gender</th>
                                        <th>DOB</th>
                                        <th>Passport</th>
                                        <th>Nationality</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($guest->otherGuests as $other)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $other->name }}</td>
                                            <td>{{ $other->email }}</td>
                                            <td>{{ $other->gender }}</td>
                                            <td>{{ $other->dob }}</td>
                                            <td>{{ $other->passport }}</td>
                                            <td>{{ $other->nationality }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('trips.index') }}" class="btn btn-secondary">Back to Trips</a>
        </div>
    </div>
</div>
@endsection
