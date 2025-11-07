{{-- resources/views/nosotros.blade.php --}}
@extends('layouts.app')

@section('title', 'Nosotros | Sistema Ganadero')

@section('content')
<div class="flex min-h-screen w-full">

    {{-- Sidebar solo si hay usuario en sesión --}}
    @if(session('usuario'))
        @include('layouts.sidebar')
    @endif

    <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">
        <div class="hm-wrapper">

            {{-- Header --}}
            @include('layouts.header')

            <header>
                <div class="carousel">
                    <img src="{{ asset('images/fondo1.png') }}" alt="Ganadería 1">
                    <img src="{{ asset('images/fondo2.png') }}" alt="Ganadería 2">
                    <img src="{{ asset('images/fondo3.png') }}" alt="Ganadería 3">
                    <img src="{{ asset('images/fondo4.png') }}" alt="Ganadería 4">
                </div>
                <div class="header-content">
                    <h1>Sistema Ganadero Inteligente</h1>
                    <p>Administra tu finca, controla tus animales y simula tu producción de forma fácil y rápida</p>
                </div>
            </header>

            {{-- ¿Qué ofrece nuestro sistema? --}}
            <section class="max-w-6xl mx-auto py-12 px-4 bg-white rounded-xl shadow-lg mt-8" data-aos="fade-up">
                <h2 class="section-title">¿Qué ofrece nuestro sistema?</h2>
                <div class="features">
                    <div class="feature" data-aos="fade-right">
                        <h3>Registro de Animales</h3>
                        <p>Lleva un control detallado del inventario de animales en tu finca, facilitando la trazabilidad y la gestión individual de cada animal.</p>
                    </div>
                    <div class="feature" data-aos="fade-up">
                        <h3>Simulación de Producción</h3>
                        <p>Simula el crecimiento y productividad de tu ganado en diferentes escenarios, optimizando decisiones para maximizar rendimientos.</p>
                    </div>
                    <div class="feature" data-aos="fade-left">
                        <h3>Alertas y Recordatorios</h3>
                        <p>Recibe notificaciones automáticas para vacunaciones, alimentación, controles sanitarios y más, asegurando el bienestar de tu rebaño.</p>
                    </div>
                </div>
            </section>

            {{-- Propuesta de Valor --}}
            <section class="valor-section max-w-6xl mx-auto py-12 px-6 mt-8">
                <div data-aos="fade-right">
                    <img src="{{ asset('images/simulatorProfile.png') }}" alt="Mujeres trabajando felices con tablet e inventario">
                </div>
                <div class="valor-texto" data-aos="fade-left">
                    <h2>PROPUESTA <br> VALOR</h2>
                    <p><em>¡Aprende, gestiona y juega mientras transformas la ganadería!</em></p>
                    <p>Hemos desarrollado una plataforma educativa y administrativa innovadora, pensada para pequeños y medianos ganaderos, aprendices y estudiantes. Nuestro sistema web combina un simulador interactivo de cuidado animal, tipo juego, con un robusto sistema de inventario ganadero.</p>
                    <p>Podrás aprender sobre alimentación, salud y producción animal mientras administras insumos reales como alimentos, medicinas y herramientas. Automatiza procesos, mejora tus decisiones y conecta el aprendizaje con la práctica, todo desde una interfaz sencilla y funcional.</p>
                    <p>¡Haz de la educación ganadera una experiencia divertida y eficiente!</p>
                </div>
            </section>

            {{-- Estadísticas --}}
            <section class="max-w-6xl mx-auto py-12 px-4 bg-white rounded-xl shadow-lg mt-8" data-aos="fade-up">
                <h2 class="section-title">Estadísticas del sistema</h2>
                <div class="stats">
                    <div class="stat" data-aos="zoom-in" data-aos-delay="100">
                        <h3>+1,200</h3>
                        <p>Usuarios Registrados</p>
                    </div>
                    <div class="stat" data-aos="zoom-in" data-aos-delay="200">
                        <h3>+8,000</h3>
                        <p>Animales Registrados</p>
                    </div>
                    <div class="stat" data-aos="zoom-in" data-aos-delay="300">
                        <h3>+2,500</h3>
                        <p>Reportes Generados</p>
                    </div>
                </div>
            </section>

            {{-- Nuestro Equipo --}}
            <section class="team-section max-w-6xl mx-auto py-16 px-6 mt-8" data-aos="fade-up">
                <h2>Nuestro Equipo de Desarrollo</h2>
                <div class="team-members">
                    <div class="team-member-card" data-aos="fade-up" data-aos-delay="200">
                        <img src="{{ asset('images/equipo-miembro-2.jpg') }}" alt="Foto de Miembro del Equipo 2">
                        <h3>Juan Santos</h3>
                        <p class="role">Desarrolladora Frontend / Diseñador UX</p>
                        <p>Responsable de la interfaz de usuario, garantizando una experiencia intuitiva y visualmente atractiva para todos nuestros usuarios.</p>
                    </div>
                    <div class="team-member-card" data-aos="fade-up" data-aos-delay="100">
                        <img src="{{ asset('images/equipo-miembro-1.jpg') }}" alt="Foto de Miembro del Equipo 1">
                        <h3>José Domínguez</h3>
                        <p class="role">Líder de Proyecto / Desarrollador Backend</p>
                        <p>Encargado de la arquitectura de la base de datos y la lógica del servidor, asegurando un rendimiento óptimo y seguro de la plataforma.</p>
                    </div>
                    <div class="team-member-card" data-aos="fade-up" data-aos-delay="300">
                        <img src="{{ asset('images/equipo-miembro-3.jpg') }}" alt="Foto de Miembro del Equipo 3">
                        <h3>Jasbleidy Morales</h3>
                        <p class="role">Especialista en Simulación y Datos</p>
                        <p>Experto en el modelado de datos para la simulación ganadera y la integración de funcionalidades analíticas avanzadas del sistema.</p>
                    </div>
                </div>
            </section>

            {{-- Footer --}}
            @include('layouts.flooter')
        </div>
    </main>
</div>


@endsection
