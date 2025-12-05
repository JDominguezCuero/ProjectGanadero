{{-- resources/views/layouts/sidebar.blade.php --}}
@php
    // Obtener estado inicial desde la cookie (si existe)
    // request()->cookie() devuelve null si no existe
    $isSidebarCollapsed = request()->cookie('sidebar_collapsed') === 'true';

    $sidebarWidthClass = $isSidebarCollapsed ? 'w-20' : 'w-64';
    $userProfileHiddenClass = $isSidebarCollapsed ? 'hidden' : '';
    $textHiddenClass = $isSidebarCollapsed ? 'hidden' : '';
    $toggleIcon = $isSidebarCollapsed ? 'chevrons-right' : 'chevrons-left';

    // Sesión: usar valores por defecto si no existen
    $sessionUrl = session('url_Usuario') ? session('url_Usuario') : null;
    $sessionNombre = session('nombre', '');
    $sessionUsuario = session('usuario', '');
    $sessionRol = session('rol', null);
@endphp

<aside id="sidebar" class="bg-green-900 text-white {{ $sidebarWidthClass }} transition-all duration-300 flex flex-col p-4 h-full fixed top-0 left-0 h-screen overflow-y-auto z-50">
    <div class="flex justify-between items-center mb-6">
        <button id="toggleBtn" class="text-white hover:text-gray-200">
            <i data-lucide="{{ $toggleIcon }}"></i>
        </button>
    </div>

    <div id="userProfile" class="text-center {{ $userProfileHiddenClass }}">
        {{-- Si tienes URL absoluta en sesión úsala; si es ruta relativa, asset() ayuda --}}
        @if($sessionUrl)
            <img src="{{ $sessionUrl }}" class="w-20 h-20 rounded-full mx-auto mb-2" alt="Avatar usuario">
        @else
            <img src="{{ asset('images/c-4.png') }}" class="w-20 h-20 rounded-full mx-auto mb-2" alt="Avatar por defecto">
        @endif

        <h4 class="text-lg font-semibold" style="color:white;">{{ $sessionNombre }}</h4>
        <p class="text-sm text-gray-300">{{ $sessionUsuario }}</p>

        <a href="{{ url('perfil/listarUsuario') }}">
            <button class="bg-green-700 mt-4 px-4 py-2 rounded hover:bg-green-800 text-white">Editar</button>
        </a>
    </div>

    <nav class="mt-10 space-y-4">
        <a href="{{ route('home.index') }}" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="home"></i>
            <span id="textInicio" class="{{ $textHiddenClass }}">Inicio</span>
        </a>

        <a href="{{ route('notificaciones.index') }}" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="bell-dot"></i>
            <span id="textNotificacion" class="{{ $textHiddenClass }}">Notificaciones</span>
        </a>

        <a href="{{ route('productos.manage') }}" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="store"></i>
            <span id="textProducto" class="{{ $textHiddenClass }}">Productos</span>
        </a>

        <a href="{{ route('inventario.index') }}" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="box"></i>
            <span id="textInventario" class="{{ $textHiddenClass }}">Inventario</span>
        </a>

        <a href="{{ url('simulador/menuPrincipal') }}" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="activity"></i>
            <span id="textSimulacion" class="{{ $textHiddenClass }}">Simulación</span>
        </a>

        {{-- Menú administrador --}}
        @if ($sessionRol == 1)
            <a href="{{ url('auth/listar') }}" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
                <i data-lucide="user"></i>
                <span id="textPerfil" class="{{ $textHiddenClass }}">Administrar Usuarios</span>
            </a>
        @endif
    </nav>

    <div class="mt-auto pt-4">
        <a href="{{ route('auth.logout') }}" class="flex items-center space-x-2 hover:bg-red-700 p-2 rounded bg-red-600" style="color:white;">
            <i data-lucide="log-out"></i>
            <span id="textCerrarSesion" class="{{ $textHiddenClass }}">Cerrar Sesión</span>
        </a>
    </div>
</aside>

{{-- Script JS (prácticamente idéntico al original, pero con comprobaciones de existencia de elementos) --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar íconos Lucide si está cargado
    if (window.lucide && typeof lucide.createIcons === 'function') {
        lucide.createIcons();
    }

    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleBtn');
    const userProfile = document.getElementById('userProfile');
    const texts = ['textInicio', 'textProducto', 'textInventario', 'textSimulacion', 'textPerfil', 'textCerrarSesion', 'textNotificacion'];
    const mainContent = document.getElementById('mainContent');
    const mainHeader = document.getElementById('mainHeader');

    if (!sidebar) return; // si no hay sidebar, salimos

    function applyLayoutState(isCollapsed = true) {
        let sidebarWidth = 0;
        let marginLeftForContent = 0;

        const toggleIconSvg = toggleBtn ? toggleBtn.querySelector('svg') : null;

        if (isCollapsed) {
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-20');
            if (userProfile) userProfile.classList.add('hidden');
            texts.forEach(id => { const el = document.getElementById(id); if (el) el.classList.add('hidden'); });
            if (toggleIconSvg) toggleIconSvg.setAttribute('data-lucide', 'chevrons-right');
            sidebarWidth = 80; // px
        } else {
            sidebar.classList.remove('w-20');
            sidebar.classList.add('w-64');
            if (userProfile) userProfile.classList.remove('hidden');
            texts.forEach(id => { const el = document.getElementById(id); if (el) el.classList.remove('hidden'); });
            if (toggleIconSvg) toggleIconSvg.setAttribute('data-lucide', 'chevrons-left');
            sidebarWidth = 256; // px
        }

        marginLeftForContent = sidebarWidth;

        if (mainContent) {
            mainContent.style.marginLeft = `${marginLeftForContent}px`;
        }

        if (mainHeader) {
            mainHeader.style.left = `${marginLeftForContent}px`;
            mainHeader.style.width = `calc(100% - ${marginLeftForContent}px)`;
        }

        // Recrear íconos (si aplica) para que el cambio de data-lucide tenga efecto
        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }
    }

    // Lee cookie 'sidebar_collapsed' (sencillo)
    const cookieValue = (document.cookie.split('; ').find(row => row.startsWith('sidebar_collapsed=')) || '=false').split('=')[1];
    const isCollapsedInitial = cookieValue === 'true' || {{ $isSidebarCollapsed ? 'true' : 'false' }};
    applyLayoutState(isCollapsedInitial);

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const isCurrentlyCollapsed = sidebar.classList.contains('w-20');
            const newState = !isCurrentlyCollapsed;
            applyLayoutState(newState);

            // Guardar cookie (1 año)
            document.cookie = "sidebar_collapsed=" + newState + "; path=/; max-age=" + (365 * 24 * 60 * 60);
        });
    }
});
</script>
