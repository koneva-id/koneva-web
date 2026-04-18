<?php

use App\Http\Controllers\Admin\ClientManagementController;
use App\Http\Controllers\Admin\DeliverableManagementController;
use App\Http\Controllers\Admin\ProjectManagementController;
use App\Http\Controllers\Admin\RequestManagementController;
use App\Http\Controllers\AltchaChallengeController;
use App\Http\Controllers\Client\DeliverableController;
use App\Http\Controllers\Client\RequestController as ClientRequestController;
use App\Http\Controllers\Superadmin\AdminManagementController;
use App\Http\Controllers\Superadmin\AuditLogController;
use App\Http\Controllers\Superadmin\BillingController;
use App\Http\Controllers\Superadmin\ReportController;
use App\Http\Controllers\ProfileController;
use App\Models\AuditLog;
use App\Models\BillingRecord;
use App\Models\Client;
use App\Models\ClientRequest;
use App\Models\Deliverable;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/altcha/challenge', AltchaChallengeController::class)
    ->middleware('throttle:60,1')
    ->name('altcha.challenge');

Route::get('/dashboard', function () {
    return redirect()->route('portal');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/portal', function (Request $request) {
    $user = $request->user();

    return match ($user->role) {
        'superadmin' => redirect()->route('superadmin.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        default => redirect()->route('client.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('portal');

Route::middleware(['auth', 'verified', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $client = $request->user()->clientProfile;

        return view('client.dashboard', [
            'client' => $client,
            'requestCount' => $client ? ClientRequest::where('client_id', $client->id)->count() : 0,
            'deliverableCount' => $client ? Deliverable::where('client_id', $client->id)->count() : 0,
            'projectCount' => $client ? Project::where('client_id', $client->id)->count() : 0,
        ]);
    })->name('dashboard');

    Route::get('/requests', [ClientRequestController::class, 'index'])->name('requests.index');
    Route::post('/requests', [ClientRequestController::class, 'store'])->name('requests.store');

    Route::get('/deliverables', [DeliverableController::class, 'index'])->name('deliverables.index');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard', [
            'clientCount' => Client::count(),
            'projectCount' => Project::count(),
            'requestCount' => ClientRequest::count(),
            'pendingRequestCount' => ClientRequest::whereIn('status', ['submitted', 'in_review', 'in_progress'])->count(),
            'deliverableCount' => Deliverable::count(),
        ]);
    })->name('dashboard');

    Route::get('/clients', [ClientManagementController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientManagementController::class, 'store'])->name('clients.store');

    Route::get('/projects', [ProjectManagementController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectManagementController::class, 'store'])->name('projects.store');

    Route::get('/requests', [RequestManagementController::class, 'index'])->name('requests.index');
    Route::get('/requests/{clientRequest}', [RequestManagementController::class, 'show'])->name('requests.show');
    Route::patch('/requests/{clientRequest}', [RequestManagementController::class, 'update'])->name('requests.update');

    Route::get('/deliverables', [DeliverableManagementController::class, 'index'])->name('deliverables.index');
    Route::post('/deliverables', [DeliverableManagementController::class, 'store'])->name('deliverables.store');
    Route::patch('/deliverables/{deliverable}', [DeliverableManagementController::class, 'update'])->name('deliverables.update');
    Route::post('/deliverables/{deliverable}/replace-file', [DeliverableManagementController::class, 'replaceFile'])->name('deliverables.replace-file');
    Route::delete('/deliverables/{deliverable}', [DeliverableManagementController::class, 'destroy'])->name('deliverables.destroy');
});

Route::middleware(['auth', 'verified', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('superadmin.dashboard', [
            'adminCount' => User::whereIn('role', ['admin', 'superadmin'])->count(),
            'inactiveCount' => User::where('is_active', false)->count(),
            'auditCount' => AuditLog::count(),
            'invoiceCount' => BillingRecord::count(),
        ]);
    })->name('dashboard');

    Route::get('/admins', [AdminManagementController::class, 'index'])->name('admins.index');
    Route::post('/admins', [AdminManagementController::class, 'store'])->name('admins.store');
    Route::patch('/admins/{user}/role', [AdminManagementController::class, 'updateRole'])->name('admins.update-role');
    Route::patch('/admins/{user}/status', [AdminManagementController::class, 'toggleStatus'])->name('admins.toggle-status');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
    Route::patch('/billing/{billing}/status', [BillingController::class, 'updateStatus'])->name('billing.update-status');
    Route::get('/billing/{billing}/pdf', [BillingController::class, 'downloadPdf'])->name('billing.pdf');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
