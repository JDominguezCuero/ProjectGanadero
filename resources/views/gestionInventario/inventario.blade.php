<!-- inventario.blade.php -->
@extends('layouts.app')

@section('content')


{{-- Manejo de mensajes enviados por el controlador --}}
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('Operación Exitosa', @json(session('success')), 'success');
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('Error', @json(session('error')), 'error');
        });
    </script>
@endif

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let errores = @json($errors->all());
            showModal('Errores de Validación', errores.join('<br>'), 'error');
        });
    </script>
@endif

<div class="flex min-h-screen w-full">
    @if(session('usuario'))
        @include('layouts.sidebar')
    @endif

    <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Inventario de Alimentos</h1>
            <button class="btn btn-success" data-toggle="modal" data-target="#modalAgregarAlimento">Agregar Alimento</button>
        </div>

        <div style="position: relative;">
            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Buscar alimento..." title="Escribe un nombre" style="padding-left: 35px;">
            <i data-lucide="search" style="position: absolute; left: 8px; top: 40%; transform: translateY(-50%); width: 16px; height: 16px; color: gray;"></i>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden mt-4">
            <table id="myTable" class="min-w-full bg-white rounded-md shadow-sm overflow-hidden">
                <thead class="bg-blue-700 text-white">
                    <tr>
                        <th class="py-3 px-4 text-center w-[200px]">Nombre</th>
                        <th class="py-3 px-4 text-center w-[120px]">Cantidad</th>
                        <th class="py-3 px-4 text-center w-[150px]">Unidad de Medida</th>
                        <th class="py-3 px-4 text-center w-[180px]">Fecha de Ingreso</th>
                        <th class="py-3 px-4 text-center w-[120px]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-10">
                    @forelse ($inventario as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 text-center">{{ $item->nombre }}</td>
                            <td class="py-2 px-4 text-center">{{ $item->cantidad }}</td>
                            <td class="py-2 px-4 text-center">{{ $item->unidad_medida }}</td>
                            <td class="py-2 px-4 text-center">{{ $item->fecha_ingreso }}</td>
                            <td class="py-3 px-6 text-center align-middle">
                                <div class="flex justify-center items-center gap-2">
                                    <i data-lucide="square-pen" class="text-blue-600 hover:text-blue-800 cursor-pointer w-4 h-4"
                                        data-id="{{ $item->id_alimento }}"
                                        data-nombre="{{ $item->nombre }}"
                                        data-cantidad="{{ $item->cantidad }}"
                                        data-unidad_medida="{{ $item->unidad_medida }}"
                                        data-fecha_ingreso="{{ $item->fecha_ingreso }}">
                                    </i>

                                    <form action="{{ route('inventario.destroy', $item->id_alimento) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('¿Estás seguro que deseas eliminar este alimento del inventario?');">
                                            <i data-lucide="trash-2" class="text-red-600 hover:text-red-800 cursor-pointer w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">No hay alimentos registrados en el inventario.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <button class="btn btn-success" onclick="exportarTablaAExcel()">Exportar Excel</button>
        </div>

    </main>    

</div>    

@include('gestionInventario.partials.registrar_alimento')
@include('gestionInventario.partials.editar_alimento')
@include('layouts.mensajesModal')

<script>
// Solo un método para evitar conflictos
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Eventos para edición
    document.querySelectorAll('[data-lucide="square-pen"]').forEach(icon => {
        icon.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            let nombre = this.getAttribute('data-nombre');
            let cantidad = this.getAttribute('data-cantidad');
            let unidad = this.getAttribute('data-unidad_medida');
            let fecha = this.getAttribute('data-fecha_ingreso');

            console.log('Datos capturados:', { id, nombre, cantidad, unidad, fecha });

            // Llenar los campos del modal
            document.getElementById('editar_id_alimento').value = id || '';
            document.getElementById('editar_nombre').value = nombre || '';
            document.getElementById('editar_cantidad').value = cantidad || '';
            document.getElementById('editar_unidadMedida').value = unidad || '';
            document.getElementById('editar_fechaIngreso').value = fecha || '';

            // Actualizar acción del formulario
            if (id) {
                document.getElementById('formEditarAlimento').action = `/inventario/${id}`;
            }

            // Mostrar modal usando Bootstrap
            $('#modalEditarAlimento').modal('show');
        });
    });
});
</script>


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
@endsection
