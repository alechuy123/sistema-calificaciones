@extends('layouts.app') 

@section('title', 'Información de ' . $materia->nombre)

@section('content')
<div class="container mt-4">
    <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a>

    <h2 class="mb-4">Información de la Materia: {{ $materia->nombre }}</h2>
    
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-bullseye"></i> Objetivo General</h5>
        </div>
        <div class="card-body">
            <p class="fs-5">{{ $materia->objetivo }}</p>
        </div>
    </div>
    
    <h3 class="mb-3">Unidades: {{ $materia->unidades->count() }} </h3>
    
    @foreach ($materia->unidades as $unidad)
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light">
                <h6>Unidad {{ $loop->iteration }}: {{ $unidad->nombre }}</h6>
                
                @if ($unidad->fecha_inicio && $unidad->fecha_fin)
                    <small class="text-muted">
                        <i class="fas fa-calendar-alt"></i> Periodo: <strong>{{ $unidad->fecha_inicio->format('d/m/Y') }}</strong> - <strong>{{ $unidad->fecha_fin->format('d/m/Y') }}</strong>
                    </small>
                @endif
            </div>

            <div class="card-body">
                
                <div class="mb-3" style="padding-left: 10px; border-left: 3px solid #0d6efd;">
                    <strong class="d-block mb-1"><i class="fas fa-bullseye text-primary"></i> Objetivo de la Unidad:</strong>
                    <p class="text-muted mb-0">
                        {{ $unidad->objetivo ?? 'El objetivo de esta unidad no ha sido definido.' }}
                    </p>
                </div>
                <hr>
                @if ($unidad->instrumentos->isEmpty())
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i> La estructura de evaluación de esta unidad aún no ha sido definida.
                    </div>
                @else
                    <table class="table table-sm table-bordered table-striped align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>Instrumento</th>
                                <th style="width: 150px;">Ponderación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalPorcentaje = 0;
                            @endphp
                            @foreach ($unidad->instrumentos as $instrumento)
                                <tr>
                                    <td>{{ $instrumento->nombre }}</td>
                                    <td>{{ $instrumento->porcentaje }}%</td>
                                </tr>
                                @php
                                    $totalPorcentaje += $instrumento->porcentaje;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold {{ $totalPorcentaje != 100 ? 'table-danger' : 'table-success' }}">
                                <td>TOTAL PONDERADO</td>
                                <td>{{ $totalPorcentaje }}%</td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        </div>
    @endforeach
    
    {{-- <a href="{{ route('materias.index') }}" class="btn btn-secondary mt-3" >
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a> --}}
</div>
@endsection