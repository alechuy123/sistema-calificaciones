@extends('layouts.app')

@section('title', 'Editar Grupo')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Editar Grupo: <span class="text-blue-600">{{ $grupo->nombre }}</span></h1>
            <a href="{{ route('grupos.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 transition duration-300">
                &larr; Volver al listado
            </a>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 sm:p-8">

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

                <form action="{{ route('grupos.update', $grupo) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Grupo</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $grupo->nombre) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <!-- --- CAMBIO: Añadido id="carrera_id" --- -->
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

                    <!-- --- CAMBIO: Select de Materias modificado --- -->
                    <div>
                        <label for="materias_select" class="block text-sm font-medium text-gray-700">
                            2. Materias del Grupo (Se cargarán al elegir carrera)
                        </label>
                        <select name="materias[]" id="materias_select" required multiple disabled
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 h-40 disabled:bg-gray-100">

                            <!-- El JS llenará esto. Dejamos un placeholder -->
                            <option value="" disabled>-- Primero seleccione una carrera --</option>

                        </select>
                        <p class="mt-1 text-sm text-gray-500">Puedes seleccionar varias materias manteniendo presionada la tecla 'Ctrl' (o 'Cmd' en Mac).</p>
                    </div>
                    <!-- --- FIN DEL CAMBIO --- -->

                    <!-- Checkbox de Activo -->
                    <div class="md:col-span-2">
                        {{-- CAMPO OCULTO (CLAVE) para manejar el estado 'off' --}}
                        <input type="hidden" name="esta_activo" value="0">
                        <div class="flex items-center">
                            <input type="checkbox" name="esta_activo" id="esta_activo" value="1"
                                   @checked(old('esta_activo', $grupo->esta_activo))
                                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="esta_activo" class="ml-2 block text-sm font-medium text-gray-900">Estado Activo</label>
                        </div>
                    </div>

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

<!-- ========================================================== -->
<!-- --- NUEVO SCRIPT DE JAVASCRIPT (Adaptado para EDITAR) --- -->
<!-- ========================================================== -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const carreraSelect = document.getElementById('carrera_id');
        const materiaSelect = document.getElementById('materias_select');

        // --- LÓGICA DE EDITAR ---
        // Pasamos los IDs de las materias que ya tiene el grupo desde PHP a JS
        const materiasSeleccionadasPorDefecto = {{ json_encode($materias_actuales_ids ?? []) }};
        // --- FIN LÓGICA DE EDITAR ---

        /**
         * Función para cargar materias vía API y pre-seleccionar las necesarias.
         * @param {string} carreraId - El ID de la carrera seleccionada.
         * @param {Array<number>} seleccionados - Array de IDs de materias que deben aparecer seleccionadas.
         */
        function cargarMaterias(carreraId, seleccionados = []) {
            materiaSelect.innerHTML = '<option value="">Cargando...</option>';

            if (!carreraId) {
                materiaSelect.innerHTML = '<option value="" disabled>-- Primero seleccione una carrera --</option>';
                materiaSelect.disabled = true;
                return;
            }

            // Hacer la llamada a la API
            fetch(`/api/carreras/${carreraId}/materias`)
                .then(response => response.json())
                .then(materias => {
                    materiaSelect.innerHTML = ''; // Limpiar "Cargando..."

                    if (materias.length === 0) {
                        materiaSelect.innerHTML = '<option value="" disabled>-- Esta carrera no tiene materias --</option>';
                        materiaSelect.disabled = true;
                    } else {
                        materias.forEach(materia => {
                            const option = document.createElement('option');
                            option.value = materia.id;
                            option.textContent = materia.nombre;

                            // --- LÓGICA DE EDITAR ---
                            // Marcamos como "selected" si el ID está en el array de seleccionados
                            if (seleccionados.includes(materia.id)) {
                                option.selected = true;
                            }
                            // --- FIN LÓGICA DE EDITAR ---

                            materiaSelect.appendChild(option);
                        });
                        materiaSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar las materias:', error);
                    materiaSelect.innerHTML = '<option value="" disabled>-- Error al cargar materias --</option>';
                    materiaSelect.disabled = true;
                });
        }

        // --- LÓGICA DE EJECUCIÓN ---

        // 1. Escuchar cambios en el <select> de Carreras
        carreraSelect.addEventListener('change', function() {
            // Si el usuario cambia la carrera, cargamos las materias SIN pre-seleccionar nada
            cargarMaterias(this.value, []);
        });

        // 2. Ejecutar al cargar la página para el estado inicial

        // Primero, revisamos si hay un error de validación (valores "old")
        @if(old('carrera_id') && old('materias'))
            // --- Caso A: Falló la validación ---
            // Pasamos los IDs de "old('materias')" para que se re-seleccionen
            const materiasOld = {{ json_encode(old('materias')) }}.map(Number);
            cargarMaterias('{{ old('carrera_id') }}', materiasOld);

        @else
            // --- Caso B: Carga normal de la página ---
            // Cargamos las materias de la carrera actual del grupo
            const carreraActual = '{{ $grupo->carrera_id }}';
            if (carreraActual) {
                // Pasamos las materias que el grupo ya tiene para pre-seleccionarlas
                cargarMaterias(carreraActual, materiasSeleccionadasPorDefecto);
            }
        @endif
    });
</script>
@endpush

