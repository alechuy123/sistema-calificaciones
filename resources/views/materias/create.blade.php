@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a>

    <div class="card shadow-sm">
        <div class="card-header">
            <h2><i class="fas fa-plus-circle"></i> Registrar Nueva Materia</h2>
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

            <form action="{{ route('materias.store') }}" method="POST">
                @csrf
                
                <div class="border p-3 rounded mb-4">
                    <h4>Datos Generales</h4>
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Materia:</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="objetivo" class="form-label">Objetivo de la Materia:</label>
                        <textarea id="objetivo" name="objetivo" rows="4" class="form-control" required>{{ old('objetivo') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="carreras" class="form-label">Carreras Asociadas:</label>
                        <select name="carreras[]" id="carreras" class="form-select" multiple required size="5">
                            @foreach ($carreras as $carrera)
                                <option value="{{ $carrera->id }}"
                                    {{ (is_array(old('carreras')) && in_array($carrera->id, old('carreras'))) ? 'selected' : '' }}>
                                    {{ $carrera->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Mantén presionado Ctrl (Windows) / Cmd (Mac) para seleccionar varias carreras.</small>
                        @if($carreras->isEmpty())
                            <p class="text-danger small mt-1">¡Error! No hay carreras registradas. Registre una primero.</p>
                        @endif
                    </div>
                </div>

                <div class="border p-3 rounded mb-4">
                    <h4>Unidades del Cuatrimestre</h4>
                    <div id="unidades-container">
                        {{-- Unidad 1 (Inicial) --}}
                        <div class="input-group mb-2 unidad-item">
                            <span class="input-group-text unidad-label">Unidad 1</span>
                            <input type="text" name="unidades[0][nombre]" class="form-control" placeholder="Ej: Fundamentos de Programación" required>
                            <button type="button" class="btn btn-outline-danger remove-unidad" style="display: none;">X</button>
                        </div>
                    </div>
                    
                    <button type="button" id="add-unidad" class="btn btn-outline-primary mt-2">
                        <i class="fas fa-plus"></i> Agregar Unidad
                    </button>
                </div>

                <div class="text-end">
                    <a href="{{ route('materias.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Materia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- El Script no cambia, pero lo incluyo por completitud --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('unidades-container');
        const addButton = document.getElementById('add-unidad');

        addButton.addEventListener('click', function () {
            const newIndex = container.children.length; 
            const newUnidad = document.createElement('div');
            newUnidad.classList.add('input-group', 'mb-2', 'unidad-item');
            
            newUnidad.innerHTML = `
                <span class="input-group-text unidad-label">Unidad ${newIndex + 1}</span>
                <input type="text" name="unidades[${newIndex}][nombre]" class="form-control" placeholder="Nombre de la Unidad" required>
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

        function updateUnitIndices() {
            const items = container.querySelectorAll('.unidad-item');
            items.forEach((item, index) => {
                const label = item.querySelector('.unidad-label');
                if (label) {
                    label.textContent = `Unidad ${index + 1}`;
                }
                const input = item.querySelector('input[type="text"]');
                if (input) {
                    input.name = `unidades[${index}][nombre]`;
                }
                const removeButton = item.querySelector('.remove-unidad');
                if (removeButton) {
                    // Solo muestra el botón X si NO es el primer elemento
                    removeButton.style.display = index === 0 ? 'none' : 'inline-block';
                }
            });
        }
        updateUnitIndices();
    });
</script>
@endsection