<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

// Define las rutas directamente
Route::get('usuarios', [UserController::class, 'index'])->name('usuarios.index');
Route::get('usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
Route::post('usuarios', [UserController::class, 'store'])->name('usuarios.store');
Route::get('usuarios/{id}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
Route::put('usuarios/{id}', [UserController::class, 'update'])->name('usuarios.update');
Route::delete('usuarios/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');

Route::prefix('retos')->name('retos.')->group(function () {
    Route::view('reto', 'Retos.reto')->name('reto');
    Route::view('guide', 'Retos.guide')->name('guide');
});
