<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi App')</title>
    <link href="{{ asset('css/perfil.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen w-full">
        {{-- Si en el futuro tienes un sidebar, aquí lo incluyes --}}
        <div class="flex-1">
            @yield('content')
        </div>
    </div>
</body>
</html>
