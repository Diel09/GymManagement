<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipsController;
use App\Http\Controllers\WalkInsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpensesController;
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

Route::get('/dashboard', [ReportController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/components/buttons', function () {
    return Inertia::render('Components/Buttons');
})->middleware(['auth', 'verified'])->name('components.buttons');

//member routes
Route::get('/members', [MemberController::class, 'index'])->middleware('auth')->name('members.home');
Route::get('/add_members', [MemberController::class, 'addMembers'])->middleware('auth')->name('members.add');
Route::post('/save_member', [MemberController::class, 'saveMember'])->middleware('auth')->name('members.save');
Route::get('/members/{id}/edit', [MemberController::class, 'edit'])->middleware('auth')->name('members.edit');
Route::post('/update_member', [MemberController::class, 'update'])->middleware('auth')->name('members.update');
Route::post('/fetch_member_in', [MemberController::class, 'fetchMemberIn'])->middleware('auth')->name('members.fetchIn');
Route::get('/member_in', [MemberController::class, 'memberIn'])->middleware('auth')->name('members.in');
Route::post('/renew', [MemberController::class, 'renew'])->middleware('auth')->name('members.renew');

//membership routes
Route::get('/memberships', [MembershipsController::class, 'index'])->middleware('auth')->name('memberships.home');
Route::get('/add_memberships', [MembershipsController::class, 'addMembership'])->middleware('auth')->name('memberships.add');
Route::post('/save_membership', [MembershipsController::class, 'saveMembership'])->middleware('auth')->name('memberships.save');
Route::get('/memberships/{id}/edit', [MembershipsController::class, 'edit'])->middleware('auth')->name('memberships.edit');
Route::post('/update_membership', [MembershipsController::class, 'update'])->middleware('auth')->name('memberships.update');
Route::delete('/memberships/{id}/delete', [MembershipsController::class, 'delete'])->middleware('auth')->name('memberships.delete');

//in out routes
Route::post('/time', [MemberController::class, 'time'])->name('members.time');

//expenses route
Route::get('/expenses', [ExpensesController::class, 'index'])->middleware('auth')->name('expenses.home');
Route::get('/add_expense', [ExpensesController::class, 'addExpense'])->middleware('auth')->name('expenses.add');
Route::post('/save_expense', [ExpensesController::class, 'saveExpense'])->middleware('auth')->name('expenses.save');
Route::get('/expense/{id}/edit', [ExpensesController::class, 'editExpense'])->middleware('auth')->name('expenses.edit');
Route::post('/update_expense', [ExpensesController::class, 'updateExpense'])->middleware('auth')->name('expenses.update');

//reports routes
Route::get('/reports', [ReportController::class, 'index'])->middleware('auth')->name('reports.home');
Route::post('/reports/download', [ReportController::class, 'download'])->middleware('auth')->name('reports.download');

//walk in routes
Route::get('/walk_in', [WalkInsController::class, 'index'])->middleware('auth')->name('walkins.home');
Route::get('/list', [WalkInsController::class, 'list'])->middleware('auth')->name('walkins.list');
Route::post('/fetch-list', [WalkInsController::class, 'fetchList'])->middleware('auth')->name('walkins.fetch');
Route::post('/save_walkin', [WalkInsController::class, 'saveWalkin'])->middleware('auth')->name('walkins.save');
Route::get('/get_latest_walkin', [WalkInsController::class, 'getLatestWalkIn'])->name('walkins.autoFetch');

require __DIR__ . '/auth.php';
