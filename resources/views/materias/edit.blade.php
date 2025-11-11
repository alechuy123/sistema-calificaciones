@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a>

    <div class="card shadow-sm">
        <div class="card-header">
            <h2><i class="fas fa-edit"></i> Editar Materia: {{ $materia->nombre }}</h2>
        </div>
        <div class="card-body">
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <p>Por favor, corrige los siguientes errores:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('materias.update', $materia) }}" method="POST">
                @csrf
                @method('PUT') 
                
                <div class="border p-3 rounded mb-4">
                    <h4>Datos Generales</h4>
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Materia:</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre', $materia->nombre) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="objetivo" class="form-label">Objetivo de la Materia:</label>
                        <textarea id="objetivo" name="objetivo" rows="4" class="form-control" required>{{ old('objetivo', $materia->objetivo) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="carreras" class="form-label">Carreras Asociadas:</label>
                        @php
                            $currentCarreraIds = old('carreras') ?: $materia->carreras->pluck('id')->toArray();
                        @endphp
                        <select name="carreras[]" id="carreras" class="form-select" multiple required size="5">
                            @foreach ($carreras as $carrera)
                                <option value="{{ $carrera->id }}"
                                    @if (in_array($carrera->id, $currentCarreraIds)) selected @endif>
                                    {{ $carrera->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Mantén presionado Ctrl (Windows) / Cmd (Mac) para seleccionar varias.</small>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="esta_activo" value="0"> {{-- Valor por defecto si no se marca --}}
                        <input class="form-check-input" type="checkbox" role="switch" id="esta_activo" name="esta_activo" value="1" @checked(old('esta_activo', $materia->esta_activo))>
                        <label class="form-check-label" for="esta_activo">Materia Activa</label>
                    </div>

                </div>

                <div class="border p-3 rounded mb-4">
                    <h4>Unidades del Cuatrimestre</h4>
                    <div id="unidades-container">
                        @foreach ($materia->unidades as $index => $unidad)
                            <div class="input-group mb-2 unidad-item">
                                <span class="input-group-text unidad-label">Unidad {{ $index + 1 }}</span>
                                <input type="hidden" name="unidades[{{ $index }}][id]" value="{{ $unidad->id ?? '' }}">
                                <input type="text" name="unidades[{{ $index }}][nombre]" class="form-control"
                                       value="{{ old('unidades.'.$index.'.nombre', $unidad->nombre) }}" 
                                       placeholder="Nombre de la Unidad" required>
                                <button type="button" class="btn btn-outline-danger remove-unidad">X</button>
                            </div>
                        @endforeach
                    </div>
                    
                    <button type="button" id="add-unidad" class="btn btn-outline-primary mt-2">
                        <i class="fas fa-plus"></i> Agregar Unidad
                    </button>
                </div>

                <div class="text-end">
                    <a href="{{ route('materias.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script para las unidades dinámicas --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('unidades-container');
        const addButton = document.getElementById('add-unidad');
        const form = document.querySelector('form');

        function updateUnitIndices() {
            const items = container.querySelectorAll('.unidad-item');
            items.forEach((item, index) => {
                const label = item.querySelector('.unidad-label');
                if (label) {
                    label.textContent = `Unidad ${index + 1}`;
                }

                // Actualizar el índice de 'name'
                item.querySelectorAll('input').forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/unidades\[\d+\]/, `unidades[${index}]`);
                    }
                });

                const removeButton = item.querySelector('.remove-unidad');
                if (removeButton) {
                    removeButton.style.display = items.length > 1 ? 'inline-block' : 'none';
                }
            });
        }

        addButton.addEventListener('click', function () {
            const newIndex = container.children.length; 
            const newUnidad = document.createElement('div');
            newUnidad.classList.add('input-group', 'mb-2', 'unidad-item');
            
            newUnidad.innerHTML = `
                <span class="input-group-text unidad-label">Unidad ${newIndex + 1}</span>
                <input type="hidden" name="unidades[${newIndex}][id]" value="">
                <input type="text" name="unidades[${newIndex}][nombre]" class="form-control" placeholder="Nombre de la Nueva Unidad" required>
                <button type="button" class="btn btn-outline-danger remove-unidad">X</button>
            `;
            container.appendChild(newUnidad);
            updateUnitIndices();
        });

        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-unidad')) {
                if (container.children.length > 1) {
                    e.target.closest('.unidad-item').remove();
                    updateUnitIndices();
                } else {
                    alert('Debe haber al menos una unidad.');
                }
            }
        });

        form.addEventListener('submit', function (e) {
            container.querySelectorAll('input[type="hidden"][name*="[id]"]').forEach(input => {
                if (input.value === '') {
                    input.remove();
                }
            });
        });
        
        updateUnitIndices(); // Ejecutar al inicio
    });
</script>
@endsection