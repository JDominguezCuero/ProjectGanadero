{{-- Lógica de estado inicial (esto es lo más cercano a tu PHP inicial) --}}
@php
    // Obtener estado de la cookie (Laravel no accede fácilmente a cookies en la vista, 
    // pero podemos usar el helper request() o simplemente dejar que el JS lo maneje, 
    // como haces en tu código original, y pasar las clases iniciales)
    
    // Simplificamos la lógica de cookie para la inicialización HTML usando request()->cookie()
    $isSidebarCollapsed = request()->cookie('sidebar_collapsed') === 'true';

    $sidebarWidthClass = $isSidebarCollapsed ? 'w-20' : 'w-64';
    $userProfileHiddenClass = $isSidebarCollapsed ? 'hidden' : '';
    $textHiddenClass = $isSidebarCollapsed ? 'hidden' : ''; // Para los spans de texto
    $toggleIcon = $isSidebarCollapsed ? 'chevrons-right' : 'chevrons-left';
@endphp

<aside id="sidebar" class="bg-green-900 text-white {{ $sidebarWidthClass }} transition-all duration-300 flex flex-col p-4 h-full fixed top-0 left-0 h-screen overflow-y-auto z-50">
    <div class="flex justify-between items-center mb-6">
        <button id="toggleBtn" class="text-white hover:text-gray-200">
            <i data-lucide="{{ $toggleIcon }}"></i>
        </button>
    </div>
    <div id="userProfile" class="text-center {{ $userProfileHiddenClass }}">
        {{-- Acceder a variables de sesión con session('key') o Auth::user() --}}
        <img src="{{ session('url_Usuario') ? htmlspecialchars(session('url_Usuario')) : asset('images/default.png') }}" class="w-20 h-20 rounded-full mx-auto mb-2">
        <h4 class="text-lg font-semibold" style="color:white;">{{ session('nombre') }}</h4>
        <p class="text-sm text-gray-300">@{{ session('usuario') }}</p>
        
        {{-- Usar route() o url() para las URLs. Asumo que BASE_URL se reemplaza por url('/') --}}
        <a href="{{ url('perfil/listarUsuario') }}">
        <button class="bg-green-700 mt-4 px-4 py-2 rounded hover:bg-green-800" style="color:white;">Editar</button>
        </a>
    </div>

    <nav class="mt-10 space-y-4">
        <a href="{{ url('index') }}" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="home"></i>
            <span id="textInicio" class="{{ $textHiddenClass }}">Inicio</span>
        </a>
        <a href="{{ url('notificaciones/listar') }}" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="bell-dot"></i>
            <span id="textNotificacion" class="{{ $textHiddenClass }}">Notificaciones</span>
        </a>
        <a href="{{ url('productos/listar') }}" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="store"></i>
            <span id="textProducto" class="{{ $textHiddenClass }}">Productos</span>
        </a>
        <a href="{{ url('inventario/listar') }}" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="box"></i>
            <span id="textInventario" class="{{ $textHiddenClass }}">Inventario</span>
        </a>
        <a href="{{ url('simulador/menuPrincipal') }}" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="activity"></i>
            <span id="textSimulacion" class="{{ $textHiddenClass }}">Simulación</span>
        </a>
        
        {{-- Menú de Administrador: Reemplazamos la lógica PHP con Blade y asumimos que 'rol' está en la sesión --}}
        @if (session('rol') == 1)
            <a href="{{ url('auth/listar') }}" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
                <i data-lucide="user"></i>
                <span id="textPerfil" class="{{ $textHiddenClass }}">Administrar Usuarios</span>
            </a>
        @endif
    </nav>

    <div class="mt-auto pt-4">
        <a href="{{ url('inicio/logout') }}" class="flex items-center space-x-2 hover:bg-red-700 p-2 rounded bg-red-600" style="color:white;">
            <i data-lucide="log-out"></i>
            <span id="textCerrarSesion" class="{{ $textHiddenClass }}">Cerrar Sesión</span>
        </a>
    </div>
</aside>

{{-- El script JS se deja inalterado para mantener la funcionalidad de las cookies, solo se añade una nota --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();

        // **NOTA DE LARAVEL:** La inicialización de 'isSidebarCollapsedInitial' en JS 
        // a través de cookies funciona, pero si quieres usar el valor pre-calculado por Blade:
        // const isSidebarCollapsedInitial = {{ $isSidebarCollapsed ? 'true' : 'false' }};
        // Y aplicar `applyLayoutState(isSidebarCollapsedInitial);`
        
        // ... (El resto de tu código JavaScript se mantiene igual para el toggle y cookies) ...
    });
</script>