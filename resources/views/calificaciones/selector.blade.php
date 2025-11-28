@extends('layouts.app')

@section('content')

{{-- ESTILOS CSS PERSONALIZADOS PARA ESTA VISTA --}}
<style>
    .selector-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease;
    }
    .step-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #e9ecef;
        color: #495057;
        border-radius: 50%;
        margin-right: 15px;
        font-weight: bold;
    }
    .step-active .step-icon {
        background-color: #0d6efd;
        color: white;
    }
    .form-select-lg {
        border-radius: 10px;
        border: 1px solid #ced4da;
    }
    .form-select-lg:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .btn-action {
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }
    .btn-action:not(.disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>

<div class="container py-4">
    
    {{-- === BOTÓN DE VOLVER MEJORADO === --}}
    <div class="mb-4">
        <a href="{{ route('materias.index') }}" class="text-decoration-none text-secondary d-inline-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Volver al Listado de Materias
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card selector-card shadow-lg">
                {{-- HEADER CON GRADIENTE O COLOR SÓLIDO --}}
                <div class="card-header bg-primary text-white p-4" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <div class="d-flex align-items-center">
                        <div class="bg-white text-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-book-open fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-white-50">Proceso de Calificación</h5>
                            <h2 class="mb-0 fw-bold">{{ $materia->nombre }}</h2>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    <p class="text-muted mb-4">Complete los siguientes pasos para acceder a la hoja de calificaciones.</p>

                    {{-- 1. Selector de Grupo --}}
                    <div class="mb-4 step-active" id="step-1-container">
                        <label for="select-grupo" class="form-label fw-bold d-flex align-items-center mb-3">
                            <span class="step-icon">1</span> Selecciona el Grupo
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-users text-muted"></i></span>
                            <select class="form-select form-select-lg border-start-0 ps-0" id="select-grupo">
                                <option value="">-- Elige un grupo disponible --</option>
                                @forelse ($grupos as $grupo)
                                    <option value="{{ $grupo->id }}">{{ $grupo->nombre }} ({{ $grupo->carrera->nombre ?? 'General' }})</option>
                                @empty
                                    <option value="" disabled>No hay grupos asignados a esta materia</option>
                                @endforelse
                            </select>
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    {{-- 2. Selector de Unidad --}}
                    <div class="mb-4 opacity-50" id="step-2-container">
                        <label for="select-unidad" class="form-label fw-bold d-flex align-items-center mb-3">
                            <span class="step-icon">2</span> Selecciona la Unidad
                            <span id="loading-unidades" class="ms-3 d-none badge bg-info text-dark">
                                <i class="fas fa-spinner fa-spin"></i> Cargando...
                            </span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-list-ol text-muted"></i></span>
                            <select class="form-select form-select-lg border-start-0 ps-0" id="select-unidad" disabled>
                                <option value="">-- Primero selecciona un grupo --</option> 
                            </select>
                        </div>
                    </div>

                    {{-- 3. Botón de Acción --}}
                    <div class="d-grid mt-5">
                        <a href="#" id="btn-ir-a-calificar" class="btn btn-primary btn-lg btn-action disabled">
                            <i class="fas fa-edit me-2"></i> Ir a la Hoja de Calificación
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pasamos el ID de la materia al JS para usarlo --}}
<input type="hidden" id="materia-id-fijo" value="{{ $materia->id }}">

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const materiaId = document.getElementById('materia-id-fijo').value;
    const selectGrupo = document.getElementById('select-grupo');
    const selectUnidad = document.getElementById('select-unidad');
    const btnCalificar = document.getElementById('btn-ir-a-calificar');
    
    // Elementos visuales extra
    const step2Container = document.getElementById('step-2-container');
    const loadingBadge = document.getElementById('loading-unidades');

    // Rutas
    const urlGetUnidades = '{{ route("api.materias.unidades", ":id") }}'.replace(':id', materiaId);
    
    // IMPORTANTE: Verifica que esta ruta exista en tu archivo de rutas
    const urlBaseCalificar = '{{ route("calificaciones.hoja", ["grupo" => ":grupoId", "materia" => ":materiaId", "unidad" => ":unidadId"]) }}';

    // 1. Cargar Unidades automáticamente al iniciar
    cargarUnidades();

    async function cargarUnidades() {
        // Mostrar estado de carga
        loadingBadge.classList.remove('d-none');
        
        try {
            const response = await fetch(urlGetUnidades, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const unidades = await response.json();

            selectUnidad.innerHTML = '<option value="">-- Selecciona Unidad --</option>';
            unidades.forEach(u => {
                selectUnidad.innerHTML += `<option value="${u.id}">${u.nombre}</option>`;
            });
            
            // UX: Habilitar visualmente el paso 2
            step2Container.classList.remove('opacity-50');
            step2Container.classList.add('step-active');
            selectUnidad.disabled = false;
            
        } catch (error) {
            console.error(error);
            selectUnidad.innerHTML = '<option>Error al cargar unidades</option>';
        } finally {
            loadingBadge.classList.add('d-none');
        }
    }

    // 2. Validar selección para activar botón
    function validarFormulario() {
        const grupoId = selectGrupo.value;
        const unidadId = selectUnidad.value;

        if (grupoId && unidadId) {
            const urlFinal = urlBaseCalificar
                .replace(':grupoId', grupoId)
                .replace(':materiaId', materiaId)
                .replace(':unidadId', unidadId);
            
            btnCalificar.href = urlFinal;
            btnCalificar.classList.remove('disabled');
            // Efecto visual en el botón
            btnCalificar.classList.add('shadow');
        } else {
            btnCalificar.classList.add('disabled');
            btnCalificar.classList.remove('shadow');
        }
    }

    selectGrupo.addEventListener('change', validarFormulario);
    selectUnidad.addEventListener('change', validarFormulario);
});
</script>
@endpush