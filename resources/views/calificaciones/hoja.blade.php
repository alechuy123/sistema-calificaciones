{{-- 
  Este es el Paso 3: La VISTA.
  Usa este archivo, ya que SÍ tienes un layout.
--}}

@extends('layouts.app') {{-- ¡CORREGIDO: 'layaout.app' en lugar de 'layouts.app'! --}}

@section('content')
<div class="container"> {{-- Asumiendo que tu layout no provee un 'container' --}}
    
    {{-- 1. EL CONTEXTO: Qué se está calificando --}}
    <div class="row mb-3">

        
        <div class="col">
            <h2>Hoja de Calificación</h2>
            <p class="lead">
                <strong>Grupo:</strong> {{ $grupo->nombre }} <br>
                <strong>Materia:</strong> {{ $materia->nombre }} <br>
                <strong>Unidad:</strong> {{ $unidad->nombre }}
            </p>
        </div>
        <div class="col-auto">
            {{-- Botón para regresar --}}
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                &larr; Volver
            </a>
        </div>
    </div>

    {{-- 2. LA TABLA DE CALIFICACIÓN --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="tabla-calificaciones">
            <thead class="table-light">
                <tr>
                    {{-- Columna Fija de Alumno --}}
                    <th style="min-width: 200px;">Alumno</th>
                    
                    {{-- 3. COLUMNAS DINÁMICAS: Los instrumentos --}}
                    @foreach ($instrumentos as $instrumento)
                        <th class="text-center" data-porcentaje="{{ $instrumento->porcentaje }}">
                            {{ $instrumento->nombre }}
                            <br>
                            <small>({{ $instrumento->porcentaje }}%)</small>
                        </th>
                    @endforeach
                    
                    {{-- Columna Fija de Promedio --}}
                    <th class="text-center" style="min-width: 100px;">Promedio Unidad</th>
                </tr>
            </thead>
            <tbody>
                {{-- 4. FILAS DINÁMICAS: Los alumnos --}}
                @forelse ($alumnos as $alumno)
                    <tr class="align-middle">
                        {{-- Nombre del Alumno --}}
                        <td>{{ $alumno->apellido_paterno }} {{ $alumno->apellido_materno }} {{ $alumno->nombre }}</td>

                        {{-- 5. CELDAS DE INPUT: El corazón de la app --}}
                        @foreach ($instrumentos as $instrumento)
                            @php
                                // Buscamos la calificación existente usando la llave "alumno_id-instrumento_id"
                                $key = $alumno->id . '-' . $instrumento->id;
                                $calificacion = $calificaciones->get($key);
                            @endphp
                            <td class="text-center">
                                <input 
                                    type="number" 
                                    class="form-control calificacion-input" 
                                    style="width: 80px; margin: 0 auto;"
                                    step="0.1" 
                                    min="0" 
                                    max="10" 
                                    
                                    {{-- Se rellena el valor si ya existe --}}
                                    value="{{ $calificacion->calificacion_obtenida ?? '' }}" 
                                    
                                    {{-- Estos data-atributos son vitales para el JS --}}
                                    data-alumno-id="{{ $alumno->id }}"
                                    data-instrumento-id="{{ $instrumento->id }}"
                                >
                            </td>
                        @endforeach
                        
                        {{-- Celda para el promedio (se calcula con JS) --}}
                        <td class="text-center fw-bold fs-5 align-middle" data-promedio-id="{{ $alumno->id }}">
                            --
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 2 + $instrumentos->count() }}" class="text-center">
                            No hay alumnos asignados a este grupo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 6. TOKEN CSRF (Tu layout 'layaout.app' ya lo tiene en el <head>) --}}
@endsection

