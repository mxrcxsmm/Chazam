<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rol;
use App\Models\Estado;
use App\Models\Nacionalidad;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Mostrar la lista de administradores
    public function index()
    {
        $admins = User::all();
        $nacionalidades = Nacionalidad::all(); // Obtiene todas las nacionalidades
        return view('admin.usuarios.index', compact('admins', 'nacionalidades')); // Pasa los administradores y nacionalidades a la vista
    }

    // Guardar un nuevo administrador en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'email' => 'required|email|unique:users,email',
            'descripcion' => 'nullable|string',
            'id_nacionalidad' => 'required|exists:nacionalidad,id_nacionalidad', // Validar que exista en la tabla nacionalidad
        ]);

        try {
            User::create([
                'username' => $request->username,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'email' => $request->email,
                'descripcion' => $request->descripcion,
                'password' => bcrypt('qweQWE123'), // Contraseña por defecto
                'id_rol' => 3, // Rol por defecto
                'id_estado' => 2, // Estado por defecto
                'id_nacionalidad' => $request->id_nacionalidad,
                'puntos' => 500, // Puntos iniciales
            ]);

            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear usuario: ' . $e->getMessage());
        }
    }

    // Mostrar el formulario para editar un administrador
    public function edit($id_usuario)
    {
        $user = User::findOrFail($id_usuario); // Busca el usuario por ID
        $nacionalidades = Nacionalidad::all(); // Obtiene todas las nacionalidades
        return view('admin.usuarios.edit', compact('user','nacionalidades')); // Pasa el usuario a la vista
    }

    // Actualizar un administrador en la base de datos
    public function update(Request $request, $id_usuario)
    {
        $user = User::findOrFail($id_usuario);

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id_usuario . ',id_usuario',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'email' => 'required|email|unique:users,email,' . $user->id_usuario . ',id_usuario',
            'descripcion' => 'nullable|string',
            'password' => 'nullable|string|min:8',
            'id_nacionalidad' => 'required|exists:nacionalidad,id_nacionalidad', // Validar que exista en la tabla nacionalidad
        ]);

        $user->update([
            'username' => $request->username,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'email' => $request->email,
            'descripcion' => $request->descripcion,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'id_nacionalidad' => $request->id_nacionalidad, // Actualizar la nacionalidad
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar un administrador de la base de datos
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
    
    // Filtrar usuarios mediante AJAX
    public function filtrar(Request $request)
    {
        try {
            // Iniciar la consulta
            $query = User::query();

            // Aplicar filtros si se proporcionan
            if ($request->filled('id')) {
                $query->where('id_usuario', 'like', '%' . $request->id . '%');
            }

            if ($request->filled('username')) {
                $query->where('username', 'like', '%' . $request->username . '%');
            }

            if ($request->filled('nombre_completo')) {
                $query->where(function ($q) use ($request) {
                    $q->where('nombre', 'like', '%' . $request->nombre_completo . '%')
                      ->orWhere('apellido', 'like', '%' . $request->nombre_completo . '%');
                });
            }

            if ($request->filled('nacionalidad')) {
                $query->where('id_nacionalidad', $request->nacionalidad);
            }

            if ($request->filled('rol')) {
                $query->where('id_rol', $request->rol);
            }

            // Ejecutar la consulta
            $admins = $query->get();

            // Si no hay resultados, devolver un mensaje vacío
            if ($admins->isEmpty()) {
                return response()->json(['html' => '<tr><td colspan="12" class="text-center">No se encontraron resultados.</td></tr>']);
            }

            // Devolver la vista parcial con los resultados
            $html = view('admin.usuarios.tabla-usuarios', compact('admins'))->render();
            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            // Manejar errores y devolver un mensaje de error
            return response()->json(['error' => 'Error al cargar los datos: ' . $e->getMessage()], 500);
        }
    }
}
