<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistema de Calificaciones - @yield('title', 'Inicio')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { background-color: #f8f9fa; }

        /* --- REGLAS DE VISIBILIDAD MANUALES --- */
        @media (min-width: 992px) {
            .ver-en-pc { display: flex !important; }
            .ver-en-movil { display: none !important; }
        }
        @media (max-width: 991.98px) {
            .ver-en-pc { display: none !important; }
            .ver-en-movil { display: block !important; }
        }

        /* --- ESTILOS AZULES (Modificados) --- */
        .navbar {
            background-color: #0d6efd !important; /* AZUL BOOTSTRAP */
        }

        /* Enlaces del menú */
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-size: 1.1rem;
            margin-left: 15px;
        }
        .nav-link:hover {
            color: #fff !important;
            text-decoration: underline;
        }

        /* Menú móvil fondo azul */
        #mobileMenu {
            background-color: #0d6efd !important; /* AZUL BOOTSTRAP */
        }
    </style>
</head>
<body>

    @auth
        <nav class="navbar navbar-dark bg-primary shadow-sm" style="padding: 1rem;">
            <div class="container d-flex justify-content-between align-items-center">

                <a class="navbar-brand fw-bold fs-4" href="{{ route('dashboard') }}" style="color: white !important;">
                    🎓 Sistema Calificaciones
                </a>

                <div class="ver-en-pc align-items-center">
                    <a class="nav-link" href="{{ route('materias.index') }}">Materias</a>
                    <a class="nav-link" href="{{ route('grupos.index') }}">Grupos</a>

                    <div class="ms-4 border-start ps-4 border-light">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-light btn-sm" type="submit">Cerrar Sesión</button>
                        </form>
                    </div>
                </div>

                <button class="navbar-toggler ver-en-movil" type="button" onclick="toggleMenu()" style="border: 1px solid white; padding: 5px 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="white" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </button>

            </div>
        </nav>

        <div id="mobileMenu" style="display: none;" class="text-white shadow-lg border-top border-light ver-en-movil">
            <ul class="nav flex-column p-4 text-center gap-3">
                <li class="nav-item"><a class="nav-link fs-5" href="{{ route('materias.index') }}">Materias</a></li>
                <li class="nav-item"><a class="nav-link fs-5" href="{{ route('grupos.index') }}">Grupos</a></li>
                <li class="nav-item pt-3 border-top border-light">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-light text-primary w-100 py-2 fw-bold" type="submit">Cerrar Sesión</button>
                    </form>
                </li>
            </ul>
        </div>

        <div class="container mt-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <strong>¡Éxito!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="bg-white p-4 rounded shadow-sm border">
                @yield('content')
            </div>
        </div>

    @else
        <main class="d-flex align-items-center min-vh-100 bg-gray-100">
            <div class="container">
                @yield('content')
            </div>
        </main>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleMenu() {
            var menu = document.getElementById('mobileMenu');
            menu.style.display = (menu.style.display === 'none') ? 'block' : 'none';
        }
    </script>

    @stack('scripts')

</body>
</html>
