@extends('layouts.app')

@section('title', 'Editar Grupo')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <!-- Encabezado y Botón -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Editar Grupo: <span class="text-blue-600">{{ $grupo->nombre }}</span>
            </h1>
            <a href="{{ route('grupos.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 transition duration-300">
                &larr; Volver al listado
            </a>
        </div>

        <!-- Tarjeta del Formulario -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 sm:p-8">

                <!-- Errores -->
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                        <strong class="font-bold">¡Atención!</strong>
                        <span class="block sm:inline">Hubo problemas al actualizar.</span>
                        <ul class="mt-3 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulario -->
                <form action="{{ route('grupos.update', $grupo) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Grupo</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $grupo->nombre) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Carrera (con ID para JS) -->
                        <div>
                            <label for="carrera_id" class="block text-sm font-medium text-gray-700">1. Carrera del Grupo</label>
                            <select name="carrera_id" id="carrera_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione la Carrera</option>
                                @foreach ($carreras as $carrera)
                                    <option value="{{ $carrera->id }}" @selected(old('carrera_id', $grupo->carrera_id) == $carrera->id)>
                                        {{ $carrera->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cuatrimestre -->
                        <div>
                            <label for="cuatrimestre_id" class="block text-sm font-medium text-gray-700">Cuatrimestre</label>
                            <select name="cuatrimestre_id" id="cuatrimestre_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione un Cuatrimestre</option>
                                @foreach ($cuatrimestres as $cuatrimestre)
                                    <option value="{{ $cuatrimestre->id }}" @selected(old('cuatrimestre_id', $grupo->cuatrimestre_id) == $cuatrimestre->id)>
                                        {{ $cuatrimestre->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Materias (Dinámico con JS) -->
                    <div>
                        <label for="materias_select" class="block text-sm font-medium text-gray-700">
                            2. Materias del Grupo (Se cargarán al elegir carrera)
                        </label>
                        <select name="materias[]" id="materias_select" required multiple disabled
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 h-40 disabled:bg-gray-100">

                            <option value="" disabled>-- Primero seleccione una carrera --</option>

                            {{--
                                El JS se encargará de llenar esto.
                                Pero como respaldo (o si JS falla), podemos iterar las materias que pasamos desde el controlador.
                            --}}
                            @foreach ($materias_de_la_carrera as $materia)
                                <option value="{{ $materia->id }}"
                                    @selected(in_array($materia->id, old('materias', $materias_actuales_ids ?? [])))>
                                    {{ $materia->nombre }}
                                </option>
                            @endforeach

                        </select>
                        <p class="mt-1 text-sm text-gray-500">Puedes seleccionar varias materias manteniendo presionada la tecla 'Ctrl' (o 'Cmd' en Mac).</p>
                    </div>

                    <!-- Checkbox Activo -->
                    <div class="md:col-span-2">
                        <input type="hidden" name="esta_activo" value="0">
                        <div class="flex items-center">
                            <input type="checkbox" name="esta_activo" id="esta_activo" value="1"
                                   @checked(old('esta_activo', $grupo->esta_activo))
                                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="esta_activo" class="ml-2 block text-sm font-medium text-gray-900">Estado Activo</label>
                        </div>
                    </div>

                    <!-- Botón -->
                    <div class="flex justify-end pt-6">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                            Actualizar Grupo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const carreraSelect = document.getElementById('carrera_id');
        const materiaSelect = document.getElementById('materias_select');

        // IDs de las materias que ya tiene el grupo (pasados desde PHP)
        // Usamos ?? [] para asegurar que sea un array válido aunque esté vacío
        const materiasSeleccionadas = {{ json_encode($materias_actuales_ids ?? []) }}.map(String);

        // Habilitar el select inicialmente si ya tiene opciones cargadas por PHP
        if (materiaSelect.options.length > 1) {
            materiaSelect.disabled = false;
        }

        function cargarMaterias(carreraId) {
            materiaSelect.innerHTML = '<option value="">Cargando...</option>';
            materiaSelect.disabled = true;

            if (!carreraId) {
                materiaSelect.innerHTML = '<option value="" disabled>-- Primero seleccione una carrera --</option>';
                return;
            }

            fetch(`/api/carreras/${carreraId}/materias`)
                .then(response => response.json())
                .then(materias => {
                    materiaSelect.innerHTML = '';

                    if (materias.length === 0) {
                        materiaSelect.innerHTML = '<option value="" disabled>-- Esta carrera no tiene materias --</option>';
                        materiaSelect.disabled = true;
                    } else {
                        materias.forEach(materia => {
                            const option = document.createElement('option');
                            option.value = materia.id;
                            option.textContent = materia.nombre;

                            // Pre-seleccionar si el ID está en la lista de materias del grupo
                            if (materiasSeleccionadas.includes(String(materia.id))) {
                                option.selected = true;
                            }

                            materiaSelect.appendChild(option);
                        });
                        materiaSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    materiaSelect.innerHTML = '<option value="" disabled>-- Error al cargar --</option>';
                });
        }

        // Solo cargar por AJAX si el usuario CAMBIA la carrera.
        // Si no la cambia, usamos las opciones que ya cargó PHP (más rápido y estable).
        carreraSelect.addEventListener('change', function() {
            // Al cambiar carrera, limpiamos la selección previa porque ya no aplica
            materiasSeleccionadas.length = 0;
            cargarMaterias(this.value);
        });
    });
</script>
@endpush
@endsection
