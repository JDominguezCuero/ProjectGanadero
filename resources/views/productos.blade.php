{{-- resources/views/productos.blade.php --}}
@extends('layouts.app')

@section('title', 'Productos | Sistema Ganadero')

@section('content')
<div class="flex min-h-screen w-full">

    {{-- Sidebar solo si hay usuario en sesión --}}
    @if(session('usuario'))
        @include('layouts.sidebar')
    @endif

    <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">  
        <div class="hm-wrapper">

            {{-- Encabezado --}}
            @include('layouts.header')

            <div class="hm-page-block">
                <div class="container">
                    <div class="header-title" data-aos="fade-up">
                        <h1>Catálogo de Productos</h1>
                    </div>

                    <div class="product-list-container">

                        {{-- Barra lateral de filtros --}}
                        <aside class="filters-sidebar" data-aos="fade-right">
                            <h2>Filtros</h2>

                            {{-- Formulario de filtros --}}
                            <form action="{{ route('productos.index') }}" method="GET"> 
                                <div class="filter-group">
                                    <label for="categoria">Categoría:</label>
                                    <select name="categoria" id="categoria" onchange="this.form.submit()">
                                        <option value="" {{ $filtros['filtro_categoria_id'] == '' ? 'selected' : '' }}>
                                            Todas las Categorías
                                        </option>

                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id_categoria }}"
                                                {{ ($filtros['filtro_categoria_id'] == $categoria->id_categoria) ? 'selected' : '' }}>
                                                {{ $categoria->nombre_categoria }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="filter-group">
                                    <label for="buscar">Buscar:</label>
                                    <input type="text" name="buscar" id="buscar"
                                        placeholder="Nombre o descripción"
                                        value="{{ $filtros['filtro_busqueda'] }}">
                                </div>

                                <div class="filter-group">
                                    <label>Precio:</label>
                                    <input type="number" name="precio_min" placeholder="Mín."
                                        value="{{ $filtros['filtro_precio_min'] }}">
                                    <input type="number" name="precio_max" placeholder="Máx."
                                        value="{{ $filtros['filtro_precio_max'] }}">
                                </div>

                                <div class="filter-group">
                                    <label for="ordenar_por">Ordenar por:</label>
                                    <select name="ordenar_por" id="ordenar_por" onchange="this.form.submit()">
                                        <option value="fecha_reciente" {{ $filtros['ordenar_por'] == 'fecha_reciente' ? 'selected' : '' }}>
                                            Fecha (más reciente)
                                        </option>
                                        <option value="precio_asc" {{ $filtros['ordenar_por'] == 'precio_asc' ? 'selected' : '' }}>
                                            Precio (menor a mayor)
                                        </option>
                                        <option value="precio_desc" {{ $filtros['ordenar_por'] == 'precio_desc' ? 'selected' : '' }}>
                                            Precio (mayor a menor)
                                        </option>
                                        <option value="nombre_asc" {{ $filtros['ordenar_por'] == 'nombre_asc' ? 'selected' : '' }}>
                                            Nombre (A-Z)
                                        </option>
                                    </select>
                                </div>

                                <button type="submit" class="hm-btn btn-primary uppercase">Aplicar Filtros</button>
                            </form>
                        </aside>

                        {{-- Listado de productos --}}
                        <main class="main-content" data-aos="fade-left">
                            @if ($productos_por_categoria->isNotEmpty())
                                @foreach ($productos_por_categoria as $nombre_categoria => $productos)
                                    @if ($productos->isNotEmpty())
                                        <div class="category-section">
                                            <h2>{{ $nombre_categoria }}</h2>
                                            <div class="products-grid">
                                                @include('layouts.partials_product_items', ['productos' => $productos])
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <p class="no-results">
                                    No se encontraron productos que coincidan con los filtros aplicados en ninguna categoría.
                                </p>
                            @endif
                        </main>

                    </div>
                </div>
            </div>

            {{-- Modal de detalle de producto --}}
            <div id="productDetailModal" class="modal">
                <div class="modal-content">
                    <span class="close-button">&times;</span>
                    <div id="modal-body-content">
                        <div class="product-detail-loading">Cargando detalles del producto...</div>
                    </div>
                </div>
            </div>
            
            {{-- Pie de página --}}
            @include('layouts.flooter')


            

        </div>
    </main>

</div>
@endsection
