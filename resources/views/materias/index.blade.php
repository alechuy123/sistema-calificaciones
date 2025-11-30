@extends('layouts.app')

@section('content')
<div class="container mt-4">
    {{-- Botón Volver --}}
    <a href="{{ url('/') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al Inicio
    </a>

    {{-- Encabezado con Título y Controles (Filtro + Botón Crear) --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Materias</h1>
        
        <div class="d-flex gap-2 align-items-center">
            {{-- Formulario de Filtro --}}
            <form action="{{ route('materias.index') }}" method="GET">
                <select name="filtro" class="form-select" onchange="this.form.submit()" style="width: 150px; cursor: pointer;">
                    <option value="todas" {{ request('filtro') == 'todas' ? 'selected' : '' }}>Todas</option>
                    <option value="activas" {{ request('filtro') == 'activas' ? 'selected' : '' }}>Activas</option>
                    <option value="desactivadas" {{ request('filtro') == 'desactivadas' ? 'selected' : '' }}>Desactivadas</option>
                </select>
            </form>

            {{-- Botón Crear --}}
            <a href="{{ route('materias.create') }}" class="btn btn-primary text-nowrap">
                <i class="fas fa-plus"></i> Registrar Nueva Materia
            </a>
        </div>
    </div>

    {{-- Mensajes de Éxito --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tabla de Resultados --}}
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
                            <th>Carreras</th>
                            <th style="width: 280px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($materias as $materia)
                            <tr>
                                {{-- <td>{{ $materia->id }}</td> --}}
                                <td>{{ $materia->nombre }}</td>

                                {{-- Estado --}}
                                <td>
                                    @if ($materia->esta_activo)
                                        <span class="badge bg-success">✅ Activa</span>
                                    @else
                                        <span class="badge bg-danger">❌ Desactivada</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-secondary">{{ $materia->unidades->count() }}</span>
                                </td>

                                {{-- Carreras --}}
                                <td>
                                    @forelse($materia->carreras as $carrera)
                                        <span class="badge bg-light text-dark border">
                                            {{ $carrera->nombre }}
                                        </span>
                                    @empty
                                        <span class="text-muted small">Sin asignación.</span>
                                    @endforelse
                                </td>

                                {{-- Botones de Acción --}}
                                <td>
    {{-- Envolvemos todo en un contenedor flexible con 'gap' (espacio) --}}
    <div class="d-flex flex-wrap gap-1">
        
        {{-- Botón Ver --}}
        <a href="{{ route('materia.publica.info', $materia->id) }}" 
           class="btn btn-sm" 
           style="background-color: #304bc3; border-color: #304bc3; color: white;" 
           title="Ver">
            <i class="fas fa-eye"></i> Ver
        </a>        
        
        {{-- Botón Evaluación --}}
        <a href="{{ route('evaluacion.show', $materia->id) }}" 
           class="btn btn-sm" 
           style="background-color: #67a1ed; border-color: #67a1ed; color: white;" 
           title="Configurar Evaluación">
            <i class="fas fa-tasks"></i> Evaluación
        </a>
        
        {{-- Botón Editar --}}
        <a href="{{ route('materias.edit', $materia->id) }}" class="btn btn-warning btn-sm" title="Editar">
            <i class="fas fa-edit"></i> Editar
        </a>

        {{-- Lógica Activar/Desactivar --}}
        @if ($materia->esta_activo)
            <form action="{{ route('materias.destroy', $materia->id) }}" method="POST" class="form-desactivar">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" title="Desactivar">
                    <i class="fas fa-trash-alt"></i> Desactivar
                </button>
            </form>
        @else
             {{-- Asegúrate de usar la ruta correcta de activar aquí --}}
             <form action="{{ route('materias.activar', $materia->id) }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success btn-sm" title="Activar">
                    <i class="fas fa-check"></i> Activar
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. Configuración para DESACTIVAR (Rojo/Warning)
        const formsDesactivar = document.querySelectorAll('.form-desactivar');
        formsDesactivar.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Detiene el envío
                
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
                        this.submit(); // Envía el formulario real
                    }
                });
            });
        });

        // 2. Configuración para ACTIVAR (Verde/Question)
        const formsActivar = document.querySelectorAll('.form-activar');
        formsActivar.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Detiene el envío
                
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
                        this.submit(); // Envía el formulario real
                    }
                });
            });
        });
    });
</script>
@endsection