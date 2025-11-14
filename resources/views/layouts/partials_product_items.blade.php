{{-- resources/views/partials/product_items.blade.php --}}

@foreach ($productos as $product)
@php
    // Variables seguras
    $id_producto = $product->id_producto ?? '';
    $nombre_producto = $product->nombre_producto ?? 'Producto Desconocido';
    $descripcion_corta = Str::limit($product->descripcion_producto ?? '', 100, '...');
    $precio_unitario = number_format($product->precio_unitario ?? 0, 2);
    $precio_anterior = number_format($product->precio_anterior ?? 0, 2);

    $imagen_url = $product->imagen_url ?? 'placeholder.jpg';
    $estado_oferta = $product->estado_oferta ?? false;

    // Info del vendedor
    $nombre_usuario = $product->nombre_usuario ?? 'No disponible';
    $telefono_usuario = $product->telefono_usuario ?? '';
    $email_usuario = $product->correo_usuario ?? '';
    $direccion_usuario = $product->direccion_usuario ?? '';
    $id_usuario_vendedor = $product->id_usuario ?? '';

    // Determinar si es nuevo (últimos 30 días)
    $es_nuevo = false;
    if ($product->fecha_publicacion ?? false) {
        $diff = now()->diffInDays($product->fecha_publicacion);
        $es_nuevo = $diff <= 30;
    }
@endphp

<div class="product-item" data-product-id="{{ $id_producto }}">

    {{-- Imagen --}}
    <div class="p-portada">
        <img src="{{ $imagen_url }}" alt="{{ $nombre_producto }}">
        
        @if ($estado_oferta)
            <span class="stin stin-oferta">Oferta</span>
        @endif

        {{-- Etiqueta opcional "Nuevo" --}}
        {{-- @if ($es_nuevo)
            <span class="stin stin-new">Nuevo</span>
        @endif --}}
    </div>

    {{-- Información --}}
    <div class="p-info">
        <h3>{{ $nombre_producto }}</h3>

        <p class="descripcion">{{ $descripcion_corta }}</p>

        <div class="precio">
            <span>S/ {{ $precio_unitario }}</span>

            @if (!empty($product->precio_anterior) && $product->precio_anterior > $product->precio_unitario)
                <span class="thash">S/ {{ $precio_anterior }}</span>
            @endif
        </div>

        {{-- Información del vendedor --}}
        @if(!empty($product->nombre_usuario))
            <p class="seller-info">Vendido por: <strong>{{ $nombre_usuario }}</strong></p>
        @else
            <p class="seller-info">Vendedor no disponible</p>
        @endif

        {{-- Botón de contacto --}}
        <a  
            class="hm-btn btn-primary uppercase contactar-vendedor"
            data-id-vendedor="{{ $id_usuario_vendedor }}"
            data-id-producto="{{ $id_producto }}">
            Contactar Con El Vendedor
        </a>
    </div>

</div>

@endforeach
