<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;

// Grupo de rutas para el administrador con middleware
// Route::middleware(['auth'])->group(function () {
    
// });

Route::get('/', function () {
    return view('login');
})->name('login');

// Rutas de autenticación
Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Grupo de rutas para el administrador con middleware
Route::middleware(['auth'])->group(function () {
    Route::get('admin', [AdminController::class, 'index'])->name('admin.usuarios.index');
    Route::post('admin', [AdminController::class, 'store'])->name('admin.usuarios.store');
    Route::put('admin/{id}', [AdminController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('admin/{id}', [AdminController::class, 'destroy'])->name('admin.usuarios.destroy');
});

// Grupo de rutas para usuarios normales
Route::middleware(['auth'])->group(function () {
    Route::get('perfil/dashboard', [PerfilController::class, 'dashboard'])->name('perfil.dashboard');
});

Route::prefix('retos')->name('retos.')->group(function () {
    Route::view('reto', 'Retos.reto')->name('reto');
    Route::view('guide', 'Retos.guide')->name('guide'); // Asegúrate de que el nombre sea 'guide'
});

// Rutas para usuarios autenticados
// Route::middleware(['auth'])->group(function () {
    Route::prefix('perfil')->name('perfil.')->group(function () {
        Route::get('/personalizacion', [PerfilController::class, 'edit'])->name('personalizacion');
        Route::put('/update', [PerfilController::class, 'update'])->name('update');
    
        // Rutas futuras
        Route::get('/vista', function () {
            return view('perfil.vista');
        })->name('vista');
    
        Route::get('/mejoras', function () {
            return view('perfil.mejoras');
        })->name('mejoras');
    
        Route::get('/puntos', function () {
            return view('perfil.puntos');
        })->name('puntos');
    });     
// });