<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipsController;
use App\Http\Controllers\WalkInsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/components/buttons', function () {
    return Inertia::render('Components/Buttons');
})->middleware(['auth', 'verified'])->name('components.buttons');

//member routes
Route::get('/members', [MemberController::class, 'index'])->middleware('auth')->name('members.home');
Route::get('/add_members', [MemberController::class, 'addMembers'])->middleware('auth')->name('members.add');
Route::post('/save_member', [MemberController::class, 'saveMember'])->middleware('auth')->name('members.save');
Route::get('/members/{id}/edit', [MemberController::class, 'edit'])->middleware('auth')->name('members.edit');
Route::post('/update_member', [MemberController::class, 'update'])->middleware('auth')->name('members.update');

//membership routes
Route::get('/memberships', [MembershipsController::class, 'index'])->middleware('auth')->name('memberships.home');
Route::get('/add_memberships', [MembershipsController::class, 'addMembership'])->middleware('auth')->name('memberships.add');
Route::post('/save_membership', [MembershipsController::class, 'saveMembership'])->middleware('auth')->name('memberships.save');
Route::get('/memberships/{id}/edit', [MembershipsController::class, 'edit'])->middleware('auth')->name('memberships.edit');
Route::post('/update_membership', [MembershipsController::class, 'update'])->middleware('auth')->name('memberships.update');
Route::delete('/memberships/{id}/delete', [MembershipsController::class, 'delete'])->middleware('auth')->name('memberships.delete');

//in out routes
Route::post('/time', [MemberController::class, 'time'])->name('members.time');

//walk in routes
Route::get('/walk_in', [WalkInsController::class, 'index'])->middleware('auth')->name('walkins.home');
Route::post('/save_walkin', [WalkInsController::class, 'saveWalkin'])->middleware('auth')->name('walkins.save');
require __DIR__ . '/auth.php';
