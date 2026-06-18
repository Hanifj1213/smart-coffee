<?php

use App\Http\Controllers\CrmAnalyticController;
use App\Http\Middleware\EnsureTeamMembership;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\MenuManagement as AdminMenuManagement;
use App\Livewire\Admin\RewardManagement as AdminRewardManagement;
use App\Livewire\Admin\UserManagement as AdminUserManagement;
use App\Livewire\Kasir\Cashier as KasirCashier;
use App\Livewire\Kasir\MemberLookup as KasirMemberLookup;
use App\Livewire\Kasir\TransactionHistory as KasirTransactionHistory;
use App\Livewire\Member\Dashboard as MemberDashboard;
use App\Livewire\Member\OrderMenu as MemberOrderMenu;
use App\Livewire\Member\RewardCatalog as MemberRewardCatalog;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();
    if ($user !== null) {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'kasir') {
            return redirect()->route('kasir.cashier');
        }

        return redirect()->route('member.dashboard');
    }

    return view('welcome');
})->name('home');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/menus', AdminMenuManagement::class)->name('admin.menus');
    Route::get('/users', AdminUserManagement::class)->name('admin.users');
    Route::get('/rewards', AdminRewardManagement::class)->name('admin.rewards');

    // Report exports
    Route::get('/reports/members', [CrmAnalyticController::class, 'exportMembers'])->name('admin.reports.members');
    Route::get('/reports/transactions', [CrmAnalyticController::class, 'exportTransactions'])->name('admin.reports.transactions');
    Route::get('/reports/sales-pdf', [CrmAnalyticController::class, 'exportSalesPdf'])->name('admin.reports.sales-pdf');

    // API endpoints for ajax call
    Route::get('/api/cluster-data', [CrmAnalyticController::class, 'getClusterData'])->name('admin.api.cluster-data');
    Route::post('/api/run-churn', [CrmAnalyticController::class, 'runChurnPrevention'])->name('admin.api.run-churn');
});

// Kasir Routes
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->group(function () {
    Route::get('/cashier', KasirCashier::class)->name('kasir.cashier');
    Route::get('/transactions', KasirTransactionHistory::class)->name('kasir.transactions');
    Route::get('/member-lookup', KasirMemberLookup::class)->name('kasir.member-lookup');
});

// Member Routes
Route::middleware(['auth', 'role:member'])->prefix('member')->group(function () {
    Route::get('/dashboard', MemberDashboard::class)->name('member.dashboard');
    Route::get('/order', MemberOrderMenu::class)->name('member.order');
    Route::get('/rewards', MemberRewardCatalog::class)->name('member.rewards');
});

// Redirect /dashboard to role-based dashboard
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user === null) {
        return redirect()->route('login');
    }

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'kasir') {
        return redirect()->route('kasir.cashier');
    }

    return redirect()->route('member.dashboard');
})->name('dashboard');

// Default Starter Kit team-based routing (kept for compatibility)
Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');
});

require __DIR__.'/settings.php';
