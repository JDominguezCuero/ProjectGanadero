<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Master Store')</title>

    {{-- CSS globales --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/principal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/detalleProducto.css') }}">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
</head>
<body class="min-h-screen flex bg-gray-100 contenedorBody @yield('body_class')">
    @yield('content')

    {{-- Scripts globales --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script> 
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('js/tienda_online.js') }}"></script>
    <script src="{{ asset('js/product_modal.js') }}"></script>
    <script src="{{ asset('js/notificaciones.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>

    <!-- <script src="https://www.powr.io/powr.js?platform=html"></script> -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>        
        AOS.init({
            duration: 1200,
        });

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
            } else {
                localStorage.setItem('darkMode', 'disabled');
            }
        }

        function applyDarkModeOnLoad() {
            if (localStorage.getItem('darkMode') === 'enabled') {
                document.body.classList.add('dark-mode');
            }
        }
        document.addEventListener('DOMContentLoaded', applyDarkModeOnLoad);

        // Lógica para manejar las pestañas (tabs)
        document.addEventListener('DOMContentLoaded', function() {
            const tabLinks = document.querySelectorAll('.hm-tab-link');
            const tabContents = document.querySelectorAll('.tabs-content');

            tabLinks.forEach(link => {
                link.addEventListener('click', function() {
                    tabLinks.forEach(item => item.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    this.classList.add('active');
                    const targetTabId = this.getAttribute('data-tab');
                    const targetContent = document.getElementById(targetTabId);
                    if (targetContent) {
                        targetContent.classList.add('active');
                    }
                    // Re-inicializar AOS para el contenido de la pestaña si es necesario
                    AOS.refresh();
                });
            });

            // Activar la primera pestaña por defecto al cargar la página si no hay una activa
            const activeTab = document.querySelector('.hm-tab-link.active');
            if (!activeTab && tabLinks.length > 0) {
                tabLinks[0].classList.add('active');
                const firstTabContent = document.getElementById(tabLinks[0].getAttribute('data-tab'));
                if (firstTabContent) {
                    firstTabContent.classList.add('active');
                }
            }
        });

        // Lógica para los carruseles horizontales
        document.addEventListener('DOMContentLoaded', function() {
            const carouselContainers = document.querySelectorAll('.carousel-container');

            carouselContainers.forEach(container => {
                const carouselTrack = container.querySelector('.carousel-track');
                const prevBtn = container.querySelector('.prev-btn');
                const nextBtn = container.querySelector('.next-btn');

                if (!carouselTrack || !prevBtn || !nextBtn) {
                    console.warn("Elementos del carrusel no encontrados en el contenedor:", container);
                    return; // Salir si los elementos no se encuentran
                }

                const productItem = carouselTrack.querySelector('.product-item');
                let scrollAmount = 300; // Valor por defecto

                // Intenta calcular el scrollAmount dinámicamente si hay un product-item
                if (productItem) {
                    const itemStyle = getComputedStyle(productItem);
                    const itemWidth = parseFloat(itemStyle.width);
                    const itemMarginRight = parseFloat(itemStyle.marginRight);
                    // O el gap si lo tienes definido con grid-gap
                    const gap = parseFloat(getComputedStyle(carouselTrack).gap || 0);

                    // Desplazar aproximadamente 3 elementos o un valor fijo si no se puede calcular
                    // Puedes ajustar '3' a la cantidad de elementos que quieres ver por scroll.
                    scrollAmount = (itemWidth + itemMarginRight + gap) * 3;
                    if (isNaN(scrollAmount) || scrollAmount === 0) {
                        scrollAmount = 300; // Fallback
                    }
                }

                nextBtn.addEventListener('click', () => {
                    carouselTrack.scrollBy({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                });

                prevBtn.addEventListener('click', () => {
                    carouselTrack.scrollBy({
                        left: -scrollAmount,
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
    
</body>
</html>
