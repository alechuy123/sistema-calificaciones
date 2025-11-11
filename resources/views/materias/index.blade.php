@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <a href="{{ url('/') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al Inicio
    </a>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Gestión de Materias</h1>
        <a href="{{ route('materias.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Registrar Nueva Materia
        </a>
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
            No hay materias registradas. <a href="{{ route('materias.create') }}">¡Registra la primera!</a>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Unidades</th>
                            <th>Asignada a Carreras</th>
                            <th style="width: 280px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($materias as $materia)
                            <tr>
                                <td>{{ $materia->id }}</td>
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

                                <td>
                                    <a href="{{ route('materia.publica.info', $materia->id) }}" class="btn btn-info btn-sm" title="Ver">
                                        <i class="fas fa-eye">ver Materia</i>
                                    </a>
                                    
                                    <a href="{{ route('evaluacion.show', $materia->id) }}" class="btn btn-secondary btn-sm" title="Configurar Evaluación">
                                        <i class="fas fa-tasks">Editar Evaluación</i>
                                    </a>
                                    
                                    <a href="{{ route('materias.edit', $materia->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit">Editar</i>
                                    </a>

                                    @if ($materia->esta_activo)
                                        <form action="{{ route('materias.destroy', $materia->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Quieres DESACTIVAR esta materia?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Desactivar">
                                                <i class="fas fa-trash-alt">Desactivar</i>
                                            </button>
                                        </form>
                                    @else
                                         <a href="{{ route('materias.edit', $materia->id) }}" class="btn btn-success btn-sm" title="Reactivar (desde Editar)">
                                            <i class="fas fa-check">Activar</i>
                                         </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection