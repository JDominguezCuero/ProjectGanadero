@extends('layouts.app')

@section('body_class', 'imagesFondo')

@section('title', 'Login y Register - Jose Domínguez Cuero')

@section('content')

{{-- Mostrar mensajes con showModal() --}}
@if(session('success') == 2)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal(
                '❌ Error al registrar',
                '{{ session("error") ?? "Error desconocido, contáctese con el administrador" }}',
                'error'
            );
        });
    </script>
@endif

@if(session('success') == 1)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('✅ Registro Exitoso', 'Usuario registrado correctamente', 'success');
        });
    </script>
@endif


{{-- Login fallido --}}
@if(session('login') == 1)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal(
                '❌ Acceso Denegado',
                '{{ session("error") }}',
                'error'
            );
        });
    </script>
@endif


{{-- Restablecimiento --}}
@if(session('enviado') == 2)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal(
                '❌ Acceso Denegado',
                '{{ session("error") ?? "Error desconocido, contacte al administrador" }}',
                'error'
            );
        });
    </script>
@endif

@if(session('enviado') == 1)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('✅ Envío Exitoso', 'Verifica tu correo electrónico.', 'success');
        });
    </script>
@endif


    <main>
        <div class="contenedor__todo">

            <div class="caja__trasera">
                <div class="caja__trasera-login">
                    <h3>¿Ya tienes una cuenta?</h3>
                    <p>Inicia sesión para entrar en la página</p>
                    <button id="btn__iniciar-sesion">Iniciar Sesión</button>
                </div>

                <div class="caja__trasera-register">
                    <h3>¿Aún no tienes una cuenta?</h3>
                    <p>Regístrate para que puedas iniciar sesión</p>
                    <button id="btn__registrarse">Regístrarse</button>
                </div>
            </div>

            <div class="contenedor__login-register">
                
                {{-- LOGIN --}}
                <form action="{{ route('auth.login') }}" method="POST" class="formulario__login">
                    @csrf
                    <h2>Iniciar Sesión</h2>

                    <input type="text" name="correoElectronicoLogin" placeholder="Correo Electronico" required>
                    <input type="password" name="contrasenaLogin" placeholder="Contraseña" required>

                    <button type="submit">Entrar</button>

                    <div class="login-options">
                        <br>
                        <a href="{{ route('home.index') }}" style="color: green;" class="btn-small">Volver</a>
                        <br>
                        <a href="#" data-toggle="modal" data-target="#modalRestablecer" style="color: green;" class="forgot-password">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </form>

                {{-- REGISTER --}}
                <form action="{{ url('/modules/auth/controller.php?accion=registro') }}" method="POST" class="formulario__register">
                    @csrf

                    <h2>Regístrarse</h2>

                    <input type="text" name="nombreCompleto" placeholder="Nombre completo" required>
                    <input type="text" name="correoElectronico" placeholder="Correo Electronico" required>
                    <input type="text" name="usuario" placeholder="Usuario" required>
                    <input type="password" name="contrasena" placeholder="Contraseña" required>

                    <button type="submit">Regístrarse</button>

                    <div class="login-options">
                        <br>
                        <a href="{{ route('home.index') }}" style="color: green;" class="btn-small">Volver</a>
                    </div>
                </form>

            </div>

        </div>

            <div class="modal fade" id="modalRestablecer" tabindex="-1" role="dialog"
            aria-labelledby="modalRestablecerLabel" aria-hidden="true"
            data-backdrop="static" data-keyboard="false">

            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    {{-- Encabezado del modal --}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalRestablecerLabel">Restablecer Contraseña</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    {{-- Cuerpo del modal --}}
                    <div class="modal-body">
                        <form action="{{ url('modules/auth/controller.php?accion=enviarEnlaceRestablecimiento') }}"
                            method="POST"
                            id="formRestablecer">
                            @csrf

                            <div class="form-group">
                                <label for="email">Correo electrónico:</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    name="email"
                                    id="email"
                                    placeholder="Ingresa tu correo"
                                    required>
                            </div>
                        </form>
                    </div>

                    {{-- Pie del modal --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                        <button type="submit"
                                class="btn btn-success"
                                form="formRestablecer">
                            Enviar enlace
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </main>




@include('layouts.mensajesModal')

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('public/assets/js/script.js') }}"></script>

@endsection
