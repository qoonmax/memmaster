<?php

use App\Http\Controllers\API\AIHelperController;
use App\Http\Controllers\API\CardController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\Page\CardController as PageCardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('pages.cards.repetition');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register')
    ]);
})->name('welcome');

Route::prefix('cards')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [PageCardController::class, 'index'])->name('pages.cards.index');
    Route::get('/repetition', [PageCardController::class, 'repetition'])->name('pages.cards.repetition');
    Route::get('/{slug}', [PageCardController::class, 'show'])->name('pages.cards.show');;
});

//TODO: Перенести в API
Route::prefix('api')->middleware(['auth', 'verified'])->group(function () {
    // Роуты для работы с карточками
    Route::prefix('cards')->group(function () {
        Route::get('/', [CardController::class, 'index'])->name('api.cards.index');
        Route::get('/{slug}', [CardController::class, 'show'])->name('api.cards.show');
        Route::post('/{slug}', [CardController::class, 'update'])->name('api.cards.update');
        Route::post('/{slug}/repeat-immediately/', [CardController::class, 'repeatImmediately'])->name('api.cards.repeat-immediately');
        Route::post('/{slug}/repeat/', [CardController::class, 'repeat'])->name('api.cards.repeat');
        Route::post('/{slug}/skip/', [CardController::class, 'skip'])->name('api.cards.skip');
        Route::delete('/{slug}', [CardController::class, 'delete'])->name('api.cards.delete');
    });

    // Роуты для работы с тегами
    Route::prefix('tags')->group(function () {
        Route::get('/', [TagController::class, 'index'])->name('api.tags.index');
    });

    Route::prefix('ai-helper')->middleware(['auth', 'verified'])->group(function () {
        Route::post('tags/generate', [AIHelperController::class, 'generateTags'])->name('api.ai-helper.tags.generate');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
