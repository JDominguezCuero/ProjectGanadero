<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\ProductoGanadero;
use App\Models\CategoriaProducto;
use App\Models\Usuarios;

class ProductosListaController extends Controller
{
    const UPLOAD_SUBPATH = 'images/productos/';

    public function __construct()
    {
        // No forzar auth aqui si prefieres que index público funcione,
        // pero las rutas admin (manage/store/update/destroy) deben usar middleware('auth')
        // Puedes aplicar middleware en rutas si lo prefieres.
    }

    /**
     * Mostrar la lista de productos con filtros y ordenamientos (público).
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
            ->where('estado_oferta', 1);       // Solo productos activos

        // Filtro por categoría
        if ($filtros['filtro_categoria_id']) {
            $query->where('categoria_id', $filtros['filtro_categoria_id']);
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
     * Página de gestión: listar productos del usuario autenticado y categorías.
     * Vista esperada: resources/views/gestionProductos/gestionProducto.blade.php
     */
    public function manage(Request $request)
    {
        // Requiere usuario autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $userId = Auth::id();

            // Obtener productos del usuario con la relación categoria
            $productosEloquent = ProductoGanadero::with('categoria')
                ->where('id_usuario', $userId)
                ->get();

            // Mapear a objetos (NO ->toArray()) para que en Blade uses $item->campo
            $productos = $productosEloquent->map(function ($p) {
                return (object)[
                    'id_producto'          => $p->id_producto ?? $p->id ?? null,
                    'nombre_producto'      => $p->nombre_producto ?? $p->nombre ?? null,
                    'descripcion_producto' => $p->descripcion_producto ?? $p->descripcion ?? null,
                    'precio_unitario'      => $p->precio_unitario ?? $p->precio ?? 0,
                    'cantidad'             => $p->cantidad ?? $p->stock ?? 0,
                    'imagen_url'           => $p->imagen_url ?? null,
                    'categoria_id'         => $p->categoria_id ?? ($p->categoria->id_categoria ?? null),
                    'nombre_categoria'     => $p->categoria->nombre_categoria ?? $p->categoria->nombre ?? null,
                    'estado_oferta'        => $p->estado_oferta ?? 0,
                    'precio_anterior'      => $p->precio_anterior ?? null,
                ];
            });

            // Categorías como array (tus partials las esperan así)
            $categoriasEloquent = CategoriaProducto::all();
            $categorias = $categoriasEloquent->map(function ($c) {
                return [
                    'id_categoria' => $c->id_categoria ?? $c->id ?? null,
                    'nombre_categoria' => $c->nombre_categoria ?? $c->nombre ?? null,
                ];
            })->toArray();

            // Pasa $productos (Collection de objetos) y $categorias (array)
            return view('gestionProductos.gestionProducto', [
                'productos'  => $productos,
                'categorias' => $categorias,
            ]);
        } catch (\Exception $e) {
            Log::error("Error en ProductosListaController@manage: " . $e->getMessage());
            $mensajeUsuario = $this->friendlyDbError($e->getMessage());
            return redirect()->route('productos.index')->with('error', $mensajeUsuario);
        }
    }


    /**
     * Guarda un nuevo producto (equivalente a 'agregar').
     * Ruta esperada: POST /productos  (o la que prefieras)
     */
    public function store(Request $request)
    {
        // Requiere auth
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $validator = Validator::make($request->all(), [
                'nombre'        => 'required|string|max:255',
                'descripcion'   => 'nullable|string',
                'precio'        => 'required|numeric|min:0.01',
                'stock'         => 'required|integer|min:0',
                'categoria_id'  => 'required',
                'imagen'        => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
                'precio_anterior' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                // return redirect()->route('productos.manage')->with('error', implode(' | ', $validator->errors()->all()));
                return redirect()
                ->route('productos.manage')
                ->withErrors($validator)
                ->withInput();
            }

            $nombre = $request->input('nombre');
            $descripcion = $request->input('descripcion');
            $precio = $request->input('precio');
            $stock = $request->input('stock');
            $categoria_id = $request->input('categoria_id');
            $estado_oferta = $request->has('estado_oferta') ? 1 : 0;
            $precio_anterior = $estado_oferta ? $request->input('precio_anterior') : null;

            $imagen_url = '';

            if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                $file = $request->file('imagen');
                $ext = $file->getClientOriginalExtension();
                $allowed = ['jpg','jpeg','png','gif'];
                if (!in_array(strtolower($ext), $allowed)) {
                    return redirect()->route('productos.manage')->with('error', 'Tipo de archivo de imagen no permitido.');
                }

                $filename = md5(time() . Str::random(8) . $file->getClientOriginalName()) . '.' . $ext;
                $destPath = public_path(self::UPLOAD_SUBPATH);
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0755, true);
                }

                $moved = $file->move($destPath, $filename);
                if ($moved) {
                    $imagen_url = asset(self::UPLOAD_SUBPATH . $filename);
                } else {
                    return redirect()->route('productos.manage')->with('error', 'Error al mover el archivo subido.');
                }
            }

            $producto = new ProductoGanadero();

            // Rellenar campos según el esquema de tu modelo
            $producto->nombre_producto      = $nombre;
            $producto->descripcion_producto = $descripcion;
            $producto->precio_unitario      = $precio;
            $producto->cantidad             = $stock;
            $producto->imagen_url           = $imagen_url;
            $producto->categoria_id         = $categoria_id;
            $producto->estado_oferta        = $estado_oferta;
            $producto->precio_anterior      = $precio_anterior;
            $producto->id_usuario           = Auth::id();

            $producto->save();

            return redirect()->route('productos.manage')->with('msg', 'Producto agregado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error en ProductosListaController@store: ' . $e->getMessage());
            $mensajeUsuario = $this->friendlyDbError($e->getMessage());
            return redirect()->route('productos.manage')->with('error', $mensajeUsuario);
        }
    }

    /**
     * Actualiza un producto existente (equivalente a 'editar').
     * Ruta esperada: PUT /productos/{id}
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $producto = ProductoGanadero::where(function($q) use ($id) {
                $q->where('id_producto', $id)->orWhere('id', $id);
            })->first();

            if (!$producto) {
                return redirect()->route('productos.manage')->with('error', 'Producto no encontrado para editar.');
            }

            $validator = Validator::make($request->all(), [
                'nombre'        => 'required|string|max:255',
                'descripcion'   => 'nullable|string',
                'precio'        => 'required|numeric|min:0.01',
                'stock'         => 'required|integer|min:0',
                'categoria_id'  => 'required',
                'imagen'        => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
                'precio_anterior' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                // return redirect()->route('productos.manage')->with('error', implode(' | ', $validator->errors()->all()));
                return redirect()
                ->route('productos.manage')
                ->withErrors($validator)
                ->withInput();
            }

            $nombre = $request->input('nombre');
            $descripcion = $request->input('descripcion');
            $precio = $request->input('precio');
            $stock = $request->input('stock');
            $categoria_id = $request->input('categoria_id');
            $estado_oferta = $request->has('estado_oferta') ? 1 : 0;
            $precio_anterior = $estado_oferta ? $request->input('precio_anterior') : null;

            $imagen_url_actual = $request->input('imagen_url_actual', $producto->imagen_url ?? '');
            $imagen_url = $imagen_url_actual;

            if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                $file = $request->file('imagen');
                $ext = $file->getClientOriginalExtension();
                $allowed = ['jpg','jpeg','png','gif'];
                if (!in_array(strtolower($ext), $allowed)) {
                    return redirect()->route('productos.manage')->with('error', 'Tipo de archivo de imagen no permitido para la actualización.');
                }

                $filename = md5(time() . Str::random(8) . $file->getClientOriginalName()) . '.' . $ext;
                $destPath = public_path(self::UPLOAD_SUBPATH);
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0755, true);
                }

                $moved = $file->move($destPath, $filename);
                if ($moved) {
                    $imagen_url = asset(self::UPLOAD_SUBPATH . $filename);
                } else {
                    return redirect()->route('productos.manage')->with('error', 'Error al mover el nuevo archivo subido.');
                }
            }

            $producto->nombre_producto      = $nombre;
            $producto->descripcion_producto = $descripcion;
            $producto->precio_unitario      = $precio;
            $producto->cantidad             = $stock;
            $producto->imagen_url           = $imagen_url;
            $producto->categoria_id         = $categoria_id;
            $producto->estado_oferta        = $estado_oferta;
            $producto->precio_anterior      = $precio_anterior;
            $producto->id_usuario           = Auth::id();

            $producto->save();

            return redirect()->route('productos.manage')->with('msg', 'Producto actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error en ProductosListaController@update: ' . $e->getMessage());
            $mensajeUsuario = $this->friendlyDbError($e->getMessage());
            return redirect()->route('productos.manage')->with('error', $mensajeUsuario);
        }
    }

    /**
     * Elimina un producto (equivalente a 'eliminar').
     * Ruta esperada: DELETE /productos/{id}
     */
    public function destroy($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $producto = ProductoGanadero::where(function($q) use ($id) {
                $q->where('id_producto', $id);
            })->first();

            if (!$producto) {
                return redirect()->route('productos.manage')->with('error', 'ID de producto no encontrado para eliminar.');
            }

            $producto->delete();

            return redirect()->route('productos.manage')->with('msg', 'Producto eliminado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error en ProductosListaController@destroy: ' . $e->getMessage());
            $mensajeUsuario = $this->friendlyDbError($e->getMessage());
            return redirect()->route('productos.manage')->with('error', $mensajeUsuario);
        }
    }

    /**
     * Retornar detalle de producto en formato JSON (para el modal dinámico).
     */
    public function detalle($id)
    {
        $producto = ProductoGanadero::with(['usuario', 'categoria'])
            ->where('id_producto', $id)
            ->orWhere('id', $id)
            ->first();

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado.'], 404);
        }

        return response()->json($producto);
    }

    /**
     * Mensaje amistoso para errores de BD detectados en el mensaje.
     */
    protected function friendlyDbError($errorMsg)
    {
        if (str_contains($errorMsg, 'Unknown column') || str_contains($errorMsg, 'Base table or view not found')) {
            return "Hubo un problema con la base de datos (columnas o tabla no encontradas). Verifica la estructura.";
        }
        return "Ocurrió un error inesperado en el servidor. Contacte al administrador.";
    }
}
