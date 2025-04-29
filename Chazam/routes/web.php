<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatLayoutController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\RetoController;
use App\Http\Controllers\FriendChatController;
use App\Http\Controllers\RetoAdminController;
use App\Http\Controllers\ReporteAdminController;
use App\Http\Controllers\ProductosAdminController;
use App\Http\Controllers\MomentmsController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\CompraController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
// Grupo de rutas para el administrador con middleware
// Route::middleware(['auth'])->group(function () {
    
// });

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

// Rutas de autenticación
Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('register', [AuthController::class, 'store'])->name('auth.register');
Route::post('check-availability', [AuthController::class, 'checkAvailability'])->name('auth.check-availability');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Middleware para proteger todas las rutas
Route::middleware(['auth'])->group(function () {
    // Rutas para el administrador
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('usuarios.index');
        Route::post('/', [AdminController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{id}', [AdminController::class, 'update'])->name('usuarios.update');
        Route::delete('/{id}', [AdminController::class, 'destroy'])->name('usuarios.destroy');
        Route::post('/usuarios/filtrar', [AdminController::class, 'filtrar'])->name('usuarios.filtrar');

        // Rutas para retos (administrador)
        Route::get('retos', [RetoAdminController::class, 'index'])->name('retos.index');
        Route::post('retos', [RetoAdminController::class, 'store'])->name('retos.store');
        Route::put('retos/{id}', [RetoAdminController::class, 'update'])->name('retos.update');
        Route::delete('retos/{id}', [RetoAdminController::class, 'destroy'])->name('retos.destroy');

        // Rutas para reportes (administrador)
        Route::get('reportes', [ReporteAdminController::class, 'index'])->name('reportes.index');
        Route::delete('reportes/{id}', [ReporteAdminController::class, 'destroy'])->name('reportes.destroy');

        // Rutas para productos (administrador)
        Route::get('productos', [ProductosAdminController::class, 'index'])->name('productos.index');
        Route::post('productos', [ProductosAdminController::class, 'store'])->name('productos.store');
        Route::put('productos/{id}', [ProductosAdminController::class, 'update'])->name('productos.update');
        Route::delete('productos/{id}', [ProductosAdminController::class, 'destroy'])->name('productos.destroy');
});

// Grupo de rutas para usuarios normales
Route::middleware(['auth'])->group(function () {
    Route::get('perfil/dashboard', [PerfilController::class, 'dashboard'])->name('perfil.dashboard');
    Route::get('user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    // mostrar racha y puntos
    Route::get('chat', [ChatLayoutController::class, 'show'])->name('chat.show');

    
    // Ruta para actualizar estado
    Route::post('estado/actualizar', [EstadoController::class, 'actualizarEstado'])->name('estado.actualizar');

    Route::get('user/friendchat', [FriendChatController::class, 'index'])->name('user.friendchat');
    Route::get('user/friendchat', [FriendChatController::class, 'index'])->name('user.friendchat');
    Route::get('user/momentms', [FriendChatController::class, 'momentms'])->name('user.momentms');

    Route::get('momentms', [MomentmsController::class, 'index'])->name('user.momentms');
    Route::get('momentms/create', [MomentmsController::class, 'create'])->name('momentms.create');
    Route::post('/momentms', [MomentmsController::class, 'store'])->name('momentms.store');
    Route::get('momentms/{id}', [MomentmsController::class, 'show'])->name('momentms.show');
    Route::get('momentms/{id}/data', [MomentmsController::class, 'getData'])->name('momentms.data');
});

Route::prefix('retos')->name('retos.')->group(function () {
    Route::view('reto', 'Retos.reto')->name('reto');
    Route::view('guide', 'Retos.guide')->name('guide'); // Asegúrate de que el nombre sea 'guide'
});

// Rutas para usuarios autenticados
// Route::middleware(['auth'])->group(function () {
    Route::prefix('perfil')->name('perfil.')->group(function () {
        Route::get('/dashboard', [PerfilController::class, 'dashboard'])->name('dashboard');
        Route::get('/personalizacion', [PerfilController::class, 'edit'])->name('personalizacion');
        Route::put('/update', [PerfilController::class, 'update'])->name('update');
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

    // Rutas para retos
    Route::prefix('retos')->name('retos.')->group(function () {
        Route::get('reto', [RetoController::class, 'show'])->name('reto');
        Route::get('guide', function () {
            $user = Auth::user();
            return view('Retos.guide', [
                'racha' => $user->racha,
                'puntos' => $user->puntos,
                'username' => $user->username,
                'nombre_completo' => $user->nombre_completo,
                'imagen_perfil' => $user->img ? 'img/profile_img/' . $user->img : null,
            ]);
        })->name('guide');
    });

    // Rutas para la tienda
    Route::get('/tienda', [TiendaController::class, 'index'])->name('tienda.index');
    Route::get('/producto/{id}/comprar', [CompraController::class, 'show'])->name('producto.comprar');
    Route::post('/producto/{id}/checkout', [CompraController::class, 'checkout'])->name('producto.checkout');

    // Rutas para el estado de los usuarios
    Route::post('/estado/actualizar', [EstadoController::class, 'actualizarEstado'])->name('estado.actualizar');
    Route::get('/estado/usuarios-en-linea', [EstadoController::class, 'obtenerUsuariosEnLinea'])->name('estado.usuarios-en-linea');

    // Rutas para el chat
    Route::get('chat', [ChatLayoutController::class, 'show'])->name('chat.show');
    Route::get('user/friendchat', [FriendChatController::class, 'index'])->name('user.friendchat');
});
