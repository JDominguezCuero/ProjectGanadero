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
            return redirect()
                ->route('inventario.index')
                ->with('error', 'Error al cargar el inventario: ' . $e->getMessage());
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
                ->with('success', '✅ Alimento agregado correctamente al inventario.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('inventario.index')
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', '❌ Error de validación en el formulario.');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('inventario.index')
                ->with('error', '❌ Error al agregar el alimento: ' . $e->getMessage());
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
                    ->with('error', '❌ Alimento no encontrado en el inventario.');
            }

            return view('inventario.show', compact('item'));
        } catch (\Exception $e) {
            return redirect()
                ->route('inventario.index')
                ->with('error', '❌ Error al consultar los datos del alimento.');
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
                    ->with('error', '❌ Alimento no encontrado para editar.');
            }

            return view('inventario.edit', compact('item'));

        } catch (\Exception $e) {
            return redirect()
                ->route('inventario.index')
                ->with('error', '❌ Error al cargar el formulario de edición.');
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
                    ->with('error', '❌ Alimento no encontrado para actualizar.');
            }

            $item->update([
                'nombre' => $request->nombre,
                'cantidad' => $request->cantidad,
                'unidad_medida' => $request->unidad_medida,
                'fecha_ingreso' => $request->fecha_ingreso,
            ]);

            return redirect()
                ->route('inventario.index')
                ->with('success', '✅ Alimento actualizado correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('inventario.index')
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', '❌ Error de validación en el formulario.');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('inventario.index')
                ->with('error', '❌ Error al actualizar el alimento: ' . $e->getMessage());
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
                    ->with('error', '❌ Alimento no encontrado para eliminar.');
            }

            $nombreAlimento = $item->nombre;
            $item->delete();

            return redirect()
                ->route('inventario.index')
                ->with('success', "✅ Alimento '$nombreAlimento' eliminado correctamente.");

        } catch (\Exception $e) {
            return redirect()
                ->route('inventario.index')
                ->with('error', '❌ Error al eliminar el alimento: ' . $e->getMessage());
        }
    }
}