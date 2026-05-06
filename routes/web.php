<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');
    Route::livewire('/expenses', 'pages::expenses.index')->name('expenses.index');
    Route::livewire('/categories', 'pages::categories.index')->name('categories.index');
    Route::livewire('/insights', 'pages::insights')->name('insights');
    Route::livewire('/counter', 'counter')->name('counter');


    Route::view('/profile', 'profile')->name('profile.edit');

    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});



require __DIR__.'/auth.php';
