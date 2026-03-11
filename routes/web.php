<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncomingLetterController;
use App\Http\Controllers\DispositionController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OutgoingLetterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Disposition;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    
    $incomingCount = IncomingLetter::visibleTo($user)->count();
    $outgoingCount = OutgoingLetter::visibleTo($user)->count();
    
    $pendingDispositions = 0;
    if ($user->division_id) {
        $pendingDispositions = Disposition::where('to_division_id', $user->division_id)
            ->where('status', 'pending')
            ->count();
    }

    return Inertia::render('Dashboard', [
        'stats' => [
            'incoming' => $incomingCount,
            'outgoing' => $outgoingCount,
            'pending_dispositions' => $pendingDispositions,
        ]
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    Route::resource('users', UserController::class);
    Route::resource('divisions', DivisionController::class)->except(['show']);
    Route::resource('incoming-letters', IncomingLetterController::class);
    Route::get('/incoming-letters/{letter}/print-disposition', [IncomingLetterController::class, 'printDisposition'])
        ->name('incoming-letters.print-disposition');
    Route::resource('outgoing-letters', OutgoingLetterController::class);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    
    Route::post('/incoming-letters/{letter}/dispositions', [DispositionController::class, 'store'])
        ->name('dispositions.store');
    
    Route::patch('/dispositions/{disposition}/status', [DispositionController::class, 'updateStatus'])
        ->name('dispositions.update-status');

    // Settings
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
