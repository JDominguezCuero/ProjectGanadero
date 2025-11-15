{{-- resources/views/auth/restablecer.blade.php --}}
@extends('layouts.app')

@section('title', 'Restablecer contraseña | PROGAN')

@section('content')

@if(request('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(request('success') == 2)
                showModal(
                    '❌ Error al establecer',
                    "{{ request('error') ?? 'Error desconocido, contactese con el administrador' }}",
                    'error'
                );
            @elseif(request('success') == 1)
                showModal(
                    '✅ Restablecimiento Exitoso',
                    'Vuelve al login y accede con tus credenciales.',
                    'success'
                );
            @endif
        });
    </script>
@endif


<div class="container">
    <h2>Restablecer contraseña</h2>

    <form action="{{ url('modules/auth/controller.php?accion=restablecer') }}" method="POST" autocomplete="off">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <label for="contrasenaNueva">Nueva contraseña:</label>
        <input
            type="password"
            id="contrasenaNueva"
            name="contrasenaNueva"
            required
            minlength="8"
            placeholder="Mínimo 8 caracteres"
            autocomplete="new-password">

        <label for="confirmarContrasena">Confirmar contraseña:</label>
        <input
            type="password"
            id="confirmarContrasena"
            name="confirmarContrasena"
            required
            minlength="8"
            placeholder="Confirma tu nueva contraseña"
            autocomplete="new-password">

        <button type="submit">Restablecer contraseña</button>

        <div class="login-options mt-4">
            <a href="{{ url('modules/auth/views/autenticacion.php') }}" style="color: green;" class="btn-small">
                Volver
            </a>
        </div>
    </form>

    <p class="note mt-3">Recuerda usar una contraseña segura y que puedas recordar.</p>
</div>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

@include('layout.mensajesModal')

@endsection
