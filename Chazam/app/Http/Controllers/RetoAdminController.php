<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reto;

class RetoAdminController extends Controller
{
    public function index()
    {
        $retos = Reto::all();
        return view('admin.retos.index', compact('retos'));
    }

    public function store(Request $request)
    {
        try {
            // Validar los datos del formulario
            $request->validate([
                'nom_reto' => 'required|string|max:100', // Validar el nombre del reto
                'desc_reto' => 'nullable|string', // Validar la descripción del reto
            ]);

            // Crear el reto en la base de datos
            Reto::create([
                'nom_reto' => $request->nom_reto,
                'desc_reto' => $request->desc_reto,
            ]);

            // Redirigir con un mensaje de éxito
            return redirect()->route('admin.retos.index')->with('success', 'Reto creado correctamente.');
        } catch (\Exception $e) {
            // Manejar errores y redirigir con un mensaje de error
            return redirect()->route('admin.retos.index')->with('error', 'Error al crear el reto: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id_reto)
    {
        $reto = Reto::findOrFail($id_reto);

        $request->validate([
            'nom_reto' => 'required|string|max:100', // Validar el nombre del reto
            'desc_reto' => 'nullable|string', // Validar la descripción del reto
        ]);

        $reto->update([
            'nom_reto' => $request->nom_reto,
            'desc_reto' => $request->desc_reto,
        ]);

        return redirect()->route('admin.retos.index')->with('update', 'Reto actualizado correctamente.');
    }

    public function destroy($id_reto)
    {
        $reto = Reto::findOrFail($id_reto);
        $reto->delete();

        return redirect()->route('admin.retos.index')->with('success', 'Reto eliminado correctamente.');
    }
}