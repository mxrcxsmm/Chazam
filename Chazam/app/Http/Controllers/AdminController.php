<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rol;
use App\Models\Estado;
use App\Models\Nacionalidad;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Mostrar la lista de administradores
    public function index()
    {
        $admins = User::all();
        $nacionalidades = Nacionalidad::all(); // Obtiene todas las nacionalidades
        return view('admin.usuarios.index', compact('admins', 'nacionalidades')); // Pasa los administradores y nacionalidades a la vista
    }

    // Eliminar un administrador de la base de datos
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // ID del estado "PermaBan"
        $idEstadoPermaBan = 4; // Asegúrate de que este ID sea correcto

        // Verificar si el usuario está en estado PermaBan
        if ($user->id_estado !== $idEstadoPermaBan) {
            return redirect()->back()->with('error', 'Solo se pueden eliminar usuarios con estado PermaBan.');
        }

        // Proceder con la eliminación
        $user->delete();

        return redirect()->route('admin.usuarios.index')->with('eliminar', 'Usuario eliminado correctamente.');
    }
    
    // Filtrar usuarios mediante AJAX
    public function filtrar(Request $request)
    {
        $query = User::query();

        if ($request->filled('id')) {
            $query->where('id_usuario', $request->id);
        }

        if ($request->filled('username')) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }

        if ($request->filled('nombre_completo')) {
            $query->whereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ['%' . $request->nombre_completo . '%']);
        }

        if ($request->filled('nacionalidad')) {
            $query->where('id_nacionalidad', $request->nacionalidad);
        }

        if ($request->filled('rol')) {
            $query->where('id_rol', $request->rol);
        }

        if ($request->filled('genero')) {
            $query->where('genero', $request->genero);
        }

        $admins = $query->get();

        $html = view('admin.usuarios.tabla-usuarios', compact('admins'))->render();

        return response()->json(['html' => $html]);
    }

    public function ban($id)
    {
        try {
            $user = User::findOrFail($id);

            // Verificar si el usuario ya está permabaneado
            $estadoPermaban = Estado::where('nom_estado', 'PermaBan')->first();
            if ($user->id_estado == $estadoPermaban->id_estado) {
                return redirect()->route('admin.usuarios.index')->with('error', 'El usuario ya está permabaneado y no se pueden realizar más acciones.');
            }

            // Verificar si el usuario está actualmente baneado y si el baneo ha expirado
            if ($user->fin_ban && now()->lt($user->fin_ban)) {
                return redirect()->route('admin.usuarios.index')->with('error', 'El usuario está actualmente baneado. No se puede aplicar un nuevo baneo hasta que el actual haya expirado.');
            }

            // Incrementar el número de strikes hasta un máximo de 4
            $user->strikes = min($user->strikes + 1, 4);

            // Determinar la duración del ban y el estado
            switch ($user->strikes) {
                case 1:
                    $banDuration = Carbon::now()->addHour(); // 1 hora
                    $estado = Estado::where('nom_estado', 'Ban')->first();
                    break;
                case 2:
                    $banDuration = Carbon::now()->addHours(12); // 12 horas
                    $estado = Estado::where('nom_estado', 'Ban')->first();
                    break;
                case 3:
                    $banDuration = Carbon::now()->addHours(24); // 24 horas
                    $estado = Estado::where('nom_estado', 'Ban')->first();
                    break;
                case 4:
                    $banDuration = null; // Permaban
                    $estado = Estado::where('nom_estado', 'PermaBan')->first();
                    break;
            }

            // Actualizar el estado del usuario
            $user->id_estado = $estado->id_estado;
            $user->inicio_ban = $user->strikes >= 4 ? null : Carbon::now();
            $user->fin_ban = $user->strikes >= 4 ? null : $banDuration;

            $user->save();

            return redirect()->route('admin.usuarios.index')->with('success', 'El usuario ha sido baneado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al banear el usuario: ' . $e->getMessage());
        }
    }
}
