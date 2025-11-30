@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <a href="{{ url('/') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al Inicio
    </a>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Materias</h1>
        
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
                            {{-- AUMENTÉ EL ANCHO A 420px PARA QUE QUEPAN LOS 5 BOTONES CON TEXTO --}}
                            <th style="width: 420px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($materias as $materia)
                            <tr>
                                {{-- <td>{{ $materia->id }}</td> --}}
                                <td class="fw-bold">{{ $materia->nombre }}</td>
                                <td>
                                    @if($materia->esta_activo)
                                        <span class="badge bg-success">Activa</span>
                                    @else
                                        <span class="badge bg-secondary">Inactiva</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $materia->unidades->count() }} Unidades</span>
                                </td>
                                <td>
                                    @if($materia->carreras->isEmpty())
                                        <span class="text-muted fst-italic small">Sin asignar</span>
                                    @else
                                        @foreach($materia->carreras as $carrera)
                                            <span class="badge bg-secondary">{{ $carrera->nombre }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-end">
                                        
                                        {{-- 1. BOTÓN CALIFICAR (NUEVO) --}}
                                        {{-- Redirige al selector general, pre-seleccionando la materia si es posible --}}
                                        <a href="{{ route('calificaciones.selector', ['materia_id' => $materia->id]) }}" class="btn btn-sm btn-success" title="Calificar esta materia">
                                            <i class="fas fa-check-circle"></i> Calificar
                                        </a>

                                        {{-- 2. BOTÓN INFO PÚBLICA --}}
                                        <a href="{{ route('materia.publica.info', $materia->id) }}" class="btn btn-sm btn-info text-white" title="Ver Info Pública">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>

                                        {{-- 3. BOTÓN CONFIGURAR EVALUACIÓN --}}
                                        <a href="{{ route('evaluacion.show', $materia->id) }}" class="btn btn-sm btn-warning text-dark" title="Configurar Evaluación">
                                            <i class="fas fa-cogs"></i> Eval
                                        </a>

                                        {{-- 4. EDITAR --}}
                                        <a href="{{ route('materias.edit', $materia->id) }}" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>

                                        {{-- 5. BAJA / ALTA --}}
                                        @if($materia->esta_activo)
                                            <form action="{{ route('materias.destroy', $materia->id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de desactivar esta materia?')" title="Desactivar">
                                                    <i class="fas fa-ban"></i> Baja
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('materias.update', $materia->id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="nombre" value="{{ $materia->nombre }}">
                                                <input type="hidden" name="objetivo" value="{{ $materia->objetivo }}">
                                                <input type="hidden" name="esta_activo" value="1">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Reactivar materia?')" title="Reactivar">
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
@endsection
