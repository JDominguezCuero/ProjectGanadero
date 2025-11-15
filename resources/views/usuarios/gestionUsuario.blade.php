@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')

{{-- ========================= --}}
{{-- MENSAJES showModal() --}}
{{-- ========================= --}}
@if(session('inv') == 1 && session('error'))
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


<div class="min-h-screen flex bg-gray-100">

    <div class="flex min-h-screen w-full">

        {{-- SIDEBAR --}}
        @include('layout.sidebar')

        <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full">

            <div class="hm-wrapper">

                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h1>

                    <button class="btn btn-success"
                        data-toggle="modal"
                        data-target="#modalAgregarUsuario">
                        Agregar Nuevo Usuario
                    </button>
                </div>

                {{-- BUSCADOR --}}
                <div style="position: relative;">
                    <input
                        type="text"
                        id="myInput"
                        onkeyup="myFunction()"
                        placeholder="Buscar usuario..."
                        title="Escribe un nombre"
                        style="padding-left: 35px;">
                    <i data-lucide="search"
                        style="position: absolute; left: 8px; top: 40%; transform: translateY(-50%);
                        width: 16px; height: 16px; color: gray;">
                    </i>
                </div>

                {{-- TABLA --}}
                <div class="bg-white shadow-md rounded-lg mt-4" style="overflow-x: auto;">

                    <table id="myTable" class="min-w-full border border-gray-200 text-sm">
                        <thead class="bg-green-700 text-white">
                            <tr>
                                <th class="py-3 px-4 text-center">Imagen</th>
                                <th class="py-3 px-4 text-center">Nombre</th>
                                <th class="py-3 px-4 text-center">Usuario</th>
                                <th class="py-3 px-4 text-center">Correo</th>
                                <th class="py-3 px-4 text-center">Dirección</th>
                                <th class="py-3 px-4 text-center">Teléfono</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-center">Rol</th>
                                <th class="py-3 px-4 text-center">Descripción Rol</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-100">

                            @forelse($usuarios as $item)
                                <tr class="hover:bg-gray-50 transition-colors">

                                    {{-- IMAGEN --}}
                                    <td class="py-2 px-4 text-center">
                                        @if(!empty($item['imagen_url_Usuario']))
                                            <img src="{{ $item['imagen_url_Usuario'] }}"
                                                alt="Foto"
                                                class="product-thumbnail"
                                                style="width:55px; height:55px; border-radius:8px;">
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    <td class="py-2 px-4 text-center">{{ $item['nombreCompleto'] }}</td>
                                    <td class="py-2 px-4 text-center">{{ $item['nombre_usuario'] }}</td>
                                    <td class="py-2 px-4 text-center">{{ $item['correo_usuario'] }}</td>
                                    <td class="py-2 px-4 text-center">{{ $item['direccion_usuario'] }}</td>
                                    <td class="py-2 px-4 text-center">{{ $item['telefono_usuario'] }}</td>
                                    <td class="py-2 px-4 text-center">{{ $item['estado'] }}</td>
                                    <td class="py-2 px-4 text-center">{{ $item['nombre_rol'] }}</td>
                                    <td class="py-2 px-4 text-center">{{ $item['descripcion'] }}</td>

                                    {{-- ACCIONES --}}
                                    <td class="py-3 px-6 text-center align-middle">
                                        <div class="flex justify-center items-center gap-2">

                                            {{-- EDITAR --}}
                                            <i data-lucide="square-pen"
                                                class="text-blue-600 hover:text-blue-800 cursor-pointer w-4 h-4"
                                                data-toggle="modal"
                                                data-target="#modalEditarUsuario"

                                                data-id="{{ $item['id_usuario'] }}"
                                                data-imagen_url="{{ $item['imagen_url_Usuario'] }}"
                                                data-nombre="{{ $item['nombreCompleto'] }}"
                                                data-nombreUsuario="{{ $item['nombre_usuario'] }}"
                                                data-correo="{{ $item['correo_usuario'] }}"
                                                data-direccion="{{ $item['direccion_usuario'] }}"
                                                data-telefono="{{ $item['telefono_usuario'] }}"
                                                data-estado="{{ $item['estado'] }}"
                                                data-rol="{{ $item['id_rol'] }}"
                                                data-contraseñaUser="{{ $item['contrasena_usuario'] }}"
                                                data-nombreRol="{{ $item['nombre_rol'] }}">
                                            </i>

                                            <span class="mx-1 text-gray-400">|</span>

                                            {{-- ELIMINAR --}}
                                            <a href="{{ BASE_URL }}/modules/auth/controller.php?accion=eliminar&id={{ $item['id_usuario'] }}"
                                                onclick="return confirm('¿Estás seguro que deseas eliminar este usuario?');">
                                                <i data-lucide="trash-2"
                                                    class="text-red-500 hover:text-red-700 cursor-pointer w-4 h-4">
                                                </i>
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
                    <button class="btn btn-secondary" style="background-color: grey">
                        Exportar Excel
                    </button>
                </div>

            </div>

        </main>

    </div>

</div>

{{-- ========== INCLUDES BLADE ========= --}}
@include('layout.registrar_usuario')
@include('layout.editar_usuario')
@include('layout.mensajesModal')


{{-- ========== SCRIPTS ========= --}}
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script>
    lucide.createIcons();

    // Limpiar URL después del mensaje
    $(document).ready(function () {
        if(window.location.search.includes("msg=") || window.location.search.includes("error=")) {
            window.history.replaceState({}, document.title, window.location.pathname + "?accion=listar");
        }
    });

    // Buscador
    function myFunction() {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) {
            let showRow = false;
            const tds = tr[i].getElementsByTagName("td");
            for (j = 1; j < tds.length - 1; j++) {
                txtValue = tds[j].textContent || tds[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    showRow = true;
                    break;
                }
            }
            tr[i].style.display = showRow ? "" : "none";
        }
    }
</script>

@endsection
