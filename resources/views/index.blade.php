{{-- resources/views/index.blade.php --}}
@extends('layouts.app') {{-- Si tienes un layout general, de lo contrario deja el html completo aquí --}}

@section('title', 'Home Master Store')

@section('content')
<div class="flex min-h-screen w-full">
    {{-- Sidebar solo si hay usuario en sesión --}}
    @if(session('usuario'))
        @include('assets.layout.sidebar')
    @endif

    @include('assets.layout.header')

    <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">   
        <div class="hm-wrapper">
            
            {{-- Header con imágenes --}}
            <header>
                <div class="carousel">
                    <img src="{{ asset('assets/images/index1.png') }}" alt="Ganadería 1">
                    <img src="{{ asset('assets/images/index2.png') }}" alt="Ganadería 2">
                    <img src="{{ asset('assets/images/index3.png') }}" alt="Ganadería 3">
                    <img src="{{ asset('assets/images/index4.png') }}" alt="Ganadería 4">
                </div>
                <div class="header-content">
                    <h1>Nuestros Productos del Campo</h1>
                    <p>Descubre y adquiere todo lo que necesitas para tu finca...</p>
                </div>
            </header>

            {{-- Categorías --}}
            <div class="hm-page-block">
                <div class="container">
                    <div class="header-title">
                        <h1 data-aos="fade-up" data-aos-duration="3000">Categorías</h1>
                    </div>

                    <div class="hm-grid-category">
                        <div class="grid-item" data-aos="fade-up" data-aos-duration="1000">
                            <a href="#">
                                <img src="{{ asset('assets/images/c-1.png') }}" alt="">
                                <div class="c-info">
                                    <h3>Todo en Productos Frescos</h3>
                                </div>
                            </a>
                        </div>
                        <div class="grid-item" data-aos="fade-up" data-aos-duration="1500">
                            <a href="#">
                                <img src="{{ asset('assets/images/c-2.png') }}" alt="">
                                <div class="c-info">
                                    <h3>Todo en Lácteos y Huevos</h3>
                                </div>
                            </a>
                        </div>
                        <div class="grid-item" data-aos="fade-up" data-aos-duration="2000">
                            <a href="#">
                                <img src="{{ asset('assets/images/c-3.png') }}" alt="">
                                <div class="c-info">
                                    <h3>Lo Mejor en Carnes y Embutidos</h3>
                                </div>
                            </a>
                        </div>
                        <div class="grid-item" data-aos="fade-up" data-aos-duration="2000">
                            <a href="#">
                                <img src="{{ asset('assets/images/c-4.png') }}" alt="">
                                <div class="c-info">
                                    <h3>Alimentos para Animales</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Todos los productos --}}
            <div class="hm-page-block all-products-section bg-fondo">
                <div class="container">
                    <div class="header-title" data-aos="fade-up">
                        <h1>Todos los Productos Publicados</h1>
                        <a href="{{ route('productos.index') }}" class="view-all-btn">Ver Todos <i class="las la-angle-right"></i></a>
                    </div>

                    <div class="carousel-container product-carousel-all" data-aos="fade-up">
                        <button class="carousel-btn prev-btn"><i class="las la-angle-left"></i></button>
                        <div class="carousel-track">
                            @if($todos_los_productos->isNotEmpty())
                                {{-- Muestra solo 10 --}}
                                @foreach($todos_los_productos->take(10) as $producto)
                                    @include('components.product-item', ['producto' => $producto])
                                @endforeach
                            @else
                                <p class="text-center">No hay productos disponibles en este momento.</p>
                            @endif
                        </div>
                        <button class="carousel-btn next-btn"><i class="las la-angle-right"></i></button>
                    </div>
                </div>
            </div>

            {{-- Productos populares --}}
            <div class="hm-page-block">
                <div class="container">
                    <div class="header-title" data-aos="fade-up">
                        <h1>Productos Populares</h1>
                    </div>

                    <ul class="hm-tabs" data-aos="fade-up">
                        @forelse($productos_populares_tabs as $categoria_nombre => $productos_list)
                            <li class="hm-tab-link {{ $loop->first ? 'active' : '' }}" data-tab="tab-{{ Str::slug($categoria_nombre) }}">
                                {{ $categoria_nombre }}
                            </li>
                        @empty
                            <li class="hm-tab-link active" data-tab="tab-default">Sin categorías populares</li>
                        @endforelse
                    </ul>

                    @forelse($productos_populares_tabs as $categoria_nombre => $productos_list)
                        <div class="tabs-content {{ $loop->first ? 'active' : '' }}" id="tab-{{ Str::slug($categoria_nombre) }}" data-aos="fade-up">
                            @if(count($productos_list))
                                <div class="carousel-container">
                                    <button class="carousel-btn prev-btn"><i class="las la-angle-left"></i></button>
                                    <div class="carousel-track">
                                        @foreach($productos_list as $producto)
                                            @include('components.product-item', ['producto' => $producto, 'showOldPrice' => ($categoria_nombre === 'En Oferta')])
                                        @endforeach
                                    </div>
                                    <button class="carousel-btn next-btn"><i class="las la-angle-right"></i></button>
                                </div>
                            @else
                                <p class="text-center">No hay productos disponibles en la categoría "{{ $categoria_nombre }}" en este momento.</p>
                            @endif
                        </div>
                    @empty
                        <div class="tabs-content active" id="tab-default" data-aos="fade-up">
                            <p class="text-center">No se encontraron productos populares ni categorías disponibles.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @include('assets.layout.flooter')
        </div>
    </main>
</div>

{{-- Modal detalle producto --}}
<div id="productDetailModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span> 
        <div id="modal-body-content">
            <div class="product-detail-loading">Cargando detalles del producto...</div>
        </div>
    </div>
</div>

@include('assets.layout.mensajesModal')

@endsection
