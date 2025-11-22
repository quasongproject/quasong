<?php

use App\Http\Controllers\MusicController;
use App\Http\Controllers\MusicLikeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/musics',              [MusicController::class, 'index'])->name('music.index');
    Route::get('/musics/create',       [MusicController::class, 'create'])->name('music.create');
    Route::post('/musics',             [MusicController::class, 'store'])->name('music.store');
    Route::get('/musics/{music}',      [MusicController::class, 'show'])->name('music.show');
    Route::get('/musics/{music}/edit', [MusicController::class, 'edit'])->name('music.edit');
    Route::put('/musics/{music}',      [MusicController::class, 'update'])->name('music.update');
    Route::delete('/music/{music}',   [MusicController::class, 'destroy'])->name('music.destroy');
});

Route::get('/player', [MusicController::class, 'player'])
    ->name('player')
    ->middleware(['auth']);

// Like/Unlike routes - accessible by authenticated users
Route::middleware(['auth'])->group(function () {
    Route::post('/music/{music}/like', [MusicLikeController::class, 'like'])
        ->name('music.like');
    Route::delete('/music/{music}/unlike', [MusicLikeController::class, 'unlike'])
        ->name('music.unlike');
    Route::post('/music/{music}/toggle-like', [MusicLikeController::class, 'toggle'])
        ->name('music.toggle-like');
});

Route::get('/', function () {
    return Inertia::render('Landing', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', function () {
    return redirect()->route('player');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';