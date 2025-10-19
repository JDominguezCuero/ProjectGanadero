@php
    // La configuración de errores se maneja en Laravel en .env y config/app.php
    // Las variables de PHP se calculan con Blade/Laravel helpers
    $isUserLoggedIn = session('usuario'); // Usamos session() si no usas Auth para la sesión

    $headerClasses = "hm-header fixed top-0 z-40 transition-all duration-300";

    if (!$isUserLoggedIn) {
      $headerClasses .= " left-0 w-full";
    } else {
      $headerClasses .= " right-0"; 
    }
@endphp

<div id="mainHeader" class="{{ $headerClasses }}">
    <div class="container">
        <div class="header-menu">

            <div class="hm-logo">
                <a href="{{ url('index') }}">
                <img src="{{ asset('images/logo1.png') }}" alt=""> {{-- Usar asset() --}}
                </a>
            </div>

            <nav class="hm-menu">
                <ul>
                <li><a href="{{ url('index') }}">Productos</a></li>
                <li><a href="{{ url('productos') }}">Catalogo de Productos</a></li>
                <li><a href="{{ url('campanas') }}">Campañas</a></li>
                <li><a href="{{ url('nosotros') }}">Nosotros</a></li>
                <li><a href="{{ url('contacto') }}">Contacto</a></li>
                
                @if (!$isUserLoggedIn) {{-- Reemplazar !isset($_SESSION['usuario']) --}}
                    <li><a href="{{ url('auth/autenticacion') }}">Ingresar</a></li>
                @endif
                </ul>

                <div class="flex items-center space-x-4"> 
                    
                    <div class="hm-icon-cart">
                        <a href="#">
                            <i class="las la-shopping-cart"></i>
                            <span>0</span>
                        </a>
                    </div>
                    
                    <button class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center justify-center -mt-2" onclick="toggleDarkMode()">
                        <i class="las la-moon text-3xl text-gray-800 dark:text-gray-200"></i>
                    </button>

                    {{-- Botón y Panel de Notificaciones --}}
                    @if ($isUserLoggedIn)
                        <button class="notification-btn relative" onclick="showNotifications()">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center"></span>
                        </button>

                        <div id="notifications-panel" class="absolute right-5 top-14 bg-white rounded shadow-md w-80 z-50 hidden">
                            <div class="p-3 border-b font-bold text-green-800">Notificaciones</div>
                            <div id="notifications-container" class="p-3 max-h-80 overflow-y-auto">
                                </div>
                        </div>
                    @endif
                    
                    {{-- La ruta del script debe usar asset() --}}
                    <script src="{{ asset('js/notificaciones.js') }}"></script>

                    <div id="toast-notificacion" style=" /* ... (Estilos CSS) ... */ ">
                        ¡Notificación enviada al vendedor!
                    </div>
                    
                    <div class="icon-menu">
                        <button type="button"><i class="fas fa-bars"></i></button>
                    </div>
                </div> 
            </nav>

        </div>
    </div>
</div>

<div class="header-menu-movil">
    <button class="cerrar-menu"><i class="fas fa-times"></i></button>
    <ul>
    <li><a href="{{ url('index') }}">Productos</a></li>
    <li><a href="{{ url('productos') }}">Catalogo de Productos</a></li>
    <li><a href="{{ url('campanas') }}">Campañas</a></li>
    <li><a href="{{ url('nosotros') }}">Nosotros</a></li>
    <li><a href="{{ url('contacto') }}">Contacto</a></li>
        @if (!$isUserLoggedIn)
            <li><a href="{{ url('auth/autenticacion') }}">Ingresar</a></li>
        @endif
    </ul>
</div>