{{-- resources/views/productos/lista.blade.php --}}
@extends('layouts.app')

@section('content')
<main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full mx-auto">
    <div class="hm-wrapper">

        @include('layouts.header')

        <div class="hm-page-block">
            <div class="container">
                <div class="header-title" data-aos="fade-up">
                    <h1>Catálogo de Productos</h1>
                </div>

                <div class="product-list-container">
                    {{-- FILTROS --}}
                    <aside class="filters-sidebar" data-aos="fade-right">
                        <h2>Filtros</h2>
                        <form action="{{ route('productos.index') }}" method="GET">
                            {{-- Categoría --}}
                            <div class="filter-group">
                                <label for="categoria">Categoría:</label>
                                <select name="categoria" id="categoria" onchange="this.form.submit()">
                                    <option value="">Todas las Categorías</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id }}"
                                            {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Buscar --}}
                            <div class="filter-group">
                                <label for="buscar">Buscar:</label>
                                <input type="text" name="buscar" id="buscar"
                                    placeholder="Nombre o descripción"
                                    value="{{ request('buscar') }}">
                            </div>

                            {{-- Precio --}}
                            <div class="filter-group">
                                <label>Precio:</label>
                                <input type="number" name="precio_min" placeholder="Mín."
                                    value="{{ request('precio_min') }}">
                                <input type="number" name="precio_max" placeholder="Máx."
                                    value="{{ request('precio_max') }}">
                            </div>

                            {{-- Ordenar --}}
                            <div class="filter-group">
                                <label for="ordenar_por">Ordenar por:</label>
                                <select name="ordenar_por" id="ordenar_por" onchange="this.form.submit()">
                                    <option value="fecha_reciente" {{ request('ordenar_por') == 'fecha_reciente' ? 'selected' : '' }}>Fecha (más reciente)</option>
                                    <option value="precio_asc" {{ request('ordenar_por') == 'precio_asc' ? 'selected' : '' }}>Precio (menor a mayor)</option>
                                    <option value="precio_desc" {{ request('ordenar_por') == 'precio_desc' ? 'selected' : '' }}>Precio (mayor a menor)</option>
                                    <option value="nombre_asc" {{ request('ordenar_por') == 'nombre_asc' ? 'selected' : '' }}>Nombre (A-Z)</option>
                                </select>
                            </div>

                            <button type="submit" class="hm-btn btn-primary uppercase mt-3">Aplicar Filtros</button>
                        </form>
                    </aside>

                    {{-- LISTA DE PRODUCTOS --}}
                    <main class="main-content" style="margin-top:auto; padding:5px;" data-aos="fade-left">
                        @if ($productos->count() > 0)
                            @foreach ($productosPorCategoria as $categoriaNombre => $productosCategoria)
                                @if ($productosCategoria->count() > 0)
                                    <div class="category-section mb-6">
                                        <h2 class="text-2xl font-bold mb-4">{{ $categoriaNombre }}</h2>
                                        <div class="products-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                            @foreach ($productosCategoria as $producto)
                                                <div class="product-card bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition">
                                                    <img src="{{ asset('storage/' . $producto->imagen) }}"
                                                        alt="{{ $producto->nombre }}" class="w-full h-48 object-cover">
                                                    <div class="p-4">
                                                        <h3 class="text-lg font-semibold">{{ $producto->nombre }}</h3>
                                                        <p class="text-gray-600 text-sm">{{ Str::limit($producto->descripcion, 80) }}</p>
                                                        <p class="text-green-700 font-bold mt-2">${{ number_format($producto->precio, 0, ',', '.') }}</p>
                                                        <a href="https://wa.me/{{ $producto->vendedor->telefono ?? '' }}?text=Hola,%20estoy%20interesado%20en%20{{ urlencode($producto->nombre) }}"
                                                            class="block text-center bg-green-600 text-white mt-3 py-2 rounded-lg hover:bg-green-700 transition">
                                                            Contactar con el vendedor
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p class="no-results text-center text-gray-500">No se encontraron productos que coincidan con los filtros aplicados.</p>
                        @endif
                    </main>
                </div>
            </div>
        </div>

        @include('layouts.footer')

    </div>
</main>
@endsection
