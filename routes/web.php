<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Http\Controllers\{
    AdminController,
    AgentController,
    AuditController,
    BoatController,
    BookingController,
    CalendarController,
    CancellationPolicyController,
    CancellationPolicyRuleController,
    CompanyController,
    FinanceController,
    FleetCalendarController,
    GuestController,
    HomeController,
    PaymentPolicyController,
    ProfileController,
    PublicBookingController,
    RatePlanController,
    RatePlanRuleController,
    RoleController,
    PermissionController,
    RolePermissionController,
    RoomController,
    WaitingListController
};

use App\Http\Controllers\Admin\{
    BoatController as AdminBoatController,
    RoomController as AdminRoomController,
    RegionController,
    PortController,
    SlotController,
    BookingController as AdminBookingController,
    CurrencyController,
    TemplateController,
    SalespersonController
};

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome');
Route::get('/about', [HomeController::class, 'about']);
Route::get('/trips', [HomeController::class, 'trips']);
Route::get('/blog', [HomeController::class, 'blog']);
Route::get('/contact', [HomeController::class, 'contact']);
                Route::get('/dashboard', [CalendarController::class, 'fleet'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Public Booking / Widget
|--------------------------------------------------------------------------
*/
Route::prefix('public')->name('public.')->group(function () {
    Route::get('widget', [PublicBookingController::class, 'widget'])->name('widget');
    Route::get('availability/{id}', [PublicBookingController::class, 'availability'])->name('availability');
    Route::post('prebooking', [PublicBookingController::class, 'prebooking'])->name('prebook');
    Route::post('waitlist', [WaitingListController::class, 'store'])->name('waitlist');
});

/*
|--------------------------------------------------------------------------
| Guest Form (Token Based)
|--------------------------------------------------------------------------
*/
Route::get('guest/form/{token}', [GuestController::class, 'show'])->name('guest.form');
Route::post('guest/form/{token}', [GuestController::class, 'submit'])->name('guest.form.submit');
        Route::middleware('auth')->group(function () {

            /* Profile */
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

            /* Dashboard */
            Route::get('/dashboard', [CalendarController::class, 'fleet'])->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Admin Area
            |--------------------------------------------------------------------------
            */
            Route::prefix('admin')
                ->name('admin.')
                ->middleware('role:admin')
                ->group(function () {

                    Route::resources([
                        'regions'    => RegionController::class,
                        'ports'      => PortController::class,
                        'currencies' => CurrencyController::class,
                        'boats'      => AdminBoatController::class,
                        'rooms'      => AdminRoomController::class,
                        'slots'      => SlotController::class,
                        'templates'  => TemplateController::class,
                        'bookings'   => AdminBookingController::class,
                        'salespeople'=> SalespersonController::class,
                        'guests'     => GuestController::class,
                    ]);

                    Route::put('currencies/{currency}/rate', [CurrencyController::class, 'updateRate'])
                        ->name('currencies.updateRate');

                    /* Waiting List */
                    Route::get('waiting-lists', [WaitingListController::class, 'index'])->name('waitinglists.index');
                    Route::post('waiting-lists/{waitingList}/notify', [WaitingListController::class, 'notify'])->name('waitinglists.notify');
                    Route::get('waiting-lists/{waitingList}/convert', [WaitingListController::class, 'convertToBooking'])->name('waitinglists.convert');
                    Route::post('waiting-lists/{waitingList}/mark-converted', [WaitingListController::class, 'markConverted'])->name('waitinglists.markConverted');
                });

            /*
            |--------------------------------------------------------------------------
            | Roles & Permissions (Admin Only)
            |--------------------------------------------------------------------------
            */
            Route::middleware('role:admin')->group(function () {
                Route::resource('permissions', PermissionController::class)->except('show');
                Route::resource('roles', RoleController::class)->except('show');

                Route::get('roles-permissions', [RolePermissionController::class, 'index'])->name('roles.permissions.index');
                Route::post('roles-permissions/{role}', [RolePermissionController::class, 'update'])->name('roles.permissions.update');

                Route::get('/users', [AdminController::class, 'index'])->name('users.index');
                Route::get('/create-user', [AdminController::class, 'create'])->name('users.create');
                Route::post('/store-user', [AdminController::class, 'store'])->name('users.store');
                Route::post('users/{id}', [AdminController::class, 'update'])->name('users.update');
                Route::delete('users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');
            });

            /*
            |--------------------------------------------------------------------------
            | Agents (Admin / Sales / Companies)
            |--------------------------------------------------------------------------
            */
            Route::middleware('role:admin|sales|companies')->group(function () {
                Route::get('agents', [AgentController::class, 'index_agent'])->name('agents.index');
                Route::get('agents/create', [AgentController::class, 'create_agent'])->name('agents.create');
                Route::post('agents', [AgentController::class, 'store_agent'])->name('agents.store');
                Route::post('agents/{id}', [AgentController::class, 'update_agent'])->name('agents.update');
                Route::delete('agents/{id}', [AgentController::class, 'destroy_agent'])->name('agents.destroy');
                Route::get('agents/filter', [AgentController::class, 'filter_agent'])->name('agents.filter');
                Route::post('agents/{agent}/assign-trips', [AgentController::class, 'assignTrips'])->name('agents.assignTrips');
            });

            /*
            |--------------------------------------------------------------------------
            | Finance
            |--------------------------------------------------------------------------
            */
            Route::middleware('role:admin|companies')->group(function () {
                Route::get('finances', [FinanceController::class, 'finance_index'])->name('finances.index');
            });

            /*
            |--------------------------------------------------------------------------
            | Companies
            |--------------------------------------------------------------------------
            */
            Route::middleware('role:admin|super-admin')->group(function () {
                Route::resource('company', CompanyController::class);
            });

            /*
            |--------------------------------------------------------------------------
            | Policies & Rate Plans
            |--------------------------------------------------------------------------
            */
            Route::resources([
                'rate-plans'            => RatePlanController::class,
                'payment-policies'      => PaymentPolicyController::class,
                'cancellation-policies' => CancellationPolicyController::class,
            ]);

            Route::prefix('rate-plan-rules')->group(function () {
                Route::get('{rate_plan}', [RatePlanRuleController::class, 'index'])->name('rate-plan-rules.index');
                Route::post('{rate_plan}', [RatePlanRuleController::class, 'store'])->name('rate-plan-rules.store');
                Route::get('{rate_plan}/edit/{rule}', [RatePlanRuleController::class, 'edit'])->name('rate-plan-rules.edit');
                Route::put('{rate_plan}/{rule}', [RatePlanRuleController::class, 'update'])->name('rate-plan-rules.update');
                Route::delete('{rate_plan}/{rule}', [RatePlanRuleController::class, 'destroy'])->name('rate-plan-rules.destroy');
            });

            Route::prefix('cancellation-policy-rules')->group(function () {
                Route::get('{policy}', [CancellationPolicyRuleController::class, 'index'])->name('cancellation-policy-rules.index');
                Route::post('{policy}', [CancellationPolicyRuleController::class, 'store'])->name('cancellation-policy-rules.store');
                Route::put('{policy}/{rule}', [CancellationPolicyRuleController::class, 'update'])->name('cancellation-policy-rules.update');
                Route::delete('{policy}/{rule}', [CancellationPolicyRuleController::class, 'destroy'])->name('cancellation-policy-rules.destroy');
            });

            /*
            |--------------------------------------------------------------------------
            | Boats & Rooms
            |--------------------------------------------------------------------------
            */
            Route::resource('boats', BoatController::class);
            Route::resource('boats.rooms', RoomController::class);

            /*
            |--------------------------------------------------------------------------
            | Booking & Calendar APIs
            |--------------------------------------------------------------------------
            */
            Route::get('bookings/trips/events', [BookingController::class, 'getEvents'])->name('booking.events');
            Route::get('trips/{trip}/available-rooms', [BookingController::class, 'availableRoomsForTrip']);
            Route::get('boats/available-rooms', [BookingController::class, 'availableRoomsForBoat']);

            Route::prefix('api/calendar')->group(function () {
                Route::get('fleet/resources', [CalendarController::class, 'fleetResources']);
                Route::get('fleet/events', [CalendarController::class, 'fleetEvents']);
                Route::get('boat/{boat}/resources', [CalendarController::class, 'boatResources']);
                Route::get('boat/{boat}/events', [CalendarController::class, 'boatEvents']);
                Route::post('event/move', [CalendarController::class, 'moveEvent']);
            });

            Route::get('calendar/fleet/resources', [FleetCalendarController::class, 'resources']);
            Route::get('calendar/fleet/events', [FleetCalendarController::class, 'events']);

            /*
            |--------------------------------------------------------------------------
            | Audit Logs
            |--------------------------------------------------------------------------
            */
            Route::get('audits', [AuditController::class, 'index'])->name('audit.index');

        });

        /*
        |--------------------------------------------------------------------------
        | Tenant Routes
        |--------------------------------------------------------------------------
        */
        Route::domain('{slug}.' . env('DOMAIN_NAME'))
            ->middleware('tenantresolver')
            ->group(function () {

                /*
                |--------------------------------------------------------------------------
                | Authenticated Users
                |--------------------------------------------------------------------------
                */

            });

require __DIR__ . '/auth.php';
