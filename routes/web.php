<?php

use App\Http\Middleware\ForceUpdate;
use App\Livewire\RegistrationWizard;
use App\Livewire\StudentAccount;
use App\Livewire\StudentDashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = Auth::user();

    if (! $user) {
        return redirect()->route('login');
    }

    if ($user->isStudent()) {
        return redirect()->route('student.dashboard');
    }

    // Admin, secretary, teacher → Filament panel
    return redirect('/admin');
})->name('home');

// Public routes
Route::middleware('guest')->group(function () {
    Route::get('/register', RegistrationWizard::class)->name('register');
    Route::get('/login', fn () => redirect('/admin/login'))->name('login');
});

// Student-facing routes (authenticated)
Route::middleware(['auth', ForceUpdate::class])->group(function () {
    Route::get('/dashboard', StudentDashboard::class)->name('student.dashboard');
    Route::get('/account', StudentAccount::class)->name('student.account');
});

// Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->middleware('auth')->name('logout');
