<?php

namespace App\Http\Controllers;

use App\Models\InventarioAlimentos;
use Illuminate\Http\Request;

class InventarioAlimentosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $inventario = InventarioAlimentos::all();
            $msg = $request->query('msg');
            $error = $request->query('error');

            return view('gestionInventario.inventario', compact('inventario', 'msg', 'error'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar el inventario.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventario.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'cantidad' => 'required|numeric|min:0',
                'unidad_medida' => 'required|string',
                'fecha_ingreso' => 'required|date',
            ]);

            InventarioAlimentos::create([
                'nombre' => $request->nombre,
                'cantidad' => $request->cantidad,
                'unidad_medida' => $request->unidad_medida,
                'fecha_ingreso' => $request->fecha_ingreso,
            ]);

            return redirect()
                ->route('inventario.index')
                ->with('msg', 'Alimento agregado correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->route('inventario.index')
                ->withErrors(['error' => 'Error al agregar el alimento.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $item = InventarioAlimentos::find($id);

            if (!$item) {
                return redirect()
                    ->route('inventario.index')
                    ->withErrors(['error' => 'Alimento no encontrado.']);
            }

            return view('inventario.show', compact('item'));
        } catch (\Exception $e) {
            return redirect()
                ->route('inventario.index')
                ->withErrors(['error' => 'Error al consultar los datos.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
        {
            try {
                $item = InventarioAlimentos::find($id);

                if (!$item) {
                    return redirect()
                        ->route('inventario.index')
                        ->withErrors(['error' => 'Alimento no encontrado para editar.']);
                }

                // Vista correcta
                return view('inventario.edit', compact('item'));

            } catch (\Exception $e) {
                return redirect()
                    ->route('inventario.index')
                    ->withErrors(['error' => 'Error al cargar el formulario de edición.']);
            }
        }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'cantidad' => 'required|numeric|min:0',
                'unidad_medida' => 'required|string',
                'fecha_ingreso' => 'required|date',
            ]);

            $item = InventarioAlimentos::find($id);

            if (!$item) {
                return redirect()
                    ->route('inventario.index')
                    ->withErrors(['error' => 'Alimento no encontrado para actualizar.']);
            }

            $item->update([
                'nombre' => $request->nombre,
                'cantidad' => $request->cantidad,
                'unidad_medida' => $request->unidad_medida,
                'fecha_ingreso' => $request->fecha_ingreso,
            ]);

            return redirect()
                ->route('inventario.index')
                ->with('msg', 'Alimento actualizado correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->route('inventario.index')
                ->withErrors(['error' => 'Error al actualizar el alimento.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $item = InventarioAlimentos::find($id);

            if (!$item) {
                return redirect()
                    ->route('inventario.index')
                    ->withErrors(['error' => 'ID de alimento no encontrado para eliminar.']);
            }

            $item->delete();

            return redirect()
                ->route('inventario.index')
                ->with('msg', 'Alimento eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->route('inventario.index')
                ->withErrors(['error' => 'Error al eliminar el alimento.']);
        }
    }
}

