<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BoatController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CancellationPolicyController;
use App\Http\Controllers\CancellationPolicyRuleController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PaymentPolicyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatePlanRuleController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\WaitingListController;
use App\Http\Controllers\RatePlanController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TripController;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FleetCalendarController;

use App\Http\Controllers\Admin\{
    BoatController as AdminBoatController,
    RoomController as AdminRoomController,
    RegionController,
    PortController,
    SlotController,
    BookingController as AdminBookingController,
    TemplateController,
    SalespersonController
};

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::resource('regions', RegionController::class);
    Route::resource('currencies', CurrencyController::class);
    Route::resource('ports', PortController::class);

    Route::resource('boats', AdminBoatController::class);
    Route::resource('rooms', AdminRoomController::class);

    Route::resource('templates', TemplateController::class);

    Route::resource('slots', SlotController::class);
    Route::resource('bookings', AdminBookingController::class);
    Route::post('guests', [GuestController::class, 'store'])->name('guests.store');

    Route::resource('salespeople', SalespersonController::class)->middleware('auth');

});

    Route::post('/guests', [GuestController::class, 'store'])->name('guests.store');

Route::domain('{slug}.' . env('DOMAIN_NAME'))->middleware(['tenantresolver'])->group(function () {
// Public routes
Route::get('/guest/form/{token}', [GuestController::class, 'show'])->name('guest.form');
Route::post('/guest/form/{token}', [GuestController::class, 'submit'])->name('guest.form.submit');



// Dashboard (any authenticated + verified user)
// Route::get('/', [AdminController::class, 'dashboard'])
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Authenticated users
Route::middleware('auth')->group(function () {

    // User profile (all authenticated roles can access)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    /*
    |--------------------------------------------------------------------------
    | Admin + Super Admin Routes
    |--------------------------------------------------------------------------
    | Both admin and super-admin can access these
    */
    // ================== Agents ==================
    Route::middleware(['role:admin|sales|companies'])->group(function () {
        Route::get('/agents', [AgentController::class, 'index_agent'])->name('agents.index');
        Route::get('/create-agent', [AgentController::class, 'create_agent'])->name('agents.create');
        Route::post('/store-agent', [AgentController::class, 'store_agent'])->name('agent.store');
        Route::post('agents/{id}', [AgentController::class, 'update_agent'])->name('agents.update');
        Route::delete('agents/{id}', [AgentController::class, 'destroy_agent'])->name('agents.destroy');
        Route::get('/agents/filter', [AgentController::class, 'filter_agent'])->name('agents.filter');
        Route::post('/agents/{agent}/assign-trips', [AgentController::class, 'assignTrips'])->name('agents.assignTrips');

        // Manage Users
        Route::get('/users', [AdminController::class, 'index'])->name('users.index');
        Route::get('/create-user', [AdminController::class, 'create'])->name('users.create');
        Route::post('/store-user', [AdminController::class, 'store'])->name('users.store');
        Route::post('users/{id}', [AdminController::class, 'update'])->name('users.update');
        Route::delete('users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');
    });

    // ================== Trips ==================
    Route::middleware(['role:admin|sales|companies'])->group(function () {
        Route::get('/trips', [TripController::class, 'trip_index'])->name('trips.index');
        Route::get('/create-trip', [TripController::class, 'create_trip'])->name('trips.create');
        Route::post('/store-trip', [TripController::class, 'store_trip'])->name('trips.store');
        Route::post('trips/{id}', [TripController::class, 'update_trip'])->name('trips.update');
        Route::get('/trips/{id}', [TripController::class, 'show'])->name('trips.show');
        Route::delete('trips/{id}', [TripController::class, 'destroy_trip'])->name('trips.destroy');
        Route::get('/admin/trips/filter', [TripController::class, 'filter'])->name('trips.filter');
        Route::get('/admin/trips/events', [TripController::class, 'events'])->name('trips.events');
        Route::get('/trips/{trip}/rooms', [TripController::class, 'getRooms'])->name('trips.rooms');
    });

    // ================== Finances ==================
    Route::middleware(['role:admin|companies'])->group(function () {
        Route::get('/finances', [FinanceController::class, 'finance_index'])->name('finances.index');
        Route::get('/create-finance', [FinanceController::class, 'create_finance'])->name('finances.create');
        Route::post('/store-finance', [FinanceController::class, 'store_finance'])->name('finances.store');
        Route::post('finances/{id}', [FinanceController::class, 'update_finance'])->name('finances.update');
        Route::delete('finances/{id}', [FinanceController::class, 'destroy_finance'])->name('finances.destroy');
    });

    // ================== Guests ==================
    Route::middleware(['role:admin|sales|companies'])->group(function () {
        Route::get('/guests', [GuestController::class, 'guest_index'])->name('guest.index');
        Route::post('/guest-store', [GuestController::class, 'store'])->name('guest.store');
        Route::get('/guest/{id}', [GuestController::class, 'show_guest'])->name('guest.show');
        Route::get('/guest/{id}/pdf', [GuestController::class, 'download_pdf'])->name('guest.download.pdf');
    });

    // ================== Bookings ==================
    Route::middleware(['role:admin|sales|companies'])->group(function () {
        Route::get('/bookings', [BookingController::class, 'booking_index'])->name('bookings.index');
        Route::get('/create-booking', [BookingController::class, 'create_booking'])->name('bookings.create');
        Route::post('/store-booking', [BookingController::class, 'store_booking'])->name('bookings.store');
        Route::put('booking/{id}', [BookingController::class, 'update_booking'])->name('bookings.update');
        Route::get('/booking/{id}', [BookingController::class, 'show_booking'])->name('bookings.show');
        Route::get('/booking/edit/{id}', [BookingController::class, 'edit_booking'])->name('bookings.edit');
        Route::delete('booking/{id}', [BookingController::class, 'destroy_booking'])->name('bookings.destroy');
    });


    Route::resource('rate-plans', RatePlanController::class);
    Route::prefix('rate-plan-rules')->group(function () {
        Route::get('/{rate_plan}', [RatePlanRuleController::class, 'index'])->name('rate-plan-rules.index');
        Route::get('/create/{id}', [RatePlanRuleController::class, 'create'])->name('rate-plan-rules.create');
        Route::post('/{id}', [RatePlanRuleController::class, 'store'])->name('rate-plan-rules.store');
        Route::get('/{id}/edit', [RatePlanRuleController::class, 'edit'])->name('rate-plan-rules.edit');
        Route::put('/{id}', [RatePlanRuleController::class, 'update'])->name('rate-plan-rules.update');
        Route::delete('/{id}', [RatePlanRuleController::class, 'destroy'])->name('rate-plan-rules.destroy');
    });
    Route::resource('payment-policies', PaymentPolicyController::class);
    Route::resource('cancellation-policies', CancellationPolicyController::class);
    Route::prefix('cancellation-policy-rules')->group(function () {
        Route::get('/{id}', [CancellationPolicyRuleController::class, 'index'])->name('cancellation-policy-rules.index');
        Route::get('/create/{id}', [CancellationPolicyRuleController::class, 'create'])->name('cancellation-policy-rules.create');
        Route::post('/{id}', [CancellationPolicyRuleController::class, 'store'])->name('cancellation-policy-rules.store');
        Route::get('/{id}/edit', [CancellationPolicyRuleController::class, 'edit'])->name('cancellation-policy-rules.edit');
        Route::put('/{id}', [CancellationPolicyRuleController::class, 'update'])->name('cancellation-policy-rules.update');
        Route::delete('/{id}', [CancellationPolicyRuleController::class, 'destroy'])->name('cancellation-policy-rules.destroy');
    });
});
});


