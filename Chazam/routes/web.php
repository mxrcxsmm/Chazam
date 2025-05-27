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
use App\Http\Controllers\PagosAdminController;
use App\Http\Controllers\CompraController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SolicitudUserController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\VistaController;
use App\Http\Controllers\ComunidadesController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\AmistadController;
use App\Http\Controllers\UserSearchController;
use App\Http\Middleware\CheckUserStatus;



Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

// Rutas de autenticación
Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('register', [AuthController::class, 'store'])->name('auth.register');
Route::post('check-availability', [AuthController::class, 'checkAvailability'])->name('auth.check-availability');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Middleware para proteger todas las rutas
Route::middleware(['auth', \App\Http\Middleware\CheckUserStatus::class])->group(function () {
    // Rutas para el administrador
    Route::prefix('admin')->middleware([\App\Http\Middleware\AdminMiddleware::class])->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('usuarios.index');
        Route::delete('/{id}', [AdminController::class, 'destroy'])->name('usuarios.destroy');
        Route::post('/usuarios/filtrar', [AdminController::class, 'filtrar'])->name('usuarios.filtrar');
        Route::post('/usuarios/{id}/ban', [AdminController::class, 'ban'])->name('usuarios.ban');
        Route::get('/usuarios/{id}/json', [AdminController::class, 'getUserJson'])->name('usuarios.json');

        // Rutas para reportes (administrador)
        Route::get('reportes', [ReporteAdminController::class, 'index'])->name('reportes.index');
        Route::delete('reportes/{id}', [ReporteAdminController::class, 'destroy'])->name('reportes.destroy');
        Route::get('/reportes/nuevos', [ReporteAdminController::class, 'contarNuevos'])->name('admin.reportes.nuevos');

        // Rutas para productos (administrador)
        Route::get('productos', [ProductosAdminController::class, 'index'])->name('productos.index');
        Route::post('productos', [ProductosAdminController::class, 'store'])->name('productos.store');
        Route::put('productos/{id}', [ProductosAdminController::class, 'update'])->name('productos.update');
        Route::delete('productos/{id}', [ProductosAdminController::class, 'destroy'])->name('productos.destroy');

        // Rutas para pagos (administrador)
        Route::get('/pagos', [PagosAdminController::class, 'index'])->name('pagos.index');
        Route::post('/pagos', [PagosAdminController::class, 'store'])->name('pagos.store');
        Route::post('/pagos/filtrar', [PagosAdminController::class, 'filtrar'])->name('pagos.filtrar');        
        Route::put('/pagos/{id}', [PagosAdminController::class, 'update'])->name('pagos.update');
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
    });

    Route::prefix('retos')->name('retos.')->group(function () {
        Route::view('reto', 'Retos.reto')->name('reto');
        Route::view('guide', 'Retos.guide')->name('guide'); // Asegúrate de que el nombre sea 'guide'
    });

    // Rutas para usuarios autenticados
    Route::middleware(['auth'])->group(function () {
        Route::prefix('perfil')->name('perfil.')->group(function () {
            Route::get('/dashboard', [PerfilController::class, 'dashboard'])->name('dashboard');
            Route::get('/personalizacion', [PerfilController::class, 'edit'])->name('personalizacion');
            Route::get('/vista', [VistaController::class, 'show'])->name('vista');
            Route::post('/marco', [VistaController::class, 'cambiarMarco'])->name('cambiarMarco');
            Route::post('/glow', [VistaController::class, 'cambiarBrillo'])->name('cambiarBrillo');
            Route::put('/update', [PerfilController::class, 'update'])->name('update');
            Route::post('/perfil/check-availability', [PerfilController::class, 'checkAvailability'])->name('perfil.checkAvailability');
            Route::get('/mejoras', function () {
                return view('perfil.mejoras');
            })->name('mejoras');
            Route::get('/puntos', function () {
                return view('perfil.puntos');
            })->name('puntos');
        });
      Route::get('user/momentms', [FriendChatController::class, 'momentms'])->name('user.momentms');

      Route::get('momentms', [MomentmsController::class, 'index'])->name('user.momentms');
      Route::get('momentms/create', [MomentmsController::class, 'create'])->name('momentms.create');
      Route::post('/momentms', [MomentmsController::class, 'store'])->name('momentms.store');
      Route::get('momentms/search', [MomentmsController::class, 'search'])->name('momentms.search');
      Route::get('momentms/{id}', [MomentmsController::class, 'show'])->name('momentms.show');
      Route::get('momentms/{id}/data', [MomentmsController::class, 'getData'])->name('momentms.data');
      Route::delete('/momentms/{id}', [MomentmsController::class, 'destroy'])->name('momentms.destroy');
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

        // Rutas para el chat aleatorio
        Route::post('buscar-companero', [RetoController::class, 'buscarCompanero'])->name('buscar-companero');
        Route::post('enviar-mensaje', [RetoController::class, 'enviarMensaje'])->name('enviar-mensaje');
        Route::get('mensajes/{chatId}', [RetoController::class, 'obtenerMensajes'])->name('obtener-mensajes');
        Route::post('verificar-estado-chats', [RetoController::class, 'verificarEstadoChats'])->name('verificar-estado-chats');
        Route::get('verificar-chat/{chatId}', [RetoController::class, 'verificarChat'])->name('verificar-chat');
        Route::post('limpiar-estado', [RetoController::class, 'limpiarEstado'])->name('limpiar-estado');
        Route::get('puntos-diarios', [RetoController::class, 'obtenerPuntosDiarios'])->name('puntos-diarios');

        // Rutas para el manejo del skip
        Route::get('verificar-skip', [RetoController::class, 'verificarSkip'])->name('verificar-skip');
        Route::get('tiempo-skip', [RetoController::class, 'tiempoSkip'])->name('tiempo-skip');
        Route::post('activar-skip', [RetoController::class, 'activarSkip'])->name('activar-skip');
    });

    // Rutas para el estado de los usuarios
    Route::post('/estado/actualizar', [EstadoController::class, 'actualizarEstado'])->name('estado.actualizar');
    Route::get('/estado/usuarios-en-linea', [EstadoController::class, 'obtenerUsuariosEnLinea'])->name('estado.usuarios-en-linea');

    // Rutas para el chat
    Route::get('chat', [ChatLayoutController::class, 'show'])->name('chat.show');
    Route::get('user/friendchat', [FriendChatController::class, 'index'])->name('user.friendchat');

    // Rutas para la tienda
    Route::get('tienda', [TiendaController::class, 'index'])->name('tienda.index');

    // Rutas para Stripe
    Route::get('/producto/{id}/checkout', [StripeController::class, 'checkout'])->name('producto.comprar');
    Route::get('/stripe/success/{id}', [StripeController::class, 'success'])->name('stripe.success');
    Route::get('/stripe/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');
    Route::post('/comprar-con-puntos/{id}', [StripeController::class, 'comprarConPuntos'])->name('comprar.con.puntos');
    Route::post('/donar', [StripeController::class, 'donar'])->name('stripe.donar');
    Route::get('/donar/success', [StripeController::class, 'donationSuccess'])->name('stripe.donation.success');
    Route::get('/donar/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');
    Route::get('/producto/success/{producto}', [StripeController::class, 'productSuccess'])->name('stripe.product.success');
    
    // Rutas para el chat de amigos
    Route::get('user/chats', [FriendChatController::class, 'getUserChats'])->name('user.chats');
    Route::get('/user/chat/{id}/messages', [FriendChatController::class, 'getChatMessages'])->name('user.chat.messages');
    Route::post('/user/chat/{id}/send', [FriendChatController::class, 'sendMessage'])->name('user.chat.send');

    // Ruta para búsqueda de usuarios
    Route::get('/buscar-usuarios', [UserSearchController::class, 'search'])->name('user.search');

    // Rutas para solicitudes y bloqueos
    Route::post('/solicitudes/enviar', [SolicitudUserController::class, 'enviarSolicitud'])->name('solicitudes.enviar');
    Route::post('/solicitudes/bloquear', [SolicitudUserController::class, 'bloquearUsuario'])->name('solicitudes.bloquear');
    Route::get('/solicitudes/verificar-bloqueo/{id_usuario}', [SolicitudUserController::class, 'verificarBloqueo'])->name('solicitudes.verificar-bloqueo');
    Route::get('/solicitudes/verificar/{id_usuario}', [SolicitudUserController::class, 'verificarSolicitud'])->name('solicitudes.verificar');
    Route::get('/solicitudes/pendientes', [SolicitudUserController::class, 'getPendientes'])->name('solicitudes.pendientes');
    Route::post('/solicitudes/responder', [SolicitudUserController::class, 'responderSolicitud'])->name('solicitudes.responder');
    
    // Rutas para búsqueda de usuarios
    Route::get('/user/search', [UserController::class, 'search'])->name('user.search');
    
    // Ruta para reportes
    Route::post('/reportes/crear', [ReporteController::class, 'crear'])->name('reportes.crear');

    // Rutas para comunidades
    Route::get('/comunidades', [ComunidadesController::class, 'index'])->name('comunidades.index');
    Route::get('/comunidades/create', [ComunidadesController::class, 'create'])->name('comunidades.create');
    Route::post('/comunidades', [ComunidadesController::class, 'store'])->name('comunidades.store');
    Route::post('/comunidades/{id}/join', [ComunidadesController::class, 'join'])->name('comunidades.join');
    Route::get('/comunidades/{id}', [ComunidadesController::class, 'show'])->name('comunidades.show');
    Route::get('/comunidades/{id}/edit', [ComunidadesController::class, 'edit'])->name('comunidades.edit');
    Route::get('/comunidades/{id}/edit-form', [ComunidadesController::class, 'editForm'])->name('comunidades.edit-form');
    Route::put('/comunidades/{id}', [ComunidadesController::class, 'update'])->name('comunidades.update');
    Route::post('/comunidades/{id}/abandonar', [ComunidadesController::class, 'abandonar'])->name('comunidades.abandonar');
    Route::post('/comunidades/{id}/eliminar', [ComunidadesController::class, 'eliminar'])->name('comunidades.eliminar');

    // Nuevas rutas para la API de comunidades
    Route::get('/comunidades/{id}/members', [ComunidadesController::class, 'getMembers'])->name('comunidades.members');
    Route::get('/comunidades/{id}/messages', [ComunidadesController::class, 'getMessages'])->name('comunidades.messages');
    Route::post('/comunidades/{id}/send-message', [ComunidadesController::class, 'sendMessage'])->name('comunidades.send-message');

    Route::get('user/comunidades', [FriendChatController::class, 'comunidades'])->name('user.comunidades');

    // Rutas para que el usuario vea sus propias compras
    Route::get('/mis-compras', [CompraController::class, 'historial'])->name('compras.historial');
    Route::get('/mis-compras/factura/{pagoId}', [CompraController::class, 'descargarFactura'])
        ->middleware('auth')
        ->name('compras.factura');
    Route::post('/mis-compras/filtrar', [CompraController::class, 'filtrarAjax'])
        ->middleware('auth')
        ->name('compras.filtrar');

    // Rutas para el disclaimer
    Route::get('/retos/verificar-disclaimer', [RetoController::class, 'verificarDisclaimer'])->middleware(['auth']);
    Route::post('/retos/guardar-disclaimer', [RetoController::class, 'guardarDisclaimer'])->middleware(['auth']);

    // Rutas de amistades
    Route::get('/amistades', [AmistadController::class, 'index'])->name('amistades.index');
    Route::delete('/amistades/{idUsuario}', [AmistadController::class, 'destroy'])->name('amistades.destroy');
    Route::post('/amistades/{idUsuario}/bloquear', [AmistadController::class, 'bloquear'])->name('amistades.bloquear');
    Route::get('/amistades/bloqueados', [AmistadController::class, 'getBloqueados'])->name('amistades.bloqueados');
    Route::post('/amistades/desbloquear', [AmistadController::class, 'desbloquearUsuario'])->name('amistades.desbloquear');
});

Route::get('/chats', [FriendChatController::class, 'index'])->name('chats.index');