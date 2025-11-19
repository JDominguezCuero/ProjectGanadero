{{-- resources/views/gestionProductos/gestionProducto.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="flex min-h-screen w-full">
    @if(session('usuario'))
        @include('layouts.sidebar')
    @endif

    <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">
        <div class="hm-wrapper">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Productos</h1>
                <button class="btn btn-success" data-toggle="modal" data-target="#modalAgregarProducto">
                    Agregar Nuevo Producto
                </button>
            </div>

            {{-- Mensajes flash (éxito / error) --}}
            @if(session('msg'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showModal('✅ Operación Exitosa', @json(session('msg')), 'success');
                    });
                </script>
            @endif
            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showModal('❌ Error', @json(session('error')), 'error');
                    });
                </script>
            @endif

            <div style="position: relative;">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Buscar producto..." title="Escribe un nombre" style="padding-left: 35px;">
                <i data-lucide="search" style="position: absolute; left: 8px; top: 40%; transform: translateY(-50%); width: 16px; height: 16px; color: gray;"></i>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-md rounded-lg overflow-hidden mt-4">
                <table id="myTable" class="min-w-full border border-gray-200 text-sm">
                    <thead class="bg-green-700 text-white">
                        <tr>
                            <th class="py-3 px-4 text-center w-[80px]">Imagen</th>
                            <th class="py-3 px-4 text-center w-[150px]">Nombre</th>
                            <th class="py-3 px-4 text-center w-[100px]">Precio</th>
                            <th class="py-3 px-4 text-center w-[100px]">Cantidad</th>
                            <th class="py-3 px-4 text-center w-[110px]">Categoría</th>
                            <th class="py-3 px-4 text-center w-[100px]">En Oferta</th>
                            <th class="py-3 px-4 text-center w-[120px]">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($productos as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-2 px-4 text-center">
                                @if(!empty($item->imagen_url))
                                    <img src="{{ e($item->imagen_url) }}" alt="Producto" class="product-thumbnail">
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="py-2 px-4 text-center">{{ e($item->nombre_producto) }}</td>
                            <td class="py-2 px-4 text-center">$ {{ number_format($item->precio_unitario, 2) }}</td>
                            <td class="py-2 px-4 text-center">{{ e($item->cantidad) }}</td>
                            <td class="py-2 px-4 text-center">{{ e($item->nombre_categoria ?? 'Sin Categoría') }}</td>
                            <td class="py-2 px-4 text-center">{{ $item->estado_oferta == 1 ? 'Sí' : 'No' }}</td>
                            <td class="py-3 px-6 text-center align-middle">
                                <div class="flex justify-center items-center gap-2">
                                    {{-- Editar: abrimos modal y rellenamos con data-attributes --}}
                                    <i
                                        data-lucide="square-pen"
                                        class="text-blue-600 hover:text-blue-800 cursor-pointer"
                                        style="width:16px; height:16px;"
                                        data-toggle="modal"
                                        data-target="#modalEditarProducto"
                                        data-product="{{ htmlspecialchars(json_encode([
                                            'id' => $item->id_producto,
                                            'nombre' => $item->nombre_producto,
                                            'descripcion' => $item->descripcion_producto,
                                            'precio' => $item->precio_unitario,
                                            'stock' => $item->cantidad,
                                            'imagen_url' => $item->imagen_url,
                                            'categoria_id' => $item->categoria_id,
                                            'estado_oferta' => $item->estado_oferta,
                                            'precio_anterior' => $item->precio_anterior
                                        ]), ENT_QUOTES, 'UTF-8') }}"
                                    ></i>



                                    <span class="mx-1 text-gray-400">|</span>

                                    {{-- Eliminar: ruta RESTful --}}
                                    <form action="{{ route('productos.destroy', $item->id_producto) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Estás seguro que deseas eliminar este producto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-link p-0 m-0">
                                            <i data-lucide="trash-2" class="inline-block text-red-500 hover:text-red-700 cursor-pointer" style="width:16px; height:16px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">No hay productos registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 30px;">
                <button class="btn btn-success" onclick="exportarTablaAExcel()">
                    Exportar Excel
                </button>
            </div>
        </div>

        {{-- registrar_producto.blade.php --}}
        

    </main>
