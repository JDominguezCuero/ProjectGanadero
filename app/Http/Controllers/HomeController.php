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

}
