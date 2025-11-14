<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductoGanadero;
use App\Models\CategoriaProducto;
use App\Models\User;

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
            'filtro_busqueda'     => $request->input('buscar'),
            'filtro_precio_min'   => $request->input('precio_min'),
            'filtro_precio_max'   => $request->input('precio_max'),
            'ordenar_por'         => $request->input('ordenar_por', 'fecha_reciente'),
        ];

        // Consulta base con Eloquent
        $query = ProductoGanadero::query()
            ->with(['usuario', 'categoria'])   // Relaciones
            ->where('estado_oferta', 1);              // Solo productos activos

        // Filtro por categoría
        if ($filtros['filtro_categoria_id']) {
            $query->where('id_categoria', $filtros['filtro_categoria_id']);
        }

        // Filtro por texto
        if ($filtros['filtro_busqueda']) {
            $busqueda = '%' . $filtros['filtro_busqueda'] . '%';

            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre_producto', 'like', $busqueda)
                    ->orWhere('descripcion_producto', 'like', $busqueda);
            });
        }

        // Filtro precio mínimo
        if ($filtros['filtro_precio_min']) {
            $query->where('precio_unitario', '>=', $filtros['filtro_precio_min']);
        }

        // Filtro precio máximo
        if ($filtros['filtro_precio_max']) {
            $query->where('precio_unitario', '<=', $filtros['filtro_precio_max']);
        }

        // Ordenamiento
        switch ($filtros['ordenar_por']) {
            case 'precio_asc':
                $query->orderBy('precio_unitario', 'asc');
                break;
            case 'precio_desc':
                $query->orderBy('precio_unitario', 'desc');
                break;
            case 'nombre_asc':
                $query->orderBy('nombre_producto', 'asc');
                break;
            default:
                $query->orderBy('fecha_publicacion', 'desc');
                break;
        }

        // Obtener productos
        $productos = $query->get();

        // Agrupar por categoría
        $productos_por_categoria = $productos->groupBy(function ($producto) {
            return $producto->categoria->nombre_categoria ?? 'Sin categoría';
        });

        // Cargar todas las categorías
        $categorias = CategoriaProducto::all();

        return view('productos', [
            'productos_por_categoria' => $productos_por_categoria,
            'categorias'              => $categorias,
            'filtros'                 => $filtros,
        ]);
    }

    /**
     * Retornar detalle de producto en formato JSON (para el modal dinámico).
     */
    public function detalle($id)
    {
        $producto = ProductoGanadero::with(['usuario'])
            ->where('id_producto', $id)
            ->first();

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado.'], 404);
        }

        return response()->json($producto);
    }
}
