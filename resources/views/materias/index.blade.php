@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <a href="{{ url('/') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al Inicio
    </a>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Gestión de Materias</h1>

        <div class="d-flex gap-2 align-items-center">
            <form action="{{ route('materias.index') }}" method="GET">
                <select name="filtro" class="form-select" onchange="this.form.submit()" style="width: 150px; cursor: pointer;">
                    <option value="todas" {{ request('filtro') == 'todas' ? 'selected' : '' }}>Todas</option>
                    <option value="activas" {{ request('filtro') == 'activas' ? 'selected' : '' }}>Activas</option>
                    <option value="desactivadas" {{ request('filtro') == 'desactivadas' ? 'selected' : '' }}>Desactivadas</option>
                </select>
            </form>

            <a href="{{ route('materias.create') }}" class="btn btn-primary text-nowrap">
                <i class="fas fa-plus"></i> Registrar Nueva Materia
            </a>
        </div>
    </div>

    {{-- Muestra el mensaje de éxito --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($materias->isEmpty())
        <div class="alert alert-info">
            @if(request('filtro') && request('filtro') != 'todas')
                No se encontraron materias con el filtro: <strong>{{ request('filtro') }}</strong>.
                <a href="{{ route('materias.index') }}">Ver todas</a>.
            @else
                No hay materias registradas. <a href="{{ route('materias.create') }}">¡Registra la primera!</a>
            @endif
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            {{-- <th>ID</th> --}}
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Unidades</th>
                            <th>Asignada a Carreras</th>
                            <th style="width: 420px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($materias as $materia)
                            <tr>
                                {{-- <td>{{ $materia->id }}</td> --}}
                                <td class="fw-bold">{{ $materia->nombre }}</td>

                                {{-- Estado --}}
                                <td>
                                    @if ($materia->esta_activo)
                                        <span class="badge bg-success">✅ Activa</span>
                                    @else
                                        <span class="badge bg-danger">❌ Desactivada</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">{{ $materia->unidades->count() }} Unidades</span>
                                </td>

                                {{-- Carreras --}}
                                <td>
                                    @if($materia->carreras->isEmpty())
                                        <span class="text-muted fst-italic small">Sin asignar</span>
                                    @else
                                        @foreach($materia->carreras as $carrera)
                                            <span class="badge bg-secondary">{{ $carrera->nombre }}</span>
                                        @endforeach
                                    @endif
                                </td>

                                {{-- ACCIONES --}}
                                <td>
                                    <div class="d-flex gap-1 justify-content-end">

                                        {{-- 1. CALIFICAR (CORREGIDO) --}}
                                        {{-- Se cambió route('calificaciones.selector', ...) por route('calificaciones.por_materia', ...) --}}
                                        <a href="{{ route('calificaciones.por_materia', $materia->id) }}" class="btn btn-sm btn-success" title="Calificar esta materia">
                                            <i class="fas fa-check-circle"></i> Calificar
                                        </a>

                                        {{-- 2. VER INFO --}}
                                        <a href="{{ route('materia.publica.info', $materia->id) }}" class="btn btn-sm btn-info text-white" title="Ver Info Pública">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>

                                        {{-- 3. EVALUACIÓN --}}
                                        <a href="{{ route('evaluacion.show', $materia->id) }}" class="btn btn-sm btn-warning text-dark" title="Configurar Evaluación">
                                            <i class="fas fa-cogs"></i> Eval
                                        </a>

                                        {{-- 4. EDITAR --}}
                                        <a href="{{ route('materias.edit', $materia->id) }}" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>

                                        {{-- 5. BAJA / ALTA --}}
                                        @if($materia->esta_activo)
                                            {{-- Formulario DESACTIVAR --}}
                                            <form action="{{ route('materias.destroy', $materia->id) }}" method="POST" class="d-inline-block form-desactivar">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Desactivar">
                                                    <i class="fas fa-trash-alt"></i> Baja
                                                </button>
                                            </form>
                                        @else
                                            {{-- Formulario ACTIVAR --}}
                                            <form action="{{ route('materias.update', $materia->id) }}" method="POST" class="d-inline-block form-activar">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="nombre" value="{{ $materia->nombre }}">
                                                <input type="hidden" name="objetivo" value="{{ $materia->objetivo }}">
                                                <input type="hidden" name="esta_activo" value="1">
                                                <button type="submit" class="btn btn-sm btn-success" title="Reactivar">
                                                    <i class="fas fa-check"></i> Alta
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // 1. Lógica para DESACTIVAR (Rojo)
        const formsDesactivar = document.querySelectorAll('.form-desactivar');
        formsDesactivar.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Detiene el envío automático

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta materia se desactivará y no será visible para los alumnos.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33', // Rojo
                    cancelButtonColor: '#3085d6', // Azul
                    confirmButtonText: 'Sí, desactivar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Envía el formulario real si se confirma
                    }
                });
            });
        });

        // 2. Lógica para ACTIVAR (Verde)
        const formsActivar = document.querySelectorAll('.form-activar');
        formsActivar.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Detiene el envío automático

                Swal.fire({
                    title: '¿Reactivar Materia?',
                    text: "La materia volverá a estar activa en el sistema.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745', // Verde
                    cancelButtonColor: '#6c757d', // Gris
                    confirmButtonText: 'Sí, activar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Envía el formulario real si se confirma
                    }
                });
            });
        });
    });
</script>
@endsection
