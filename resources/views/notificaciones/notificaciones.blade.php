{{-- resources/views/notificaciones.blade.php --}}
@extends('layouts.app')

@section('title', 'Notificaciones | Sistema Ganadero')

@php
$notificaciones = $notificaciones ?? [];
@endphp

@section('content')


<div class="flex min-h-screen w-full">

    {{-- Sidebar igual que en productos --}}
    @if(session('usuario'))
        @include('layouts.sidebar')
    @endif

    <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full">

        <div class="hm-wrapper">

            {{-- Header global --}}
            @include('layouts.header')

            <div class="hm-page-block">
                <div class="container">

                    {{-- Título con estilo productos --}}
                    <div class="header-title" data-aos="fade-up">
                        <h1>Notificaciones</h1>
                    </div>

                    <div class="notifications-container">

                        <div class="notifications-list">
                            <h2>Tus Notificaciones</h2>

                            <div class="notifications-actions">
                                <button id="selectAllBtn">Seleccionar Todo</button>
                                <button id="deleteSelectedBtn" disabled>Eliminar Seleccionadas</button>
                                <button id="deleteAllBtn">Eliminar Todas</button>
                            </div>

                            <div id="notificationListContent">

                                @if (empty($notificaciones))
                                    <p class="no-notifications">No tienes notificaciones por el momento.</p>
                                @else
                                    @foreach ($notificaciones as $notificacion)

                                        @php
                                            $leidoClass = $notificacion->leido ? 'read' : 'unread';
                                            $imagenProducto = !empty($notificacion->imagen_url) ? $notificacion->imagen_url : 'placeholder.jpg';
                                            $nombreProducto = $notificacion->nombre_producto ?? 'Producto Desconocido';
                                            $precioProducto = isset($notificacion->precio_unitario)
                                                ? '$' . number_format($notificacion->precio_unitario, 0, ',', '.')
                                                : 'N/A';
                                            $emisorNombre = $notificacion->emisor_nombre ?? 'Usuario Desconocido';
                                        @endphp

                                        <div class="notification-item {{ $leidoClass }}"
                                        data-id="{{ $notificacion->id_notificacion }}"
                                        data-mensaje="{{ e($notificacion->mensaje) }}"
                                        data-fecha="{{ $notificacion->fecha }}"
                                        data-producto-nombre="{{ $nombreProducto }}"
                                        data-producto-descripcion="{{ e($notificacion->descripcion_producto ?? 'Sin descripción.') }}"
                                        data-producto-imagen="{{ $imagenProducto }}"
                                        data-producto-precio="{{ $precioProducto }}"
                                        data-producto-id="{{ $notificacion->id_producto ?? '' }}"
                                        data-id-usuario-emisor="{{ $notificacion->id_usuario_emisor ?? '' }}"
                                        data-emisor-nombre="{{ $emisorNombre }}"
                                        data-emisor-correo="{{ $notificacion->emisor_correo ?? 'N/A' }}"
                                        data-emisor-telefono="{{ $notificacion->emisor_telefono ?? 'N/A' }}"
                                        data-tipo-notificacion="{{ $notificacion->tipo_notificacion ?? 'interes' }}">

                                            <input type="checkbox" class="notification-checkbox">

                                            <div class="notification-content">
                                                <span class="notification-title">{{ $nombreProducto }} - Notificación</span>
                                                <span class="notification-date">{{ date('d/m/Y H:i', strtotime($notificacion->fecha)) }}</span>

                                                <p class="notification-preview">{{ \Illuminate\Support\Str::limit($notificacion->mensaje, 70) }}...</p>
                                            </div>

                                            <button class="delete-single-btn" data-id="{{ $notificacion->id_notificacion }}">X</button>
                                        </div>

                                    @endforeach
                                @endif

                            </div>
                        </div>

                        <div class="notification-detail">
                            <h2>Detalles de la Notificación</h2>

                            <div id="detailContent" class="no-selection">
                                <p>Selecciona una notificación para ver los detalles.</p>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            {{-- Footer global --}}
            @include('layouts.flooter')

        </div>

    </main>

</div>
@endsection
