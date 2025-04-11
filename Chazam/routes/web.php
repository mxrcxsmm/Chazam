<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

// Grupo de rutas para el administrador con middleware
// Route::middleware(['auth'])->group(function () {
    
// });

Route::get('admin', [AdminController::class, 'index'])->name('admin.usuarios.index');
Route::post('admin', [AdminController::class, 'store'])->name('admin.usuarios.store');
Route::put('admin/{id}', [AdminController::class, 'update'])->name('admin.usuarios.update');
Route::delete('admin/{id}', [AdminController::class, 'destroy'])->name('admin.usuarios.destroy');
Route::post('admin/usuarios/filtrar', [AdminController::class, 'filtrar'])->name('admin.usuarios.filtrar');
