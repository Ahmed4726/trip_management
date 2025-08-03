<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolePermissionController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/about',[HomeController::class, 'about']);
Route::get('/trips',[HomeController::class, 'trips']);
Route::get('/blog',[HomeController::class, 'blog']);
Route::get('/contact',[HomeController::class, 'contact']);



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    //Permissions
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::patch('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::patch('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // Roles & Permissions
    Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles.permissions.index');
    Route::post('/roles-permissions/{role}', [RolePermissionController::class, 'update'])->name('roles.permissions.update');

     // Manage Users
    Route::get('/users', [AdminController::class, 'index'])->name('users.index');
    Route::get('/create-user', [AdminController::class, 'create'])->name('users.create');
    Route::post('/store-user', [AdminController::class, 'store'])->name('users.store');
    Route::post('users/{id}', [AdminController::class, 'update'])->name('users.update');
    Route::delete('users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');


    //Manage Agents
    Route::get('/agents', [AdminController::class, 'index_agent'])->name('agents.index');
    Route::get('/create-agent', [AdminController::class, 'create_agent'])->name('agents.create');
    Route::post('/store-agent', [AdminController::class, 'store_agent'])->name('agent.store');
    Route::post('agents/{id}', [AdminController::class, 'update_agent'])->name('agents.update');
    Route::delete('agents/{id}', [AdminController::class, 'destroy_agent'])->name('agents.destroy');

     //Manage Trips
    Route::get('/trips', [AdminController::class, 'trip_index'])->name('trips.index');
    Route::get('/create-trip', [AdminController::class, 'create_trip'])->name('trips.create');
    Route::post('/store-trip', [AdminController::class, 'store_trip'])->name('trips.store');
    Route::post('trips/{id}', [AdminController::class, 'update_trip'])->name('trips.update');
    Route::delete('trips/{id}', [AdminController::class, 'destroy_trip'])->name('trips.destroy');
    Route::get('/guest/form/{token}', [GuestController::class, 'show'])->name('guest.form');
    Route::post('/guest/form/{token}', [GuestController::class, 'submit'])->name('guest.form.submit');


     //Manage Finances
    Route::get('/finances', [AdminController::class, 'finance_index'])->name('finances.index');
    Route::get('/create-finance', [AdminController::class, 'create_finance'])->name('finances.create');
    Route::post('/store-finance', [AdminController::class, 'store_finance'])->name('finances.store');
    Route::post('finances/{id}', [AdminController::class, 'update_finance'])->name('finances.update');
    Route::delete('finances/{id}', [AdminController::class, 'destroy_finance'])->name('finances.destroy');

});





require __DIR__.'/auth.php';
