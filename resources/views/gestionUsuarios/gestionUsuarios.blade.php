{{-- resources/views/usuarios/index.blade.php --}}
@extends('layouts.app')

@section('content')

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showModal('❌ Error', @json(session('error')), 'error');
    });
</script>
@endif

@if(session('msg'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showModal('✅ Operación Exitosa', @json(session('msg')), 'success');
    });
</script>
@endif

<div class="flex min-h-screen w-full">

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    <main id="mainContent"
        class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full"
        style="margin: auto;">

        <div class="hm-wrapper">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    Gestión de Usuarios
                </h1>

                <button class="btn btn-success"
                        data-toggle="modal"
                        data-target="#modalAgregarUsuario">
                    Agregar Nuevo Usuario
                </button>
            </div>

            {{-- BUSCADOR --}}
            <div style="position: relative;">
                <input type="text" id="myInput"
                    onkeyup="myFunction()"
                    placeholder="Buscar usuario..."
                    title="Escribe un nombre"
                    style="padding-left: 35px;">
                <i data-lucide="search"
                   style="position: absolute; left: 8px; top: 40%; transform: translateY(-50%);
                          width: 16px; height: 16px; color: gray;"></i>
            </div>

            {{-- TABLA --}}
            <div class="bg-white shadow-md rounded-lg mt-4" style="overflow-x: auto;">
                <table id="myTable" class="min-w-full border border-gray-200 text-sm">
                    <thead class="bg-green-700 text-white">
                        <tr>
                            <th class="py-3 px-4 text-center w-[80px]">Imagen</th>
                            <th class="py-3 px-4 text-center w-[80px]">Nombre Completo</th>
                            <th class="py-3 px-4 text-center w-[150px]">Nombre de Usuario</th>
                            <th class="py-3 px-4 text-center w-[100px]">Correo</th>
                            <th class="py-3 px-4 text-center w-[100px]">Dirección</th>
                            <th class="py-3 px-4 text-center w-[100px]">Teléfono</th>
                            <th class="py-3 px-4 text-center w-[110px]">Estado</th>
                            <th class="py-3 px-4 text-center w-[100px]">Rol</th>
                            <th class="py-3 px-4 text-center w-[100px]">Descripción Rol</th>
                            <th class="py-3 px-4 text-center w-[120px]">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-100">

                        @forelse($usuarios as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-2 px-4 text-center">
                                @if($item->imagen_url_Usuario)
                                    <img src="{{ $item->imagen_url_Usuario }}"
                                         class="product-thumbnail"
                                         style="width:60px; height:60px; border-radius:6px;">
                                @else
                                    N/A
                                @endif
                            </td>

                            <td class="py-2 px-4 text-center">{{ $item->nombreCompleto }}</td>
                            <td class="py-2 px-4 text-center">{{ $item->nombre_usuario }}</td>
                            <td class="py-2 px-4 text-center">{{ $item->correo_usuario }}</td>
                            <td class="py-2 px-4 text-center">{{ $item->direccion_usuario }}</td>
                            <td class="py-2 px-4 text-center">{{ $item->telefono_usuario }}</td>
                            <td class="py-2 px-4 text-center">{{ $item->estado }}</td>
                            <td class="py-2 px-4 text-center">{{ $item->nombre_rol }}</td>
                            <td class="py-2 px-4 text-center">{{ $item->descripcion }}</td>

                            <td class="py-3 px-6 text-center align-middle">
                                <div class="flex justify-center items-center gap-2">

                                    {{-- EDITAR --}}
                                    <i data-lucide="square-pen"
                                        class="text-blue-600 hover:text-blue-800 cursor-pointer w-4 h-4"
                                        data-toggle="modal"
                                        data-target="#modalEditarUsuario"
                                        data-id="{{ $item->id_usuario }}"
                                        data-imagen_url="{{ $item->imagen_url_Usuario }}"
                                        data-nombre="{{ $item->nombreCompleto }}"
                                        data-nombreUsuario="{{ $item->nombre_usuario }}"
                                        data-correo="{{ $item->correo_usuario }}"
                                        data-direccion="{{ $item->direccion_usuario }}"
                                        data-telefono="{{ $item->telefono_usuario }}"
                                        data-estado="{{ $item->estado }}"
                                        data-rol="{{ $item->id_rol }}"
                                        data-contraseñaUser="{{ $item->contrasena_usuario }}"
                                        data-nombreRol="{{ $item->nombre_rol }}">
                                    </i>

                                    <span class="mx-1 text-gray-400">|</span>

                                    {{-- ELIMINAR --}}
                                    <a href="{{ route('usuarios.eliminar', $item->id_usuario) }}"
                                       onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                        <i data-lucide="trash-2"
                                            class="text-red-500 hover:text-red-700 cursor-pointer w-4 h-4"></i>
                                    </a>

                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-gray-500">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            {{-- EXPORTAR --}}
            <div class="mt-6">
                <button class="btn btn-success" onclick="exportarTablaAExcel()">Exportar Excel</button>
            </div>

        </div>

    </main>
</div>

{{-- MODALES --}}
@include('gestionUsuarios.partials.registrar_usuario')
@include('gestionUsuarios.partials.editar_usuario')
@include('layouts.mensajesModal')

@endsection

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>



<script>
function exportarTablaAExcel() {
    var tabla = document.getElementById("myTable");
    var hoja = XLSX.utils.table_to_sheet(tabla, { raw: true });
    var rango = XLSX.utils.decode_range(hoja['!ref']);

    for (let R = rango.s.r; R <= rango.e.r; ++R) {
        let celda = XLSX.utils.encode_cell({ r: R, c: rango.e.c });
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
