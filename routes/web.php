<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasksController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('tasks.index')
        : redirect()->route('login');
});
Route::get('/dashboard', fn() => redirect()->route('tasks.index'))
    ->middleware('auth')
    ->name('dashboard');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::post('/tasks/{task}/toggle', [TasksController::class, 'toggle'])->name('tasks.toggle');
    Route::post('/tasks/{task}/share',  [TasksController::class, 'share'])->name('tasks.share');
    Route::resource('tasks', TasksController::class);
});