// Get available rooms for a specific trip
Route::get('/trips/{trip}/available-rooms', [BookingController::class, 'availableRoomsForTrip']);

// Get available rooms for a boat in a given date range (inline trip creation)
Route::get('/boats/available-rooms', [BookingController::class, 'availableRoomsForBoat']);


// routes/api.php (or web.php if you want web sessions)
Route::prefix('public')->group(function() {
    Route::get('/widget', [PublicBookingController::class, 'widget'])->name('public.widget');
    Route::get('/availability/{id}', [PublicBookingController::class, 'availability'])->name('public.availability');
    Route::post('/prebooking', [PublicBookingController::class, 'prebooking'])->name('public.prebook');
});

Route::get('/boats/rooms', [BookingController::class, 'getRoomsByBoat']);






// Test PDF
Route::get('/test-pdf', function () {
    $pdf = Pdf::loadHTML('<h1>Hello World</h1><p>This is working!</p>');
    return $pdf->download('test.pdf');
});

Route::get('/test', function () {
    return view('calendar_test');
});


Route::get('/download-pdf', function () {
    $users = User::get();

    $data = [
        'title' => 'Welcome to ItSolutionStuff.com',
        'date' => date('m/d/Y'),
        'users' => $users
    ];

    $pdf = PDF::loadView('pdf', $data);
    return $pdf->download('itsolutionstuff.pdf');
});

