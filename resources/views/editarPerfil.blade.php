{{-- resources/views/perfil.blade.php --}}
@extends('layouts.app')

@section('title', 'Perfil de Usuario | Sistema Ganadero')

@section('content')
<div class="flex min-h-screen w-full">

    @if(session('usuario'))
        @include('layouts.sidebar')
    @endif

    <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">

        {{-- Header --}}
        @include('layouts.header')

        @php
            // $userData debe ser pasado desde el controlador como arreglo/objeto
            $user = $userData ?? null;
            $profileImage = old('preview') ?: ($user['imagen_url_usuario'] ?? null);
            // Si no hay imagen, usar una por defecto pública en /public/images
            if (!$profileImage) {
                $profileImage = asset('images/profileDefault.png');
            } else {
                // Si la url guardada es relativa sin dominio, convertir a asset()
                if (!Str::startsWith($profileImage, ['http://','https://'])) {
                    $profileImage = asset(ltrim($profileImage, '/'));
                }
            }
        @endphp

        {{-- Mensajes desde sesión (error / message) y validación --}}
        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const mensaje = @json(session('error'));
                    if (typeof showModal === 'function') {
                        showModal('❌ Error', mensaje, 'error');
                    } else {
                        console.error('showModal no está definido — mensaje:', mensaje);
                    }
                });
            </script>
        @endif

        @if(session('message'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const mensaje = @json(session('message'));
                    if (typeof showModal === 'function') {
                        showModal('✅ Operación Exitosa', mensaje, 'success');
                    } else {
                        console.log('Operación exitosa:', mensaje);
                    }
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const errorsArray = @json($errors->all());
                    const errorsText = errorsArray.join(' ');
                    if (typeof showModal === 'function') {
                        showModal('❌ Errores de validación', errorsText || 'Error desconocido', 'error');
                    } else {
                        console.error('Errores de validación:', errorsText);
                    }
                });
            </script>
        @endif

        {{-- Si por alguna razón no se cargó el usuario, puedes mostrar un mensaje en blade --}}
        @if (!$user)
            <div class="max-w-3xl mx-auto p-6 bg-yellow-100 border-l-4 border-yellow-500 mb-6">
                <p class="text-yellow-800">No se pudo cargar la información del usuario.</p>
            </div>
        @endif

        <form class="datos" method="POST" action="{{ route('perfil.actualizar') }}" enctype="multipart/form-data">
            @csrf

            {{-- Si tu ruta usa otro nombre, ajusta route('perfil.actualizar') al de tu web.php --}}
            <input type="hidden" name="id_usuario" value="{{ old('id_usuario', $user['id_usuario'] ?? '') }}">
            <input type="hidden" name="imagen_url_actual" value="{{ old('imagen_url_actual', $user['imagen_url_usuario'] ?? '') }}">

            <div class="mb-8 p-6 bg-white rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-2">Foto de perfil</h2>
                <p class="text-gray-600 mb-4">Sube tu foto de perfil, un retrato en primer plano es ideal.</p>

                <div class="flex items-center space-x-4">
                    {{-- preview muestra old('preview') si hubo cambio en client, sino la imagen actual --}}
                    <img id="preview" src="{{ $profileImage }}" alt="Perfil" class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">
                    <div>
                        <button type="button" onclick="document.getElementById('fileFoto').click()" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">SUBIR FOTO DE PERFIL</button>
                        <input type="file" id="fileFoto" name="fileFoto" accept="image/*" style="display: none;" onchange="previewFoto(event)">
                        <input type="hidden" name="imagen_url_actual" value="{{ old('imagen_url_actual', $user['imagen_url_usuario'] ?? '') }}">
                        <small class="block text-gray-500 mt-1">*Mínimo 500 x 500px</small>
                    </div>
                </div>
            </div>

            <div class="section p-6 bg-white rounded-lg shadow-md mb-8">
                <h3 class="text-xl font-semibold mb-2">Datos de usuario</h3>
                <p class="text-gray-600 mb-4">Añade tus datos personales y de contacto.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="nombreCompleto" placeholder="Nombre completo" value="{{ old('nombreCompleto', $user['nombreCompleto'] ?? '') }}" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="text" name="nombre_usuario" placeholder="Nombre de usuario" value="{{ old('nombre_usuario', $user['nombre_usuario'] ?? '') }}" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="email" name="correo_usuario" placeholder="Correo electrónico" value="{{ old('correo_usuario', $user['correo_usuario'] ?? '') }}" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="tel" name="telefono_usuario" placeholder="Teléfono Móvil" value="{{ old('telefono_usuario', $user['telefono_usuario'] ?? '') }}" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="text" name="direccion_usuario" placeholder="Dirección" value="{{ old('direccion_usuario', $user['direccion_usuario'] ?? '') }}" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="password" name="contrasena" placeholder="Contraseña (dejar en blanco para no cambiar)" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

             <div class="section p-6 bg-white rounded-lg shadow-md mb-8">
                <h3 class="text-xl font-semibold mb-2">Redes sociales</h3>
                <p class="text-gray-600 mb-4">Añade tus redes sociales.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="url" name="facebook" placeholder="Facebook URL" value="{{ old('facebook', $user['facebook'] ?? '') }}" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" >
                    <input type="url" name="instagram" placeholder="Instagram URL" value="{{ old('instagram', $user['instagram'] ?? '') }}" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" >
                    <input type="url" name="whatsapp" placeholder="Whatsapp" value="{{ old('whatsapp', $user['whatsapp'] ?? '') }}" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" >
                </div>
            </div>

            <div class="section p-6 bg-white rounded-lg shadow-md mb-8">
                <h3 class="text-xl font-semibold mb-2">Ubicación principal</h3>
                <p class="text-gray-600 mb-4">Indica tu ubicación.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <select name="pais" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Colombia" selected>Colombia</option>
                    </select>

                    <select id="departamento" name="departamento" onchange="cargarMunicipios()" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecciona un departamento</option>
                    </select>

                    <select id="municipio" name="municipio" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecciona un municipio</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="w-full py-3 mt-6 bg-green-600 text-white font-bold rounded-md hover:bg-green-700 transition-colors">Guardar Cambios</button>
        </form>

        {{-- Mensajes modal partial (ajusta la ruta si la tienes en otra carpeta) --}}
        @includeIf('auth.mensajesModal')

    </main>
