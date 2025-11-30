@extends('layouts.app')

{{-- @section('title', 'Información de ' . $materia->nombre) --}}

@section('content')

{{-- CSS PERSONALIZADO EN LÍNEA PARA ESTA VISTA --}}
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #304bc3 0%, #67a1ed 100%);
        --card-bg: #ffffff;
        --text-dark: #2d3748;
        --text-light: #718096;
        --accent-color: #203ec0;
    }

    body {
        background-color: #f7fafc; /* Fondo muy suave */
        font-family: 'Nunito', sans-serif;
    }

    /* Hero Section con Gradiente */
    .course-hero {
        background: var(--primary-gradient);
        color: white;
        padding: 3rem 2rem 5rem; /* Padding extra abajo para el efecto de superposición */
        border-radius: 0 0 50px 0;
        margin-bottom: -3rem;
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2);
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(5px);
        transition: all 0.3s ease;
    }
    .btn-back:hover {
        background: white;
        color: #764ba2;
        transform: translateX(-5px);
    }

    /* Tarjetas Modernas */
    .modern-card {
        background: var(--card-bg);
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }

    /* Animación de entrada */
    .fade-in-up {
        animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Estilos específicos para la Unidad */
    .unit-header {
        background: #fff;
        border-bottom: 1px solid #edf2f7;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .unit-badge {
        background: #e2e8f0;
        color: #4a5568;
        padding: 0.5em 1em;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .objective-box {
        background-color: #ebf4ff;
        border-left: 4px solid var(--accent-color);
        padding: 1.5rem;
        border-radius: 0 10px 10px 0;
        margin-bottom: 1.5rem;
    }

    /* Reemplazo de la Tabla por Lista Visual */
    .eval-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .eval-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 0.5rem;
        background: #f8f9fa;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .eval-item:hover {
        background: #fff;
        border-color: #e2e8f0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }

    .percentage-pill {
        background: var(--primary-gradient);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        min-width: 60px;
        text-align: center;
        font-size: 0.9rem;
    }

    /* Barra de estado total */
    .total-status {
        margin-top: 1rem;
        padding: 1rem;
        border-radius: 12px;
        text-align: center;
        font-weight: bold;
    }
    .status-ok { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .status-alert { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

</style>

{{-- SECCIÓN HERO (Encabezado Visual) --}}
<div class="course-hero">
    <div class="container">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <a href="{{ route('materias.index') }}" class="btn btn-sm btn-back rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
            <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill" style="opacity: 0.9;">
                <i class="fas fa-layer-group text-primary me-1"></i> {{ $materia->unidades->count() }} Unidades
            </span>
        </div>
        
        <h1 class="display-5 fw-bold mb-2">{{ $materia->nombre }}</h1>  
        {{-- <p class="opacity-75 fs-5 mb-0">Materia y estructura de evaluación</p> --}}
    </div>
</div>

<div class="container mt-4">
    
    {{-- TARJETA DE OBJETIVO GENERAL (Superpuesta al Hero) --}}
    <div class="modern-card fade-in-up" style="position: relative; z-index: 10;">
        <div class="card-body p-5 p-lg-6">
            <h5 class="text-uppercase text-secondary fw-bold fs-6 mb-3">
                <i class="fas fa-bullseye me-2 text-primary"></i>Objetivo del Curso
            </h5>
            <p class="fs-5 text-dark mb-0 leading-relaxed">
                {{ $materia->objetivo }}
            </p>
        </div>
    </div>

    {{-- LISTADO DE UNIDADES --}}
    @foreach ($materia->unidades as $unidad)
        <div class="modern-card fade-in-up" style="animation-delay: {{ $loop->iteration * 0.1 }}s">
            
            {{-- Encabezado de Unidad --}}
            <div class="unit-header">
                <h4 class="mb-0 fw-bold text-dark">
                    {{-- <span class="text-primary opacity-50 me-2">{{ $loop->iteration }}</span>  --}}
                    {{ $unidad->nombre }}
                </h4>
                
                @if ($unidad->fecha_inicio && $unidad->fecha_fin)
                    <div class="unit-badge">
                        <i class="far fa-calendar-alt text-primary"></i>
                        <span>{{ $unidad->fecha_inicio->format('d M') }} - {{ $unidad->fecha_fin->format('d M') }}</span>
                    </div>
                @endif
            </div>

            <div class="card-body p-4">
                
                {{-- Objetivo de Unidad --}}
                <div class="objective-box">
                    <strong class="d-block mb-2 text-primary">Objetivo de la unidad</strong>
                    <p class="text-muted mb-0">
                        {{ $unidad->objetivo ?? 'Aún no se ha definido el objetivo para esta unidad.' }}
                    </p>
                </div>

                {{-- Sección de Evaluación --}}
                <h6 class="fw-bold text-secondary text-uppercase fs-7 mb-3 mt-4">
                    <i class="fas fa-clipboard-list me-1"></i> Estructura de Evaluación
                </h6>

                @if ($unidad->instrumentos->isEmpty())
                    <div class="alert alert-light border-start border-warning border-4 text-muted">
                        <i class="fas fa-clock me-2 text-warning"></i> 
                        Evaluación pendiente de definir.
                    </div>
                @else
                    <div class="row g-4">
                        <div class="col-md-8">
                            <ul class="eval-list">
                                @php $totalPorcentaje = 0; @endphp
                                @foreach ($unidad->instrumentos as $instrumento)
                                    <li class="eval-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 text-primary">
                                                <i class="fas fa-check-circle fs-4 opacity-25"></i>
                                            </div>
                                            <span class="fw-bold text-dark">{{ $instrumento->nombre }}</span>
                                        </div>
                                        <span class="percentage-pill shadow-sm">{{ $instrumento->porcentaje }}%</span>
                                    </li>
                                    @php $totalPorcentaje += $instrumento->porcentaje; @endphp
                                @endforeach
                            </ul>
                        </div>
                        
                        {{-- Columna lateral con resumen --}}
                        <div class="col-md-4 d-flex flex-column justify-content-center">
                            <div class="total-status {{ $totalPorcentaje == 100 ? 'status-ok' : 'status-alert' }}">
                                <div class="fs-1 fw-bold mb-0">{{ $totalPorcentaje }}%</div>
                                <div class="small text-uppercase ls-1">Total</div>
                                @if($totalPorcentaje != 100)
                                    <div class="mt-2 text-xs">
                                        <i class="fas fa-exclamation-circle"></i> Debe sumar 100%
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
    
    <div class="text-center py-4 text-muted small">
        &copy; {{ date('Y') }} Sistema de Calificaciones
    </div>

</div>
@endsection