// Public routes
Route::get('/guest/form/{token}', [GuestController::class, 'show'])->name('guest.form');
Route::post('/guest/form/{token}', [GuestController::class, 'submit'])->name('guest.form.submit');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about',[HomeController::class, 'about']);
Route::get('/trips',[HomeController::class, 'trips']);
Route::get('/blog',[HomeController::class, 'blog']);
Route::get('/contact',[HomeController::class, 'contact']);

// Dashboard (any authenticated + verified user)
Route::get('/dashboard', [AdminController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Authenticated users
Route::middleware('auth')->group(function () {

    // User profile (all authenticated roles can access)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Super Admin Routes
    |--------------------------------------------------------------------------
    | Only super-admin can manage roles, permissions, and users
    */
    Route::middleware('role:admin')->group(function () {
        // Permissions
        Route::resource('permissions', PermissionController::class)->except(['show']);

        // Roles
        Route::resource('roles', RoleController::class)->except(['show']);

        // Roles & Permissions
        Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles.permissions.index');
        Route::post('/roles-permissions/{role}', [RolePermissionController::class, 'update'])->name('roles.permissions.update');

        // Manage Users
        Route::get('/users', [AdminController::class, 'index'])->name('users.index');
        Route::get('/create-user', [AdminController::class, 'create'])->name('users.create');
        Route::post('/store-user', [AdminController::class, 'store'])->name('users.store');
        Route::post('users/{id}', [AdminController::class, 'update'])->name('users.update');
        Route::delete('users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');
    });


    Route::prefix('admin')->middleware(['auth','role:admin'])->group(function() {
    Route::get('waiting-lists', [WaitingListController::class, 'index'])->name('admin.waitinglists.index');
    Route::post('waiting-lists/{waitingList}/notify', [WaitingListController::class, 'notify'])->name('admin.waitinglists.notify');
    Route::get('waiting-lists/{waitingList}/convert', [WaitingListController::class, 'convertToBooking'])->name('admin.waitinglists.convert');
    Route::post('waiting-lists/{waitingList}/mark-converted', [WaitingListController::class, 'markConverted'])->name('admin.waitinglists.markConverted');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin + Super Admin Routes
    |--------------------------------------------------------------------------
    | Both admin and super-admin can access these
    */
    // ================== Agents ==================
    Route::middleware(['role:admin|sales'])->group(function () {
        Route::get('/agents', [AgentController::class, 'index_agent'])->name('agents.index');
        Route::get('/create-agent', [AgentController::class, 'create_agent'])->name('agents.create');
        Route::post('/store-agent', [AgentController::class, 'store_agent'])->name('agent.store');
        Route::post('agents/{id}', [AgentController::class, 'update_agent'])->name('agents.update');
        Route::delete('agents/{id}', [AgentController::class, 'destroy_agent'])->name('agents.destroy');
        Route::get('/agents/filter', [AgentController::class, 'filter_agent'])->name('agents.filter');
        Route::post('/agents/{agent}/assign-trips', [AgentController::class, 'assignTrips'])->name('agents.assignTrips');
    });

    // ================== Trips ==================
    Route::middleware(['role:admin|sales'])->group(function () {
        Route::get('/trips', [TripController::class, 'trip_index'])->name('trips.index');
        Route::get('/create-trip', [TripController::class, 'create_trip'])->name('trips.create');
        Route::post('/store-trip', [TripController::class, 'store_trip'])->name('trips.store');
        Route::post('trips/{id}', [TripController::class, 'update_trip'])->name('trips.update');
        Route::get('/trips/{id}', [TripController::class, 'show'])->name('trips.show');
        Route::delete('trips/{id}', [TripController::class, 'destroy_trip'])->name('trips.destroy');
        Route::get('/admin/trips/filter', [TripController::class, 'filter'])->name('trips.filter');
        Route::get('/admin/trips/events', [TripController::class, 'events'])->name('trips.events');
        Route::get('/trips/{trip}/rooms', [TripController::class, 'getRooms'])->name('trips.rooms');
    });

    // ================== Finances ==================
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/finances', [FinanceController::class, 'finance_index'])->name('finances.index');
        Route::get('/create-finance', [FinanceController::class, 'create_finance'])->name('finances.create');
        Route::post('/store-finance', [FinanceController::class, 'store_finance'])->name('finances.store');
        Route::post('finances/{id}', [FinanceController::class, 'update_finance'])->name('finances.update');
        Route::delete('finances/{id}', [FinanceController::class, 'destroy_finance'])->name('finances.destroy');
    });

    // ================== Guests ==================
    Route::middleware(['role:admin|sales'])->group(function () {
        Route::get('/guests', [GuestController::class, 'guest_index'])->name('guest.index');
        Route::post('/guest-store', [GuestController::class, 'store'])->name('guest.store');
        Route::get('/guest/{id}', [GuestController::class, 'show_guest'])->name('guest.show');
        Route::get('/guest/{id}/pdf', [GuestController::class, 'download_pdf'])->name('guest.download.pdf');
    });

    // ================== Bookings ==================
    Route::middleware(['role:admin|sales'])->group(function () {
        Route::get('/bookings', [BookingController::class, 'booking_index'])->name('bookings.index');
        Route::get('/create-booking', [BookingController::class, 'create_booking'])->name('bookings.create');
        Route::post('/store-booking', [BookingController::class, 'store_booking'])->name('bookings.store');
        Route::put('booking/{id}', [BookingController::class, 'update_booking'])->name('bookings.update');
        Route::get('/booking/{id}', [BookingController::class, 'show_booking'])->name('bookings.show');
        Route::get('/booking/edit/{id}', [BookingController::class, 'edit_booking'])->name('bookings.edit');
        Route::delete('booking/{id}', [BookingController::class, 'destroy_booking'])->name('bookings.destroy');
        Route::get('/trip/{trip}/rooms', [BookingController::class, 'getTripRooms']);


    });

    // ================== Companies ==================
    Route::middleware(['role:admin|super-admin'])->group(function () {
        Route::get('/companies', [CompanyController::class, 'company_index'])->name('company.index');
        Route::get('/create-company', [CompanyController::class, 'create_company'])->name('company.create');
        Route::post('/store-company', [CompanyController::class, 'store_company'])->name('company.store');
        Route::put('company/{id}', [CompanyController::class, 'update_company'])->name('company.update');
        Route::get('/company/{id}', [CompanyController::class, 'show_company'])->name('company.show');
        Route::delete('company/{id}', [CompanyController::class, 'destroy_company'])->name('company.destroy');
    });


     // ================== Rate Plans ==================
    Route::resource('rate-plans', RatePlanController::class);
    Route::prefix('rate-plan-rules')->group(function () {
        Route::get('/{rate_plan}', [RatePlanRuleController::class, 'index'])->name('rate-plan-rules.index');
        Route::get('/create/{id}', [RatePlanRuleController::class, 'create'])->name('rate-plan-rules.create');
        Route::post('/{id}', [RatePlanRuleController::class, 'store'])->name('rate-plan-rules.store');
        Route::get('/{ratePlanId}/rules/{rule}/edit', [RatePlanRuleController::class, 'edit'])->name('rate-plan-rules.edit');
        Route::put('/{ratePlanId}/rules/{rule}', [RatePlanRuleController::class, 'update'])->name('rate-plan-rules.update');
        Route::delete('/{ratePlanId}/rules/{rule}', [RatePlanRuleController::class, 'destroy'])->name('rate-plan-rules.destroy');
    });

     // ================== Payment policies ==================
    Route::resource('payment-policies', PaymentPolicyController::class)->parameters(['payment-policies' => 'policy']);
     // ================== Cancellation policies ==================
    Route::resource('cancellation-policies', CancellationPolicyController::class);
    Route::prefix('cancellation-policy-rules')->group(function () {
        Route::get('/{id}', [CancellationPolicyRuleController::class, 'index'])->name('cancellation-policy-rules.index');
        Route::get('/create/{id}', [CancellationPolicyRuleController::class, 'create'])->name('cancellation-policy-rules.create');
        Route::post('/{id}', [CancellationPolicyRuleController::class, 'store'])->name('cancellation-policy-rules.store');
        Route::get('/{id}/edit', [CancellationPolicyRuleController::class, 'edit'])->name('cancellation-policy-rules.edit');
        Route::put('/{id}', [CancellationPolicyRuleController::class, 'update'])->name('cancellation-policy-rules.update');
        Route::delete('/{id}', [CancellationPolicyRuleController::class, 'destroy'])->name('cancellation-policy-rules.destroy');
    });

      // ================== // WaitingList ==================
    Route::post('/public/waitlist', [App\Http\Controllers\WaitingListController::class, 'store'])
        ->name('public.waitlist');

    // ================== Boats ==================
    Route::get('/boats', [BoatController::class, 'boat_index'])->name('boat.index');
    Route::get('/boats/create', [BoatController::class, 'create_boat'])->name('boat.create');
    Route::post('/boats', [BoatController::class, 'store_boat'])->name('boat.store');
    Route::get('/boats/{boat}', [BoatController::class, 'show_boat'])->name('boat.show');
    Route::put('/boats/{boat}', [BoatController::class, 'update_boat'])->name('boat.update');
    Route::delete('/boats/{boat}', [BoatController::class, 'destroy_boat'])->name('boat.destroy');

    // ================== Rooms (NESTED) ==================
       Route::prefix('boats/{boat}')->group(function () {
        Route::get('/rooms', [RoomController::class, 'room_index'])->name('room.index');
        Route::get('/rooms/create', [RoomController::class, 'create_room'])->name('room.create');
        Route::post('/rooms', [RoomController::class, 'store_room'])->name('room.store');
        Route::get('/rooms/{room}/edit', [RoomController::class, 'edit_room'])->name('room.edit');
        Route::put('/rooms/{room}', [RoomController::class, 'update_room'])->name('room.update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy_room'])->name('room.destroy');
    });

    Route::get('/boat/rooms/{id}', [BoatController::class, 'room_index']);

    Route::get('/bookings/trips/events', [BookingController::class, 'getEvents'])->name('booking.events');

     // ================== Audit & Logging ==================
    Route::get('/audits', [AuditController::class, 'index'])->name('audit.index');


    /* Dashboard */
    Route::get('/dashboard', [CalendarController::class, 'fleet'])->name('dashboard');

    /* Boat detail */
    Route::get('/boats/{boat}', [BoatController::class, 'show'])->name('boats.show');

    /* Calendar APIs */
    Route::prefix('api/calendar')->group(function () {
        Route::get('/fleet/resources', [CalendarController::class, 'fleetResources']);
        Route::get('/fleet/events', [CalendarController::class, 'fleetEvents']);

        Route::get('/boat/{boat}/resources', [CalendarController::class, 'boatResources']);
        Route::get('/boat/{boat}/events', [CalendarController::class, 'boatEvents']);

        Route::post('/event/move', [CalendarController::class, 'moveEvent']);
    });

    /* Iframe (public / readonly) */
    Route::get('/embed/fleet', [CalendarController::class, 'fleetIframe']);
    Route::get('/embed/boat/{boat}', [CalendarController::class, 'boatIframe']);


    Route::get('/calendar/fleet/resources', [FleetCalendarController::class, 'resources']);
Route::get('/calendar/fleet/events', [FleetCalendarController::class, 'events']);
Route::post('/calendar/event/move', [FleetCalendarController::class, 'move']);


});





require __DIR__.'/auth.php';