</div>

{{-- Incluye tus modales como partials --}}
@include('gestionProductos.partials.registrar_producto')
@include('gestionProductos.partials.editar_producto')

@endsection

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

<script>
$(document).ready(function() {

    $(document).on('click', '[data-toggle="modal"][data-target="#modalEditarProducto"]', function() {

        const dataStr = $(this).attr('data-product');
        if (!dataStr) return;

        let product;
        try {
            product = JSON.parse(dataStr);
        } catch (err) {
            console.error('Error parseando data-product:', err, dataStr);
            return;
        }

        // Rellenar campos del modal
        $('#editar_id_producto').val(product.id);
        $('#editar_nombre').val(product.nombre);
        $('#editar_descripcion').val(product.descripcion);
        $('#editar_precio').val(product.precio);
        $('#editar_stock').val(product.stock);
        $('#editar_imagen_url_actual').val(product.imagen_url);

        if (product.imagen_url) {
            $('#imagen_preview_editar').attr('src', product.imagen_url).show();
        } else {
            $('#imagen_preview_editar').hide();
        }

        $('#editar_categoria_id').val(product.categoria_id);
        $('#editar_estado_oferta').prop('checked', product.estado_oferta == 1);
        $('#editar_precio_anterior').val(product.precio_anterior ?? '');

        // Mostrar/ocultar precio anterior según checkbox
        const togglePrecioAnterior = () => {
            if ($('#editar_estado_oferta').is(':checked')) {
                $('#editar_precio_anterior_group').show();
                $('#editar_precio_anterior').attr('required', 'required');
            } else {
                $('#editar_precio_anterior_group').hide();
                $('#editar_precio_anterior').removeAttr('required').val('');
            }
        };
        togglePrecioAnterior();
        $('#editar_estado_oferta').off('change').on('change', togglePrecioAnterior);

        // Ajustar action del formulario
        const updateUrl = '{{ route("productos.update", ":id") }}'.replace(':id', product.id);
        $('#formEditarProducto').attr('action', updateUrl);

        // Abrir modal
        $('#modalEditarProducto').modal('show');
    });

});
</script>



<script>
    // Búsqueda client-side (sin cambios)
    function myFunction() {
        let input = document.getElementById("myInput");
        let filter = input.value.toUpperCase();
        let table = document.getElementById("myTable");
        let tr = table.getElementsByTagName("tr");
        for (let i = 1; i < tr.length; i++) {
            let rowContainsFilter = false;
            const tds = tr[i].getElementsByTagName("td");
            for (let j = 1; j < tds.length - 1; j++) {
                const td = tds[j];
                if (td) {
                    let txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        rowContainsFilter = true;
                        break;
                    }
                }
            }
            tr[i].style.display = rowContainsFilter ? "" : "none";
        }
    }
</script>

<script>
    // Exportar a Excel con SheetJS (sin cambios)
    function exportarTablaAExcel() {
        var tabla = document.getElementById("myTable");
        var hoja = XLSX.utils.table_to_sheet(tabla, { raw: true });

        const rango = XLSX.utils.decode_range(hoja['!ref']);
        for (let R = rango.s.r; R <= rango.e.r; ++R) {
            const celda = XLSX.utils.encode_cell({ r: R, c: rango.e.c });
            delete hoja[celda];
        }
        rango.e.c--;
        hoja['!ref'] = XLSX.utils.encode_range(rango);

        var libro = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(libro, hoja, "Inventario");
        XLSX.writeFile(libro, "inventario_productos.xlsx", { bookType: "xlsx", type: "binary" });
    }
</script>

<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
@endpush

