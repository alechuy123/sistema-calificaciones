<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Criterios de Evaluación</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f7f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px 0; }
        .container { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
        h1 { color: #1e88e5; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; font-size: 1em; box-sizing: border-box; }
        select:disabled { background-color: #e9ecef; cursor: not-allowed; }
        button { padding: 15px; background-color: #28a745; color: white; border: none; border-radius: 8px; font-size: 1.1em; cursor: pointer; transition: background-color 0.3s; }
        button:hover { background-color: #218838; }
        #add-subtarea { background-color: #007bff; margin-top: 10px; width: 100%; }
        #add-subtarea:hover { background-color: #0056b3; }
        .subtarea-item { display: flex; gap: 10px; align-items: center; margin-bottom: 10px; }
        .subtarea-item input { flex-grow: 1; }
        .remove-subtarea { background-color: #dc3545; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; font-weight: bold; cursor: pointer; line-height: 30px; padding: 0; flex-shrink: 0; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #777; }
        .success-message { padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; margin-bottom: 20px; }
        .error-message { padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Gestión de Criterios de Evaluación</h1>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif
    @error('porcentaje_decimal')
        <div class="error-message">{{ $message }}</div>
    @enderror

    <form action="{{ route('criterios.store') }}" method="POST">
        @csrf
        
        {{-- CAMPO OCULTO: Guardamos el ID del grupo para saber a dónde volver --}}
        @if(request()->has('grupo_id'))
            <input type="hidden" name="grupo_id" value="{{ request('grupo_id') }}">
        @endif

        <div class="form-group">
            <label for="materia_id">Materia:</label>
            {{-- Si la materia ya viene definida, el select se deshabilita para evitar errores --}}
            <select name="materia_id" id="materia_id" required {{ isset($materia) && $materia ? 'disabled' : '' }}>
                @if(!isset($materia))
                    <option value="">-- Selecciona una materia --</option>
                @endif
                @foreach($materias as $mat)
                    {{-- Si la materia actual coincide con la que se pasó, la pre-seleccionamos --}}
                    <option value="{{ $mat->id }}" {{ isset($materia) && $materia->id == $mat->id ? 'selected' : '' }}>
                        {{ $mat->nombre }}
                    </option>
                @endforeach
            </select>
            
            {{-- Si el select está deshabilitado, enviamos el ID en un campo oculto para que el controlador lo reciba --}}
            @if(isset($materia) && $materia)
                <input type="hidden" name="materia_id" value="{{ $materia->id }}">
            @endif
        </div>

        <div class="form-group">
            <label for="nombre">Nombre del Criterio Principal:</label>
            <input type="text" name="nombre" placeholder="Ej. Ejercicios" required value="{{ old('nombre') }}">
        </div>
        <div class="form-group">
            <label for="porcentaje_decimal">Porcentaje del Criterio (%):</label>
            <input type="number" name="porcentaje_decimal" placeholder="Ej. 30" required value="{{ old('porcentaje_decimal') }}">
        </div>
        
        <hr>
        <h3>Subtareas (Opcional)</h3>
        <div id="subtareas-container"></div>
        <button type="button" id="add-subtarea">Añadir Subtarea</button>

        <button type="submit" style="margin-top: 20px; width: 100%;">Guardar Criterio</button>
    </form>
    
    {{-- El enlace de "Cancelar" ahora es contextual, te regresa al panel de gestión --}}
    @if(request()->has('grupo_id') && isset($materia) && $materia)
        <a class="back-link" href="{{ route('calificaciones.create', ['grupo' => request('grupo_id'), 'materia' => $materia->id]) }}">Cancelar y Volver al Panel</a>
    @else
        <a class="back-link" href="{{ route('grupos.index') }}">Cancelar</a>
    @endif
</div>
<script>
    const container = document.getElementById('subtareas-container');
    document.getElementById('add-subtarea').addEventListener('click', function() {
        const index = container.children.length;
        const subtareaWrapper = document.createElement('div');
        subtareaWrapper.classList.add('subtarea-item');
        subtareaWrapper.innerHTML = `
            <input type="text" name="subtareas[${index}][nombre]" placeholder="Nombre de Subtarea (ej. Práctica 1)" required>
            <button type="button" class="remove-subtarea">&times;</button>
        `;
        container.appendChild(subtareaWrapper);
    });
    container.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-subtarea')) {
            event.target.parentElement.remove();
        }
    });
</script>
</body>
</html>