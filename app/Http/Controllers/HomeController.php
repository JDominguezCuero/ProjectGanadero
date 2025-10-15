<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductoGanadero;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ejemplo: traer los primeros 10 productos
        $productos = ProductoGanadero::orderBy('fecha_publicacion', 'desc')
            ->take(10)
            ->get();

        // Traer productos populares agrupados por categoría
        $productosPopularesTabs = ProductoGanadero::with('categoria')
            ->get()
            ->groupBy(fn($p) => $p->categoria->nombre_categoria ?? 'Otros');

        return view('index', compact('productos', 'productosPopularesTabs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $producto = ProductoGanadero::findOrFail($id);
        return view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
