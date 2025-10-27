<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Criterio de Evaluación</title>
  
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f7f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px 0; }
        .container { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
        h1 { color: #1e88e5; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; font-size: 1em; box-sizing: border-box; }
        input:disabled { background-color: #e9ecef; }
        button { width: 100%; padding: 15px; background-color: #ffc107; color: #212529; border: none; border-radius: 8px; font-size: 1.1em; cursor: pointer; transition: background-color 0.3s; font-weight: bold; }
        button:hover { background-color: #e0a800; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #777; }
        .success-message { padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; margin-bottom: 20px; }
        .error-message { padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px; margin-bottom: 20px; }
        #add-subtarea { background-color: #007bff; margin-top: 10px; width: 100%; }
        #add-subtarea:hover { background-color: #0056b3; }
        .subtarea-item { display: flex; gap: 10px; align-items: center; margin-bottom: 10px; }
        .subtarea-item input { flex-grow: 1; }
        .remove-subtarea { background-color: #dc3545; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; font-weight: bold; cursor: pointer; line-height: 30px; padding: 0; flex-shrink: 0; }
    </style>
</head>
<body>
<div class="container">
    <h1>Editar Criterio de Evaluación</h1>

    @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif
    @error('porcentaje_decimal')
        <div class="error-message">{{ $message }}</div>
    @enderror

    <form action="{{ route('criterios.update', $criterio->id) }}" method="POST">
        @csrf
        @method('PUT')

        @if(request()->has('grupo_id'))
            <input type="hidden" name="grupo_id" value="{{ request('grupo_id') }}">
        @endif

        {{-- ... (Campos para Materia, Nombre y Porcentaje del criterio principal) ... --}}
        <div class="form-group">
            <label>Materia:</label>
            <input type="text" value="{{ $criterio->materia->nombre }}" disabled>
        </div>
        <div class="form-group">
            <label for="nombre">Nombre del Criterio:</label>
            <input type="text" name="nombre" value="{{ $criterio->nombre }}" required>
        </div>
        <div class="form-group">
            <label for="porcentaje_decimal">Porcentaje (%):</label>
            <input type="number" name="porcentaje_decimal" value="{{ $criterio->porcentaje_decimal }}" required>
        </div>
        
        <hr>
        <h3>Gestión de Subtareas</h3>
        
        {{-- ========================================================== --}}
        {{-- === INICIO DEL CAMBIO: Gestión dinámica de subtareas === --}}
        {{-- ========================================================== --}}
        <div id="subtareas-container">
            {{-- Mostramos las subtareas que ya existen --}}
            @foreach($criterio->subtareas as $subtarea)
            <div class="subtarea-item" data-id="{{ $subtarea->id }}">
                {{-- El nombre del input incluye el ID para que el controlador sepa cuál actualizar --}}
                <input type="text" name="subtareas[{{ $subtarea->id }}][nombre]" value="{{ $subtarea->nombre }}" required>
                <button type="button" class="remove-subtarea">&times;</button>
            </div>
            @endforeach
        </div>
        
        {{-- Campo oculto para guardar los IDs de las subtareas a eliminar --}}
        <div id="delete-subtareas-container"></div>
        
        <button type="button" id="add-subtarea">Añadir Nueva Subtarea</button>

        <button type="submit" style="margin-top: 20px;">Actualizar Criterio</button>
    </form>
    
    <a class="back-link" href="javascript:history.back()">Cancelar y Volver</a>
</div>

<script>
    const container = document.getElementById('subtareas-container');
    const deleteContainer = document.getElementById('delete-subtareas-container');

    // Lógica para AÑADIR nuevas subtareas
    document.getElementById('add-subtarea').addEventListener('click', function() {
        // Usamos 'new_' para que el controlador sepa que es un nuevo registro
        const index = 'new_' + Date.now();
        const subtareaWrapper = document.createElement('div');
        subtareaWrapper.classList.add('subtarea-item');
        subtareaWrapper.innerHTML = `
            <input type="text" name="subtareas[${index}][nombre]" placeholder="Nombre de la nueva subtarea" required>
            <button type="button" class="remove-subtarea">&times;</button>
        `;
        container.appendChild(subtareaWrapper);
    });

    // Lógica para ELIMINAR subtareas (existentes y nuevas)
    container.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-subtarea')) {
            const wrapper = event.target.parentElement;
            
            // Si la subtarea es una que ya existía (tiene un data-id)...
            if (wrapper.dataset.id) {
                const subtareaId = wrapper.dataset.id;
                // ...creamos un campo oculto para decirle al controlador que la borre.
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'delete_subtareas[]';
                hiddenInput.value = subtareaId;
                deleteContainer.appendChild(hiddenInput);
            }
            
            // Eliminamos el elemento visual de la pantalla
            wrapper.remove();
        }
    });
</script>
</body>
</html>
