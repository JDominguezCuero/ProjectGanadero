<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductosListaController extends Controller
{
    /**
     * Mostrar la lista de productos con filtros y ordenamientos.
     */
    public function index(Request $request)
    {
        // Recuperar filtros desde la URL (GET)
        $filtros = [
            'filtro_categoria_id' => $request->input('categoria'),
            'filtro_busqueda' => $request->input('buscar'),
            'filtro_precio_min' => $request->input('precio_min'),
            'filtro_precio_max' => $request->input('precio_max'),
            'ordenar_por' => $request->input('ordenar_por', 'fecha_reciente'),
        ];

        // Consulta base
        $query = DB::table('productos')
            ->join('usuarios', 'productos.id_usuario', '=', 'usuarios.id_usuario')
            ->join('categorias', 'productos.id_categoria', '=', 'categorias.id_categoria')
            ->select(
                'productos.*',
                'usuarios.nombre_usuario',
                'categorias.nombre_categoria'
            )
            ->where('productos.estado', '=', 1); // Solo productos activos

        // Aplicar filtros dinámicos
        if ($filtros['filtro_categoria_id']) {
            $query->where('productos.id_categoria', $filtros['filtro_categoria_id']);
        }

        if ($filtros['filtro_busqueda']) {
            $busqueda = '%' . $filtros['filtro_busqueda'] . '%';
            $query->where(function ($q) use ($busqueda) {
                $q->where('productos.nombre_producto', 'like', $busqueda)
                  ->orWhere('productos.descripcion_producto', 'like', $busqueda);
            });
        }

        if ($filtros['filtro_precio_min']) {
            $query->where('productos.precio_unitario', '>=', $filtros['filtro_precio_min']);
        }

        if ($filtros['filtro_precio_max']) {
            $query->where('productos.precio_unitario', '<=', $filtros['filtro_precio_max']);
        }

        // Ordenamiento
        switch ($filtros['ordenar_por']) {
            case 'precio_asc':
                $query->orderBy('productos.precio_unitario', 'asc');
                break;
            case 'precio_desc':
                $query->orderBy('productos.precio_unitario', 'desc');
                break;
            case 'nombre_asc':
                $query->orderBy('productos.nombre_producto', 'asc');
                break;
            default:
                $query->orderBy('productos.fecha_publicacion', 'desc');
                break;
        }

        // Ejecutar consulta
        $productos = $query->get();

        // Agrupar por categoría
        $productos_por_categoria = $productos->groupBy('nombre_categoria');

        // Cargar categorías para el filtro lateral
        $categorias = DB::table('categorias')->get();

        return view('productos', [
            'productos_por_categoria' => $productos_por_categoria,
            'categorias' => $categorias,
            'filtros' => $filtros,
        ]);
    }

    /**
     * Retornar detalle de producto en formato JSON (para el modal dinámico).
     */
    public function detalle($id)
    {
        $producto = DB::table('productos')
            ->join('usuarios', 'productos.id_usuario', '=', 'usuarios.id_usuario')
            ->select(
                'productos.*',
                'usuarios.nombre_usuario',
                'usuarios.correo_usuario',
                'usuarios.telefono_usuario'
            )
            ->where('productos.id_producto', $id)
            ->first();

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado.'], 404);
        }

        return response()->json($producto);
    }
}