@push('scripts')
{{-- 
  7. EL JAVASCRIPT. 
  Tu layout 'layaout.app' ya tiene el '@stack('scripts')', así que esto funcionará.
--}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Busca el token en el <head> (que tu layout debe tener)
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const urlGuardar = '{{ route("calificaciones.guardar.unidad") }}';
    const inputs = document.querySelectorAll('.calificacion-input');

    // Recalcular promedios al cargar la página
    recalcularTodosLosPromedios();

    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            // Cuando el usuario sale del input...
            const alumnoId = this.dataset.alumnoId;
            const instrumentoId = this.dataset.instrumentoId;
            let calificacion = this.value; // <--- MODIFICADO a 'let'
            const fila = this.closest('tr');

            // ==========================================================
            // --- ¡NUEVA VALIDACIÓN DE ENTRADA! ---
            // ==========================================================
            if (calificacion !== "") {
                let califNum = parseFloat(calificacion);
                
                // 1. Si es mayor a 10, se ajusta a 10
                if (califNum > 10) {
                    califNum = 10;
                    this.value = califNum; // Corregir el valor en la caja
                } 
                // 2. Si es menor a 0, se ajusta a 0
                else if (califNum < 0) {
                    califNum = 0;
                    this.value = califNum;
                }
                
                calificacion = califNum.toString(); // Usar el valor corregido para guardar
            }
            // --- FIN VALIDACIÓN ---


            // 1. Guardar la calificación
            guardarCalificacion(alumnoId, instrumentoId, calificacion, this);

            // 2. Recalcular el promedio de esa fila
            recalcularPromedioFila(fila);
        });
    });

    /**
     * Guarda la calificación en la base de datos vía Fetch.
     */
    async function guardarCalificacion(alumnoId, instrumentoId, calificacion, inputElement) {
        
        // Si la calificación está vacía, no guardamos, pero limpiamos el color
        if (calificacion === "") {
             inputElement.style.backgroundColor = '#FFFFFF'; // Blanco
             // NOTA: Aquí podrías llamar a una ruta "delete" si quisieras borrar
             // el registro de la BD. Por ahora, solo no lo guarda.
             return;
        }

        // Poner un feedback visual de "guardando"
        inputElement.style.backgroundColor = '#fff9c4'; // Amarillo

        try {
            const response = await fetch(urlGuardar, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    alumno_id: alumnoId,
                    instrumento_id: instrumentoId,
                    calificacion: calificacion
                })
            });

            const data = await response.json();

            // ==========================================================
            // --- ¡VALIDACIÓN MEJORADA! ---
            // ==========================================================
            // Si el backend (Laravel) rechaza la nota (ej. max:10), 
            // la respuesta no será 'ok'.
            if (!response.ok) {
                // Si la validación falla, data.message tendrá el error
                throw new Error(data.message || 'Error del servidor');
            }
            // --- FIN VALIDACIÓN ---

            if (data.success) {
                inputElement.style.backgroundColor = '#c8e6c9'; // Verde
            } else {
                // Esto ya no debería pasar si !response.ok funciona, pero es buena seguridad
                inputElement.style.backgroundColor = '#ffcdd2'; // Rojo
                console.error('Error al guardar:', data.message);
            }

        } catch (error) {
            inputElement.style.backgroundColor = '#ffcdd2'; // Rojo
            console.error('Error de red o validación:', error);
        }
    }

    /**
     * Recalcula el promedio de una fila de alumno específica.
     */
    function recalcularPromedioFila(fila) {
        const inputsFila = fila.querySelectorAll('.calificacion-input');
        const celdaPromedio = fila.querySelector('[data-promedio-id]');
        const ths = document.querySelectorAll('#tabla-calificaciones thead th[data-porcentaje]');
        
        let sumaPonderada = 0;
        let sumaPorcentajes = 0;

        inputsFila.forEach((input, index) => {
            const calif = parseFloat(input.value);
            const porcentaje = parseFloat(ths[index].dataset.porcentaje);

            if (!isNaN(calif) && !isNaN(porcentaje)) {
                sumaPonderada += (calif * (porcentaje / 100));
                sumaPorcentajes += (porcentaje / 100);
            }
        });

        if (sumaPorcentajes > 0) {
            // Promedio basado en lo que se ha calificado:
            let promedioFinal = sumaPonderada / sumaPorcentajes; // <--- MODIFICADO a 'let'
            
            // ==========================================================
            // --- ¡NUEVA VALIDACIÓN DE PROMEDIO! ---
            // ==========================================================
            if (promedioFinal > 10) {
                promedioFinal = 10;
            }
            // --- FIN VALIDACIÓN ---

            celdaPromedio.textContent = promedioFinal.toFixed(2);
        } else {
            celdaPromedio.textContent = '--';
        }
    }

    /**
     * Llama a recalcularPromedioFila para todas las filas al cargar la página.
     */
    function recalcularTodosLosPromedios() {
        const filas = document.querySelectorAll('#tabla-calificaciones tbody tr');
        filas.forEach(fila => {
            if (fila.querySelector('.calificacion-input')) {
                recalcularPromedioFila(fila);
            }
        });
    }
});
</script>
@endpush