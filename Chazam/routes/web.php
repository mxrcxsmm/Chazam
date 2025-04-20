<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatLayoutController;
use App\Http\Controllers\EstadoController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
    Route::get('user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    // mostrar racha y puntos
    Route::get('chat', [ChatLayoutController::class, 'show'])->name('chat.show');
    
    // Ruta para actualizar estado
    Route::post('estado/actualizar', [EstadoController::class, 'actualizarEstado'])->name('estado.actualizar');
});

// Middleware para pasar variables de racha y puntos a todas las vistas
Route::middleware(['auth'])->group(function () {
    Route::get('retos/reto', function () {
        $user = Auth::user();
        return view('Retos.reto', [
            'racha' => $user->racha,
            'puntos' => $user->puntos,
            'username' => $user->username,
            'nombre_completo' => $user->nombre_completo,
            'imagen_perfil' => $user->imagen_perfil,
        ]);
    })->name('retos.reto');
    
    Route::get('retos/guide', function () {
        $user = Auth::user();
        return view('Retos.guide', [
            'racha' => $user->racha,
            'puntos' => $user->puntos,
            'username' => $user->username,
            'nombre_completo' => $user->nombre_completo,
            'imagen_perfil' => $user->imagen_perfil,
        ]);
    })->name('retos.guide');
});