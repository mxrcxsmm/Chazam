<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

// Grupo de rutas para el administrador con middleware
// Route::middleware(['auth'])->group(function () {
    
// });

Route::get('admin', [AdminController::class, 'index'])->name('admin.usuarios.index');
    Route::post('admin', [AdminController::class, 'store'])->name('admin.usuarios.store');
    Route::put('admin/{id}', [AdminController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('admin/{id}', [AdminController::class, 'destroy'])->name('admin.usuarios.destroy');

// Rutas para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/personalizacion', [UserController::class, 'edit'])->name('personalizacion');
        Route::put('/update', [UserController::class, 'update'])->name('update');

        // Rutas futuras
        Route::get('/perfil', function () {
            return view('user.perfil');
        })->name('perfil');

        Route::get('/mejoras', function () {
            return view('user.mejoras');
        })->name('mejoras');

        Route::get('/puntos', function () {
            return view('user.puntos');
        })->name('puntos');
    });
});