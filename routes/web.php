<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

Route::middleware('auth')->group(function () {
    Route::get('/password/change', function () {
        return view('auth.force-password-change');
    })->name('password.change.notice');

    Route::post('/password/change', function (Request $request) {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
            'needs_password_change' => false,
        ]);

        return redirect()->intended('/dashboard')->with('status', 'password-changed');
    })->name('password.change.update');
});

use App\Http\Controllers\UserController;

Route::middleware(['auth', 'can:manage-users'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset_password');
});
