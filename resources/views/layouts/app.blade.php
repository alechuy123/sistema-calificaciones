<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Sistema de Calificaciones - @yield('title', 'Inicio')</title>

    <!-- Estilos (Tailwind y Bootstrap) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-gray-100">

    {{-- Revisa si el usuario está autenticado --}}
    @auth
        <!-- SI ESTÁ AUTENTICADO, MUESTRA EL HEADER -->
        <header>
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container">
                    <a class="navbar-brand" href="{{ route('dashboard') }}">Sistema de Calificaciones</a>
                    <div class="collapse navbar-collapse">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('materias.index') }}">Materias</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('grupos.index') }}">Grupos</a>
                            </li>
                            <!-- Botón de Logout -->
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="nav-link" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); this.closest('form').submit();">
                                        Cerrar Sesión
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Y USA EL CONTENEDOR PRINCIPAL CON MARGEN -->
        <main class="container mt-4">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @yield('content')
        </main>

    @else
        <!-- SI ES INVITADO (NO LOGUEADO), NO MUESTRA EL HEADER -->
        <!-- Y usa un 'main' simple sin contenedor para el login/registro -->
        <main>
            @yield('content')
        </main>
    @endauth


    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ========================================================== -->
    <!-- --- ¡AQUÍ SE IMPRIMIRÁN LOS SCRIPTS DE OTRAS VISTAS! --- -->
    <!-- ========================================================== -->
    @stack('scripts')

</body>
</html>
