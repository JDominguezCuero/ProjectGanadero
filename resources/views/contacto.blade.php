@extends('layouts.app')

@section('title', 'Contacto | Sistema Ganadero')

@section('content')
<main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">
    <div class="hm-wrapper">

        {{-- Header de la sección --}}
        <header class="text-center mb-10">
            <div>
                <img src="{{ asset('assets/images/contacto1.png') }}" alt="Ganadería" class="imagen-contacto mx-auto">
            </div>
            
            <div class="header-content mt-6">
                <h1 class="text-3xl font-bold">¿Necesitas ayuda? ¡Contáctanos!</h1>
                <p class="text-gray-600 mt-2">
                    Estamos aquí para resolver tus dudas, brindarte soporte o escucharte. Tu éxito es nuestra prioridad.
                </p>
            </div>
        </header>

        {{-- Sección de contacto --}}
        <section class="contact-modern" id="contact" data-aos="fade-up">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-semibold text-center mb-4">Contáctanos</h2>
                <p class="text-center text-gray-600 mb-8">
                    Estamos aquí para ayudarte. Envíanos un mensaje o encuéntranos en el mapa.
                </p>

                <div class="grid md:grid-cols-2 gap-10">
                    {{-- Formulario de contacto --}}
                    <div class="contact-form bg-white shadow-lg rounded-2xl p-6">
                        <form action="#" method="POST">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="name" class="font-semibold">Nombre completo</label>
                                <input type="text" id="name" name="name" class="w-full border rounded-lg p-2 mt-1" placeholder="Tu nombre" required>
                            </div>

                            <div class="form-group mb-4">
                                <label for="email" class="font-semibold">Correo electrónico</label>
                                <input type="email" id="email" name="email" class="w-full border rounded-lg p-2 mt-1" placeholder="tu.email@ejemplo.com" required>
                            </div>

                            <div class="form-group mb-4">
                                <label for="message" class="font-semibold">Tu mensaje</label>
                                <textarea id="message" name="message" rows="6" class="w-full border rounded-lg p-2 mt-1" placeholder="Escribe tu mensaje aquí..." required></textarea>
                            </div>

                            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg transition">
                                Enviar mensaje
                            </button>
                        </form>
                    </div>

                    {{-- Información de contacto y mapa --}}
                    <div class="contact-info-map">
                        <div class="contact-details mb-6 bg-white p-6 rounded-2xl shadow-lg">
                            <h3 class="text-lg font-bold mb-4">Nuestra Ubicación</h3>
                            <p class="mb-2"><i class="fas fa-map-marker-alt text-green-700 mr-2"></i> Carrera 11 # 28-51, Miradores de San Lorenzo, Puerto Boyacá, Boyacá, Colombia</p>
                            <p class="mb-2"><i class="fas fa-phone text-green-700 mr-2"></i> +57 320 633 9397</p>
                            <p><i class="fas fa-envelope text-green-700 mr-2"></i> info@sofwganadero.com</p>
                        </div>

                        <div class="map-container rounded-2xl overflow-hidden shadow-lg">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15878.71830601446!2d-74.6540307!3d6.0371306!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e40639903930b57%3A0xb30e38a4c803360!2sPuerto%20Boyac%C3%A1%2C%20Boyac%C3%A1%2C%20Colombia!5e0!3m2!1sen!2sco!4v1717286708764!5m2!1sen!2sco"
                                width="100%"
                                height="300"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</main>
@endsection
