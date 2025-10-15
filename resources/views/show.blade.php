@extends('layouts.app')

@section('title', $producto->nombre_producto)

@section('content')
<div class="container mx-auto py-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        {{-- Imagen del producto --}}
        <div>
            <img src="{{ asset($producto->imagen_url) }}" 
                 alt="{{ $producto->nombre_producto }}" 
                 class="rounded-lg shadow-md w-full">
        </div>

        {{-- Información del producto --}}
        <div>
            <h1 class="text-3xl font-bold mb-4">{{ $producto->nombre_producto }}</h1>
            <p class="text-gray-600 mb-4">{{ $producto->descripcion_producto }}</p>

            <p class="mb-2">
                <strong>Categoría:</strong> {{ $producto->categoria->nombre_categoria ?? 'Sin categoría' }}
            </p>

            {{-- Precios --}}
            <div class="mb-4">
                @if($producto->precio_anterior && $producto->precio_anterior > $producto->precio_unitario)
                    <span class="line-through text-red-500 mr-2">
                        ${{ number_format($producto->precio_anterior, 2) }}
                    </span>
                @endif
                <span class="text-2xl font-semibold text-green-600">
                    ${{ number_format($producto->precio_unitario, 2) }}
                </span>
            </div>

            {{-- Botón comprar --}}
            <div>
                <button class="px-6 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600">
                    Agregar al carrito
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
