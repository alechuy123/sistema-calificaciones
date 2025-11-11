@extends('layouts.app') 

@section('content')
<div class="container mt-4">
    <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a>

    <h2 class="mb-3"><i class="fas fa-tasks"></i> Configuración de Evaluación para: {{ $materia->nombre }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('evaluacion.store', $materia) }}" method="POST">
        @csrf

        @foreach ($materia->unidades as $unidad)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Unidad {{ $loop->iteration }}: {{ $unidad->nombre }}</h4>
                    <span class="fs-5">Suma: <strong id="total-unidad-{{ $unidad->id }}" class="text-danger">0%</strong></span>
                </div>
                
                <div class="card-body">
                    
                <h5 style="margin-bottom: 2.5rem;">Periodo de la Unidad</h5>
                <div class="row g-3 p-3 bg-light border rounded mb-4">

                    <div class="col-12">
                        <label for="objetivo_{{ $unidad->id }}" class="form-label">Objetivo de la Unidad:</label>
                        <textarea id="objetivo_{{ $unidad->id }}"
                                name="unidades[{{ $unidad->id }}][objetivo]"
                                class="form-control"
                                rows="2"
                                placeholder="Escribe el objetivo de aprendizaje para esta unidad..."
                        >{{ old('unidades.'.$unidad->id.'.objetivo', $unidad->objetivo) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_inicio_{{ $unidad->id }}" class="form-label">Fecha de Inicio:</label>
                        <input type="date"
                            id="fecha_inicio_{{ $unidad->id }}"
                            name="unidades[{{ $unidad->id }}][fecha_inicio]"
                            class="form-control"
                            value="{{ old('unidades.'.$unidad->id.'.fecha_inicio', $unidad->fecha_inicio?->format('Y-m-d')) }}"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_fin_{{ $unidad->id }}" class="form-label">Fecha de Fin:</label>
                        <input type="date"
                            id="fecha_fin_{{ $unidad->id }}"
                            name="unidades[{{ $unidad->id }}][fecha_fin]"
                            class="form-control"
                            value="{{ old('unidades.'.$unidad->id.'.fecha_fin', $unidad->fecha_fin?->format('Y-m-d')) }}"
                            required>
                    </div>
                </div>

                    <h5 class="mt-3">Instrumentos de Evaluación</h5>
                    <div id="instrumentos-container-{{ $unidad->id }}">
                        
                        @php $instrumentoIndex = 0; @endphp
                        
                        @foreach ($unidad->instrumentos as $instrumento)
                            <div class="input-group mb-2 instrumento-item" data-unidad="{{ $unidad->id }}" data-evaluacion-id="{{ $instrumento->id }}">
                                <span class="input-group-text">Nombre:</span>
                                <input type="hidden" name="unidades[{{ $unidad->id }}][instrumentos][{{ $instrumentoIndex }}][id]" value="{{ $instrumento->id }}">
                                <input type="text" 
                                       class="form-control" 
                                       name="unidades[{{ $unidad->id }}][instrumentos][{{ $instrumentoIndex }}][nombre]" 
                                       placeholder="Ej: Examen Parcial" 
                                       value="{{ old('unidades.'.$unidad->id.'.instrumentos.'.$instrumentoIndex.'.nombre', $instrumento->nombre) }}" 
                                       required>
                                <span class="input-group-text">Peso (%):</span>
                                <input type="number" 
                                       class="form-control input-porcentaje" 
                                       name="unidades[{{ $unidad->id }}][instrumentos][{{ $instrumentoIndex }}][porcentaje]" 
                                       placeholder="%" 
                                       value="{{ old('unidades.'.$unidad->id.'.instrumentos.'.$instrumentoIndex.'.porcentaje', $instrumento->porcentaje) }}"
                                       min="1" max="100" required style="max-width: 100px;">
                                <button type="button" class="btn btn-outline-danger remove-instrumento">X</button>
                            </div>
                            @php $instrumentoIndex++; @endphp
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary add-instrumento mt-2" data-unidad-id="{{ $unidad->id }}">
                        <i class="fas fa-plus"></i> Agregar Instrumento
                    </button>
                    
                </div>
                
                <input type="hidden" name="unidades[{{ $unidad->id }}][id]" value="{{ $unidad->id }}">
            </div>
        @endforeach

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary btn-lg" style="margin: 4px;">
                <i class="fas fa-save"></i> Guardar Estructura de Evaluación
            </button>
        </div>
    </form>
</div>

{{-- El Script JS es idéntico, no necesita cambios --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let totalEvaluacionesExistentes = {{ $materia->unidades->pluck('instrumentos')->flatten()->count() }};
        let globalIndex = totalEvaluacionesExistentes; 
        
        document.querySelectorAll('.add-instrumento').forEach(button => {
            button.addEventListener('click', function() {
                const unidadId = this.dataset.unidadId;
                const container = document.getElementById(`instrumentos-container-${unidadId}`);
                const newIndex = container.querySelectorAll('.instrumento-item').length + globalIndex; // Índice único

                const html = `
                    <div class="input-group mb-2 instrumento-item" data-unidad="${unidadId}">
                        <span class="input-group-text">Nombre:</span>
                        <input type="text" class="form-control" name="unidades[${unidadId}][instrumentos][${newIndex}][nombre]" placeholder="Ej: Tarea Nueva" required>
                        <span class="input-group-text">Peso (%):</span>
                        <input type="number" class="form-control input-porcentaje" name="unidades[${unidadId}][instrumentos][${newIndex}][porcentaje]" placeholder="%" min="1" max="100" required style="max-width: 100px;">
                        <button type="button" class="btn btn-outline-danger remove-instrumento">X</button>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
                attachPercentageListeners();
                calculateTotal(unidadId); 
            });
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-instrumento')) {
                const item = e.target.closest('.instrumento-item');
                const unidadId = item.dataset.unidad;
                const evaluacionId = item.dataset.evaluacionId; 
                
                if (evaluacionId) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'instrumentos_a_eliminar[]';
                    hiddenInput.value = evaluacionId;
                    document.querySelector('form').appendChild(hiddenInput); 
                }
                item.remove();
                calculateTotal(unidadId);
            }
        });

        function attachPercentageListeners() {
            document.querySelectorAll('.input-porcentaje').forEach(input => {
                input.removeEventListener('input', handlePercentageInput);
                input.addEventListener('input', handlePercentageInput);
            });
        }
        
        function handlePercentageInput() {
            const item = this.closest('.instrumento-item');
            if (item) {
                const unidadId = item.dataset.unidad;
                calculateTotal(unidadId);
            }
        }

        function calculateTotal(unidadId) {
            let total = 0;
            const totalDisplay = document.getElementById(`total-unidad-${unidadId}`);
            if (!totalDisplay) return;
            
            document.querySelectorAll(`#instrumentos-container-${unidadId} .input-porcentaje`).forEach(pInput => {
                total += parseInt(pInput.value || 0);
            });

            totalDisplay.textContent = `${total}%`;
            if (total === 100) {
                totalDisplay.classList.remove('text-danger');
                totalDisplay.classList.add('text-success');
            } else {
                totalDisplay.classList.remove('text-success');
                totalDisplay.classList.add('text-danger');
            }
        }
        
        attachPercentageListeners(); 
        @foreach ($materia->unidades as $unidad)
            calculateTotal({{ $unidad->id }});
        @endforeach
    });
</script>
@endsection