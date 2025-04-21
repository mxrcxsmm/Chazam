<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatLayoutController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\RetoController;
use App\Http\Controllers\FriendChatController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\RetoAdminController;
use App\Http\Controllers\ReporteAdminController;

// Grupo de rutas para el administrador con middleware
// Route::middleware(['auth'])->group(function () {
    
// });

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

// Rutas de autenticación
Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('register', [AuthController::class, 'store'])->name('auth.register');
Route::post('check-availability', [AuthController::class, 'checkAvailability'])->name('auth.check-availability');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Grupo de rutas para el administrador con middleware
Route::middleware(['auth'])->group(function () {
    Route::get('admin', [AdminController::class, 'index'])->name('admin.usuarios.index');
    Route::post('admin', [AdminController::class, 'store'])->name('admin.usuarios.store');
    Route::put('admin/{id}', [AdminController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('admin/{id}', [AdminController::class, 'destroy'])->name('admin.usuarios.destroy');
    Route::post('admin/usuarios/filtrar', [AdminController::class, 'filtrar'])->name('admin.usuarios.filtrar');
});

// Grupo de rutas para retos (administrador)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('retos', [RetoAdminController::class, 'index'])->name('retos.index');
    Route::post('retos', [RetoAdminController::class, 'store'])->name('retos.store');
    Route::put('retos/{id}', [RetoAdminController::class, 'update'])->name('retos.update');
    Route::delete('retos/{id}', [RetoAdminController::class, 'destroy'])->name('retos.destroy');
});

// Grupo de rutas para reportes (administrador)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('reportes', [ReporteAdminController::class, 'index'])->name('reportes.index');
    Route::delete('reportes/{id}', [ReporteAdminController::class, 'destroy'])->name('reportes.destroy');
});

// Grupo de rutas para usuarios normales
Route::middleware(['auth'])->group(function () {
    Route::get('user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    // mostrar racha y puntos
    Route::get('chat', [ChatLayoutController::class, 'show'])->name('chat.show');

    
    // Ruta para actualizar estado
    Route::post('estado/actualizar', [EstadoController::class, 'actualizarEstado'])->name('estado.actualizar');

    Route::get('user/friendchat', [FriendChatController::class, 'index'])->name('user.friendchat');
});

Route::prefix('retos')->name('retos.')->group(function () {
    Route::view('reto', 'Retos.reto')->name('reto');
    Route::view('guide', 'Retos.guide')->name('guide'); // Asegúrate de que el nombre sea 'guide'

});

// Middleware para pasar variables de racha y puntos a todas las vistas
Route::middleware(['auth'])->group(function () {
    Route::get('retos/reto', [RetoController::class, 'show'])->name('retos.reto');
    
    Route::get('retos/guide', function () {
        $user = Auth::user();
        // Actualizar estado a Disponible (5)
        // User::where('id_usuario', $user->id_usuario)->update(['id_estado' => 5]);
        
        return view('Retos.guide', [
            'racha' => $user->racha,
            'puntos' => $user->puntos,
            'username' => $user->username,
            'nombre_completo' => $user->nombre_completo,
            'imagen_perfil' => $user->img ? 'img/profile_img/' . $user->img : null,
        ]);
    })->name('retos.guide');
});