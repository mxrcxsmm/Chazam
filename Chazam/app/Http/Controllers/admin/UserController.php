<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        // Obtener todos los usuarios
        $users = User::all();

        // Pasar los usuarios a la vista de administración
        return view('admin.usuarios.index', compact('users'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            User::create($request->all());
        });

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado con éxito.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.usuarios.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado con éxito.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            User::destroy($id);
        });

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado con éxito.');
    }
}
