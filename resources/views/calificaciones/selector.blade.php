{{-- 
  Esta es la NUEVA página "Selector" (con el selector de grupo habilitado).
  Coloca este archivo en: resources/views/calificaciones/selector.blade.php
--}}

@extends('layouts.app') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h2>Seleccionar Grupo para Calificar</h2>
                </div>

                <div class="card-body">
                    <p class="mb-3">Por favor, selecciona el grupo, materia y unidad que deseas calificar.</p>
                    
                    {{-- 1. Selector de Grupo --}}
                    <div class="mb-3">
                        <label for="select-grupo" class="form-label fw-bold">Paso 1: Selecciona un Grupo</label>
                        <select class="form-select form-select-lg" id="select-grupo">
                            <option value="">-- Elige un grupo --</option>
                            @foreach ($grupos as $grupo)
                                <option value="{{ $grupo->id }}">{{ $grupo->nombre }} ({{ $grupo->carrera->nombre }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Selector de Materia (Se rellena con JS) --}}
                    <div class="mb-3">
                        <label for="select-materia" class="form-label fw-bold">Paso 2: Selecciona una Materia</label>
                        <select class="form-select form-select-lg" id="select-materia" disabled>
                            <option value="">-- Esperando un grupo --</option>
                        </select>
                    </div>

                    {{-- 3. Selector de Unidad (Se rellena con JS) --}}
                    <div class="mb-3">
                        <label for="select-unidad" class="form-label fw-bold">Paso 3: Selecciona una Unidad</label>
                        <select class="form-select form-select-lg" id="select-unidad" disabled>
                            <option value="">-- Esperando una materia --</option>
                        </select>
                    </div>

                    {{-- 4. Botón de Acción (Se activa con JS) --}}
                    <div class="d-grid mt-4">
                        <a href="#" id="btn-ir-a-calificar" class="btn btn-primary btn-lg disabled" role="button">
                            Ir a Calificar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const selectGrupo = document.getElementById('select-grupo');
    const selectMateria = document.getElementById('select-materia');
    const selectUnidad = document.getElementById('select-unidad');
    const btnCalificar = document.getElementById('btn-ir-a-calificar');
    
    // CORRECCIÓN 1: Añadir el token CSRF para que el 'fetch' funcione
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // URLs de las rutas (¡asegúrate que no tengan 'http://localhost' al inicio!)
    const urlBaseGrupos = '{{ route("api.grupos.materias", ["grupo" => ":id"]) }}';
    const urlBaseMaterias = '{{ route("api.materias.unidades", ["materia" => ":id"]) }}';
    const urlBaseCalificar = '{{ route("calificaciones.hoja", ["grupo" => ":grupoId", "materia" => ":materiaId", "unidad" => ":unidadId"]) }}'; 

    
    // === CAMBIO 2 (HACER "INTELIGENTE" AL SELECTOR) ===
    // Revisa si la URL tiene un grupo_id (ej. ?grupo_id=1)
    const urlParams = new URLSearchParams(window.location.search);
    const grupoIdFromUrl = urlParams.get('grupo_id');

    if (grupoIdFromUrl) {
        // Si lo tiene, selecciónalo en el dropdown
        selectGrupo.value = grupoIdFromUrl;
        
        // *** CAMBIO CLAVE: QUITAR EL BLOQUEO (Solución solicitada) ***
        // El select se queda HABILITADO para permitir al usuario cambiar el grupo
        // Anteriormente aquí estaba: selectGrupo.disabled = true; 
        
        // CORRECCIÓN 3: Poner texto de "Cargando"
        selectMateria.innerHTML = '<option value="">-- Cargando materias... --</option>';
        selectMateria.disabled = false; // Habilitado para que se vea el "Cargando"

        // Versión CORREGIDA (Permite que el DOM se asiente antes de cargar)
        // Esperamos 10ms (es casi instantáneo) para asegurar que el valor del select esté completamente cargado
        setTimeout(() => {
            selectGrupo.dispatchEvent(new Event('change'));
        }, 10);
            }
            


    // --- Evento 1: Cambia el Grupo ---
    selectGrupo.addEventListener('change', async function() {
        const grupoId = this.value;
        resetSelect(selectMateria, '-- Esperando un grupo --');
        resetSelect(selectUnidad, '-- Esperando una materia --');
        btnCalificar.classList.add('disabled');

        if (!grupoId) return;

        // Si el "Paso 1" está ocurriendo (no por la URL), mostrar "Cargando"
        // O si viene de la URL y es la primera carga (sin el !grupoIdFromUrl)
        if (!grupoIdFromUrl || grupoIdFromUrl === grupoId) {
            selectMateria.innerHTML = '<option value="">-- Cargando materias... --</option>';
            selectMateria.disabled = false;
        }

        try {
            // Reemplaza el placeholder :id con el grupoId real
            const url = urlBaseGrupos.replace(':id', grupoId);
            
            // CORRECCIÓN 4: Añadir headers de autenticación al fetch
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' // <-- ESTA LÍNEA ES CLAVE
                }
            });

            if (!response.ok) throw new Error('Error al cargar materias');
            
            const materias = await response.json();
            
            selectMateria.innerHTML = '<option value="">-- Elige una materia --</option>'; // Limpiar
            materias.forEach(materia => {
                selectMateria.innerHTML += `<option value="${materia.id}">${materia.nombre}</option>`;
            });
            selectMateria.disabled = false;
        } catch (error) {
            console.error(error);
            resetSelect(selectMateria, 'Error al cargar materias');
        }
    });

    // --- Evento 2: Cambia la Materia ---
    selectMateria.addEventListener('change', async function() {
        const materiaId = this.value;
        resetSelect(selectUnidad, '-- Esperando una materia --');
        btnCalificar.classList.add('disabled');

        if (!materiaId) return;

        selectUnidad.innerHTML = '<option value="">-- Cargando unidades... --</option>';
        selectUnidad.disabled = false;

        try {
            // Reemplaza el placeholder :id con el materiaId real
            const url = urlBaseMaterias.replace(':id', materiaId);

            // CORRECCIÓN 5: Añadir headers de autenticación al fetch
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' // <-- ESTA LÍNEA ES CLAVE
                }
            });
            if (!response.ok) throw new Error('Error al cargar unidades');

            const unidades = await response.json();

            selectUnidad.innerHTML = '<option value="">-- Elige una unidad --</option>'; // Limpiar
            unidades.forEach(unidad => {
                selectUnidad.innerHTML += `<option value="${unidad.id}">${unidad.nombre}</option>`;
            });
            selectUnidad.disabled = false;
        } catch (error) {
            console.error(error);
            resetSelect(selectUnidad, 'Error al cargar unidades');
        }
    });

    // --- Evento 3: Cambia la Unidad ---
    selectUnidad.addEventListener('change', function() {
        const grupoId = selectGrupo.value;
        const materiaId = selectMateria.value;
        const unidadId = this.value;

        if (grupoId && materiaId && unidadId) {
            // Construir la URL final
            const urlFinal = urlBaseCalificar
                                .replace(':grupoId', grupoId)
                                .replace(':materiaId', materiaId)
                                .replace(':unidadId', unidadId);
            
            btnCalificar.href = urlFinal;
            btnCalificar.classList.remove('disabled');
        } else {
            btnCalificar.classList.add('disabled');
        }
    });

    // Función de utilidad
    function resetSelect(select, defaultText) {
        select.innerHTML = `<option value="">${defaultText}</option>`;
        select.disabled = true;
    }
});
</script>
@endpush