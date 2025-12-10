<div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                {{-- Ajusta esta ruta con la correcta --}}
                <form action="{{ route('usuarios.editar') }}" method="POST" id="formEditarUsuario" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="id_usuario" id="editar_id_usuario">
                    <input type="hidden" name="imagen_url_actual" id="editar_imagen_url_actual">

                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="editar_nombreCompleto">Nombre Completo:</label>
                                <input type="text" class="form-control" name="nombreCompleto" id="editar_nombreCompleto" required>
                            </div>

                            <div class="form-group">
                                <label for="editar_nombre_usuario">Nombre de Usuario:</label>
                                <input type="text" class="form-control" name="nombre_usuario" id="editar_nombre_usuario" required>
                            </div>

                            <div class="form-group">
                                <label for="editar_correo_usuario">Correo Electrónico:</label>
                                <input type="email" class="form-control" name="correo_usuario" id="editar_correo_usuario" required>
                            </div>

                            <div class="form-group">
                                <label for="editar_contrasena">Cambiar Contraseña (opcional):</label>
                                <input type="password" class="form-control" name="contrasena" id="editar_contrasena">
                            </div>

                            <div class="form-group">
                                <label for="editar_rol_id">Rol:</label>
                                <select class="form-control" name="rol_id" id="editar_rol_id" required>
                                    <option value="">Selecciona un rol</option>

                                    @foreach ($roles as $rol)
                                        <option value="{{ $rol->id_rol }}">
                                            {{ $rol->nombre_rol }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="editar_direccion_usuario">Dirección:</label>
                                <input type="text" class="form-control" name="direccion_usuario" id="editar_direccion_usuario" required>
                            </div>

                            <div class="form-group">
                                <label for="editar_telefono_usuario">Teléfono:</label>
                                <input type="text" class="form-control" name="telefono_usuario" id="editar_telefono_usuario" required>
                            </div>

                            <div class="form-group">
                                <label for="editar_estado_usuario">Estado:</label>
                                <select class="form-control" name="estado" id="editar_estado_usuario" required>
                                    <option value="">Selecciona un estado</option>
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Imagen Actual:</label><br>
                                <img id="imagen_preview_usuario" src="" alt="Imagen actual"
                                     style="max-width: 150px; display:block; margin-bottom:10px;">

                                <label for="editar_imagen_usuario">Cargar Nueva Imagen:</label>
                                <input type="file" class="form-control-file" name="imagen" id="editar_imagen_usuario" accept="image/*">
                                <small class="form-text text-muted">Deja en blanco para mantener la imagen actual.</small>
                            </div>

                        </div>
                    </div>

                    
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success" form="formEditarUsuario">Guardar Cambios</button>
                </div>
            
            </form>
                
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    $('#modalEditarUsuario').on('show.bs.modal', function (event) {

        let button = $(event.relatedTarget);

        $('#editar_id_usuario').val(button.data('id'));
        $('#editar_nombreCompleto').val(button.data('nombre'));
        $('#editar_nombre_usuario').val(button.data('nombreusuario'));
        $('#editar_correo_usuario').val(button.data('correo'));
        $('#editar_direccion_usuario').val(button.data('direccion'));
        $('#editar_telefono_usuario').val(button.data('telefono'));
        $('#editar_rol_id').val(button.data('rol'));
        $('#editar_contrasena').val('');
        $('#editar_imagen_url_actual').val(button.data('imagen_url'));

        let estado = button.data('estado');
        let estadoValor = estado === 'Activo' ? '1' : estado === 'Inactivo' ? '2' : '';

        $('#editar_estado_usuario').val(estadoValor);

        let imagen = button.data('imagen_url');
        if (imagen) {
            $('#imagen_preview_usuario').attr('src', imagen).show();
        } else {
            $('#imagen_preview_usuario').hide();
        }
    });

});
</script>
