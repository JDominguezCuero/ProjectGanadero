<div class="modal fade" id="modalEditarAlimento" tabindex="-1" role="dialog"
     aria-labelledby="modalEditarAlimentoLabel" aria-hidden="true"
     data-backdrop="static" data-keyboard="false">

    <div class="modal-dialog" role="document">
        <div class="modal-content">

            {{-- Encabezado --}}
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarAlimentoLabel">Editar Alimento del Inventario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Cuerpo --}}
            <div class="modal-body">
                <form method="POST" id="formEditarAlimento">
                    @csrf
                    @method('PUT')

                    {{-- ID oculto --}}
                    <input type="hidden" name="id" id="editar_id_alimento">

                    {{-- Nombre --}}
                    <div class="form-group">
                        <label for="editar_nombre">Nombre del Alimento:</label>
                        <input type="text" class="form-control" name="nombre" id="editar_nombre" required>
                    </div>

                    {{-- Cantidad --}}
                    <div class="form-group">
                        <label for="editar_cantidad">Cantidad:</label>
                        <input type="number" class="form-control" name="cantidad"
                               id="editar_cantidad" min="0" step="1" required>
                    </div>

                    {{-- Unidad de medida --}}
                    <div class="form-group">
                        <label for="editar_unidadMedida">Unidad de Medida:</label>
                        <input type="text" class="form-control" name="unidad_medida" id="editar_unidadMedida" required>
                    </div>

                    {{-- Fecha ingreso --}}
                    <div class="form-group">
                        <label for="editar_fechaIngreso">Fecha de Ingreso:</label>
                        <input type="date" class="form-control" name="fecha_ingreso"
                               id="editar_fechaIngreso" required>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success" form="formEditarAlimento">Guardar Cambios</button>
            </div>

        </div>
    </div>

</div>


<script>
function editarAlimento(id, nombre, cantidad, unidad, fecha) {

    document.getElementById('editar_id_alimento').value = id;
    document.getElementById('editar_nombre').value = nombre;
    document.getElementById('editar_cantidad').value = cantidad;
    document.getElementById('editar_unidadMedida').value = unidad;
    document.getElementById('editar_fechaIngreso').value = fecha;

    // Cambiar dinámicamente la ruta del UPDATE
    document.getElementById('formEditarAlimento').action =
        `/inventario/${id}`;

    $('#modalEditarAlimento').modal('show');
}
</script>
