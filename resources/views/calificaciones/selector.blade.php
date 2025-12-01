@extends('layouts.app')

@section('content')

<style>
    /* Fondo con patrón suave */
    .bg-soft-main {
        background-color: #f8f9fa;
        background-image: radial-gradient(#e9ecef 1px, transparent 1px);
        background-size: 20px 20px;
    }

    .card-dashboard {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    /* --- CORRECCIÓN HEADER AZUL --- */
    .header-gradient {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        padding: 2.5rem 2rem;
        color: white;
        position: relative;
        /* Aseguramos que no haya flexbox raro alineando cosas invisibles */
        display: block;
    }

    /* ESTO BORRA EL CUADRO FANTASMA SI O SI */
    .header-gradient > div:not(.header-content-text) {
        display: none !important;
    }

    /* --- CORRECCIÓN BOTÓN VOLVER (Estilo Píldora) --- */
    .btn-back-pill {
        background-color: white;
        color: #495057;
        padding: 10px 25px;
        border-radius: 50px; /* Completamente redondo */
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05); /* Sombra suave */
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-back-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        color: #0d6efd; /* Azul al pasar el mouse */
    }

    /* Estilos Generales de Inputs */
    .custom-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .form-control-lg-custom {
        border: 2px solid #eaedf1;
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #fff;
    }

    .form-control-lg-custom:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }

    .form-control-lg-custom:disabled {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    /* Botón Grande de Acción */
    .btn-submit-custom {
        background-color: #0d6efd;
        color: white;
        border: none;
        padding: 15px;
        border-radius: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-submit-custom:hover:not(.disabled) {
        background-color: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2);
        color: white;
    }

    .btn-submit-custom.disabled {
        background-color: #e9ecef;
        color: #adb5bd;
        box-shadow: none;
    }
</style>

<div class="container-fluid bg-soft-main min-vh-100 d-flex flex-column">
    <div class="container py-5">

        {{-- BOTÓN VOLVER CORREGIDO --}}
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <a href="{{ route('materias.index') }}" class="btn-back-pill">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Listado</span>
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-dashboard">

                    {{-- HEADER LIMPIO --}}
                    {{-- Solo hay un div dentro. Si había otro antes, el CSS de arriba lo ocultará --}}
                    <div class="header-gradient">
                        <div class="header-content-text">
                            <h6 class="text-white-50 text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">
                                Configuración de Calificación
                            </h6>
                           {{-- Si existe materia, muestra el nombre. Si no, muestra un texto genérico --}}
<h2 class="mb-0 fw-bold">
    {{ $materia ? $materia->nombre : 'Selección General' }}
</h2>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5 bg-white">

                        <div class="row g-4">

                            {{-- SELECCIÓN DE GRUPO --}}
                            <div class="col-md-6">
                                <label for="select-grupo" class="custom-label">
                                    <i class="fas fa-users me-1 text-primary"></i> 1. Elige el Grupo
                                </label>
                                <select class="form-select form-control-lg-custom" id="select-grupo">
                                    <option value="">Seleccionar...</option>
                                    @forelse ($grupos as $grupo)
                                        <option value="{{ $grupo->id }}">
                                            {{ $grupo->nombre }}
                                            @if(isset($grupo->carrera))
                                                ({{ $grupo->carrera->nombre }})
                                            @endif
                                        </option>
                                    @empty
                                        <option value="" disabled>Sin grupos asignados</option>
                                    @endforelse
                                </select>
                            </div>

                            {{-- SELECCIÓN DE UNIDAD --}}
                            <div class="col-md-6">
                                <label for="select-unidad" class="custom-label d-flex justify-content-between">
                                    <span><i class="fas fa-layer-group me-1 text-primary"></i> 2. Elige la Unidad</span>
                                    <span id="loading-badge" class="badge bg-light text-primary d-none fw-normal">
                                        <i class="fas fa-sync fa-spin"></i>
                                    </span>
                                </label>
                                <select class="form-select form-control-lg-custom" id="select-unidad" disabled>
                                    <option value="">Esperando grupo...</option>
                                </select>
                            </div>

                        </div>

                        {{-- ALERTA --}}
                        <div id="no-units-alert" class="alert alert-warning border-0 bg-warning bg-opacity-10 mt-4 d-none">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-warning me-3 fs-4"></i>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">Atención</h6>
                                    <small class="text-dark opacity-75">Esta materia o grupo no tiene unidades configuradas.</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-5 border-light">

                        {{-- BOTÓN DE ACCIÓN --}}
                        <div class="text-center">
                            <a href="#" id="btn-calificar" class="btn btn-submit-custom w-100 disabled d-flex align-items-center justify-content-center">
                                <span class="me-2">IR A LA HOJA DE CALIFICACIÓN</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                            <p class="text-muted small mt-3 mb-0">
                                <i class="fas fa-lock me-1"></i> Selecciona ambos campos para continuar
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="materia-id" value="{{ $materia->id }}">
<input type="hidden" id="url-template" value="{{ route('calificaciones.hoja', ['grupo' => 'GRUPO_ID', 'materia' => 'MATERIA_ID', 'unidad' => 'UNIDAD_ID']) }}">
<input type="hidden" id="api-url" value="{{ route('api.materias.unidades', ':id') }}">

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const materiaId = document.getElementById('materia-id').value;
    const apiUrlTemplate = document.getElementById('api-url').value;
    const urlHojaTemplate = document.getElementById('url-template').value;
    const selectGrupo = document.getElementById('select-grupo');
    const selectUnidad = document.getElementById('select-unidad');
    const btnCalificar = document.getElementById('btn-calificar');
    const loadingBadge = document.getElementById('loading-badge');
    const noUnitsAlert = document.getElementById('no-units-alert');

    selectGrupo.addEventListener('change', async function() {
        const grupoId = this.value;
        selectUnidad.innerHTML = '<option value="">Cargando...</option>';
        selectUnidad.disabled = true;
        btnCalificar.classList.add('disabled');
        noUnitsAlert.classList.add('d-none');

        if (!grupoId) {
            selectUnidad.innerHTML = '<option value="">Esperando grupo...</option>';
            return;
        }

        loadingBadge.classList.remove('d-none');

        try {
            const url = apiUrlTemplate.replace(':id', materiaId);
            const response = await fetch(url);
            const unidades = await response.json();

            if (unidades.length > 0) {
                selectUnidad.innerHTML = '<option value="">-- Seleccionar --</option>';
                unidades.forEach(u => {
                    selectUnidad.innerHTML += `<option value="${u.id}">${u.nombre}</option>`;
                });
                selectUnidad.disabled = false;
                selectUnidad.focus();
            } else {
                selectUnidad.innerHTML = '<option value="">N/A</option>';
                noUnitsAlert.classList.remove('d-none');
            }
        } catch (error) {
            console.error(error);
            selectUnidad.innerHTML = '<option>Error</option>';
        } finally {
            loadingBadge.classList.add('d-none');
        }
    });

    selectUnidad.addEventListener('change', function() {
        const unidadId = this.value;
        const grupoId = selectGrupo.value;

        if (unidadId && grupoId) {
            let finalUrl = urlHojaTemplate
                .replace('GRUPO_ID', grupoId)
                .replace('MATERIA_ID', materiaId)
                .replace('UNIDAD_ID', unidadId);

            btnCalificar.href = finalUrl;
            btnCalificar.classList.remove('disabled');
        } else {
            btnCalificar.classList.add('disabled');
            btnCalificar.removeAttribute('href');
        }
    });
});
</script>
@endpush
