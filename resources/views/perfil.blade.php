@extends('layouts.app')

@section('title', 'Perfil de Usuario')

@section('content')
<main id="mainContent">
    <form class="datos" action="{{ route('perfil.actualizar') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id_usuario" value="{{ $usuario->id_usuario ?? '' }}">
        <input type="hidden" name="imagen_url_actual" value="{{ $usuario->imagen_url_Usuario ?? asset('modules/auth/perfiles/profileDefault.png') }}">

        {{-- FOTO DE PERFIL --}}
        <div class="foto-perfil">
            <h2>Foto de perfil</h2>
            <p>Sube tu foto de perfil, un retrato en primer plano es ideal. No pongas un logo, queremos verte la cara.</p>

            <img id="preview" src="{{ $usuario->imagen_url_Usuario ?? asset('modules/auth/perfiles/profileDefault.png') }}" alt="Perfil">
            
            <button type="button" onclick="document.getElementById('fileFoto').click()">Subir foto de perfil</button>
            <input type="file" id="fileFoto" name="fileFoto" accept="image/*" style="display: none;" onchange="previewFoto(event)">
            <small>*Mínimo 500 x 500px</small>
        </div>

        {{-- DATOS DE USUARIO --}}
        <div class="section">
            <h3>Datos de usuario</h3>
            <p>Añade tus datos personales y de contacto.</p>
            <div class="form-grid">
                <input type="text" name="nombreCompleto" placeholder="Nombre completo" value="{{ $usuario->nombreCompleto ?? '' }}">
                <input type="text" name="nombre_usuario" placeholder="Nombre de usuario" value="{{ $usuario->nombre_usuario ?? '' }}">
                <input type="email" name="correo_usuario" placeholder="Correo electrónico" value="{{ $usuario->correo_usuario ?? '' }}">
                <input type="tel" name="telefono_usuario" placeholder="Teléfono Móvil" value="{{ $usuario->telefono_usuario ?? '' }}">
                <input type="text" name="direccion_usuario" placeholder="Dirección" value="{{ $usuario->direccion_usuario ?? '' }}">
                <input type="password" name="contrasena" placeholder="Contraseña (dejar en blanco para no cambiar)">
            </div>
        </div>

        {{-- REDES SOCIALES --}}
        <div class="section">
            <h3>Redes sociales</h3>
            <p>Añade tus redes sociales.</p>
            <div class="form-grid">
                <input type="url" name="facebook" placeholder="Facebook URL">
                <input type="url" name="instagram" placeholder="Instagram URL">
                <input type="url" name="whatsapp" placeholder="Whatsapp">
            </div>
        </div>

        {{-- UBICACIÓN --}}
        <div class="section">
            <h3>Ubicación principal</h3>
            <p>Indica tu ubicación.</p>
            <div class="form-grid">
                <select name="pais">
                    <option value="Colombia" selected>Colombia</option>
                </select>
                <select id="departamento" name="departamento" onchange="cargarMunicipios()">
                    <option value="">Selecciona un departamento</option>
                </select>
                <select id="municipio" name="municipio">
                    <option value="">Selecciona un municipio</option>
                </select>
            </div>
        </div>

        <button type="submit" class="submit-btn">Guardar Cambios</button>
    </form>
</main>

{{-- Scripts --}}
<script>
    function previewFoto(event) {
        const reader = new FileReader();
        reader.onload = function () {
            document.getElementById('preview').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