</div>

{{-- Scripts --}}
@section('scripts')
    
    <script>
        function previewFoto(event) {
            const reader = new FileReader();
            reader.onload = function () {
                document.getElementById('preview').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        let datosColombia = [];

        document.addEventListener("DOMContentLoaded", function () {
            // Ajusta la ruta si guardaste el JSON en public/js/colombia.json
            fetch("{{ asset('js/colombia.json') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    datosColombia = data;
                    cargarDepartamentos();
                })
                .catch(error => console.error('Error al cargar el JSON:', error));
        });

        function cargarDepartamentos() {
            const departamentoSelect = document.getElementById('departamento');
            departamentoSelect.innerHTML = '<option value="">Selecciona un departamento</option>';
            datosColombia.forEach(depto => {
                const option = document.createElement('option');
                option.value = depto.departamento;
                option.textContent = depto.departamento;
                departamentoSelect.appendChild(option);
            });

            // Si el usuario ya tiene departamento guardado, seleccionarlo
            const selectedDept = @json(old('departamento', $user['departamento'] ?? ''));
            if (selectedDept) {
                departamentoSelect.value = selectedDept;
                cargarMunicipios();
            }
        }

        function cargarMunicipios() {
            const departamento = document.getElementById('departamento').value;
            const municipioSelect = document.getElementById('municipio');
            municipioSelect.innerHTML = '<option value="">Selecciona un municipio</option>';

            const depto = datosColombia.find(d => d.departamento === departamento);
            if (depto) {
                depto.ciudades.forEach(muni => {
                    const option = document.createElement('option');
                    option.value = muni;
                    option.textContent = muni;
                    municipioSelect.appendChild(option);
                });

                // Seleccionar municipio previo si aplica
                const selectedMuni = @json(old('municipio', $user['municipio'] ?? ''));
                if (selectedMuni) {
                    municipioSelect.value = selectedMuni;
                }
            }
        }

        lucide.createIcons();
    </script>
@endsection

@endsection
