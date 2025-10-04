<div class="bot-icon-container">
    <a class="bot-activator" href="#">
        🤖
    </a>
    <span class="bot-tooltip-text">Chatea con Santos!</span>
</div>

<div class="chatbot-floating-container" id="chatbot-floating-container">
    {{-- ... Contenido del Chatbot ... --}}
</div>

<footer class="bg-gray-800 text-white py-4 mt-6">
    <div class="container">
        <div class="foo-row">
            {{-- ... Contenido del Footer ... --}}
            <div class="foo-col">
                <ul>
                    {{-- Rutas actualizadas con url() --}}
                    <li><a href="{{ url('index') }}">Productos</a></li>
                    <li><a href="{{ url('productos') }}">Catálogo de Productos</a></li>
                    <li><a href="{{ url('nosotros') }}">Nosotros</a></li>
                    <li><a href="{{ url('contacto') }}">Contacto</a></li>
                    <li><a href="{{ url('auth/autenticacion') }}">Ingresar</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<div class="foo-copy">
    <div class="container mx-auto text-center">
        <p>&copy; 2025 Simulador Ganadero. Todos los derechos reservados.</p>
        <p style="font-size: 11px;">José Domínguez Cuero - Jasbleidy Morales - Juan Santos.</p>
    </div>
</div>

{{-- Script del Chatbot con asset() --}}
<script src="{{ asset('modules/asistenteVirtual/views/js/chatbot.js') }}"></script>