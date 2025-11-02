@extends('layouts.app')

@section('title', 'Editar Alumno')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Editar Alumno: <span class="text-blue-600">{{ $alumno->nombre }} {{ $alumno->apellido_paterno }}</span>
            </h1>
            <a href="{{ route('alumnos.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 transition duration-300">
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

                <form action="{{ route('alumnos.update', $alumno) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre(s)</label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $alumno->nombre) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="apellido_paterno" class="block text-sm font-medium text-gray-700">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" id="apellido_paterno" value="{{ old('apellido_paterno', $alumno->apellido_paterno) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="apellido_materno" class="block text-sm font-medium text-gray-700">Apellido Materno</LAbel>
                            <input type="text" name="apellido_materno" id="apellido_materno" value="{{ old('apellido_materno', $alumno->apellido_materno) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="matricula" class="block text-sm font-medium text-gray-700">Matrícula</label>
                            <input type="text" name="matricula" id="matricula" value="{{ old('matricula', $alumno->matricula) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="carrera_id" class="block text-sm font-medium text-gray-700">1. Carrera</label>
                            <select name="carrera_id" id="carrera_select" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione una Carrera</option>
                                @foreach ($carreras as $carrera)
                                    <option value="{{ $carrera->id }}" @selected(old('carrera_id', $alumno->carrera_id) == $carrera->id)>
                                        {{ $carrera->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="ciclo_escolar_id" class="block text-sm font-medium text-gray-700">Ciclo Escolar</label>
                            <select name="ciclo_escolar_id" id="ciclo_escolar_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione un Ciclo</option>
                                @foreach ($ciclos as $ciclo)
                                    <option value="{{ $ciclo->id }}" @selected(old('ciclo_escolar_id', $alumno->ciclo_escolar_id) == $ciclo->id)>
                                        {{ $ciclo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            {{-- CAMPO OCULTO (CLAVE) para manejar el estado --}}
                            <input type="hidden" name="esta_activo" value="0">
                            <div class="flex items-center">
                                <input type="checkbox" name="esta_activo" id="esta_activo" value="1"
                                       @checked(old('esta_activo', $alumno->esta_activo))
                                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="esta_activo" class="ml-2 block text-sm font-medium text-gray-900">Estado Activo</label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div>
                        <label for="grupo_id" class="block text-sm font-medium text-gray-700">2. Asignar a Grupo (Opcional)</label>
                        <select name="grupo_id" id="grupo_select"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                disabled> <option value="">-- Primero seleccione una carrera --</option>

                            </select>
                         <p class="mt-1 text-sm text-gray-500">
                            (Solo se mostrarán grupos de la carrera seleccionada)
                        </p>
                    </div>
                    <div class="flex justify-end pt-6">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                            Actualizar Alumno
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

        const carreraSelect = document.getElementById('carrera_select');
        const grupoSelect = document.getElementById('grupo_select');

        // --- LÓGICA DE EDITAR ---
        // Guardamos el ID del grupo que debe estar seleccionado
        // (ya sea por un error de validación 'old()', o el que ya tiene el alumno en la BD)
        const idGrupoSeleccionado = "{{ old('grupo_id', $grupo_actual_id ?? null) }}";

        /**
         * Función para cargar grupos vía API y pre-seleccionar las necesarias.
         * @param {string} carreraId - El ID de la carrera seleccionada.
         * @param {string|null} idGrupoParaSeleccionar - El ID del grupo que debe aparecer seleccionado.
         */
        function cargarGrupos(carreraId, idGrupoParaSeleccionar) {
            grupoSelect.innerHTML = '<option value="">Cargando...</option>';
            grupoSelect.disabled = true;

            if (!carreraId) {
                grupoSelect.innerHTML = '<option value="">-- Primero seleccione una carrera --</option>';
                return;
            }

            // Llamamos a la API
            fetch(`/api/carreras/${carreraId}/grupos`)
                .then(response => response.json())
                .then(grupos => {
                    grupoSelect.innerHTML = ''; // Limpiar "Cargando..."

                    const defaultOption = document.createElement('option');
                    defaultOption.value = "";
                    defaultOption.textContent = "-- No asignar a un grupo aún --";
                    grupoSelect.appendChild(defaultOption);

                    if (grupos.length === 0) {
                        defaultOption.textContent = "-- Esta carrera no tiene grupos --";
                    } else {
                        grupos.forEach(grupo => {
                            const option = document.createElement('option');
                            option.value = grupo.id;
                            option.textContent = grupo.nombre;

                            // *** La Lógica de Selección de EDITAR ***
                            // Si el ID de este grupo es el que debe estar seleccionado, lo marcamos
                            if (grupo.id == idGrupoParaSeleccionar) {
                                option.selected = true;
                            }

                            grupoSelect.appendChild(option);
                        });
                        grupoSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar los grupos:', error);
                    grupoSelect.innerHTML = '<option value="" disabled>-- Error al cargar grupos --</option>';
                });
        }

        // "Listener" para cuando el usuario cambia la carrera
        carreraSelect.addEventListener('change', function() {
            // Al cambiar la carrera, no pre-seleccionamos nada (null)
            cargarGrupos(this.value, null);
        });

        // --- CARGA INICIAL (La parte clave de "Editar") ---
        // Al cargar la página, obtenemos la carrera que YA ESTÁ seleccionada
        const idCarreraActual = carreraSelect.value;

        if (idCarreraActual) {
            // Y llamamos a la función con el ID de esa carrera Y el ID del grupo
            // que debe estar pre-seleccionado (ya sea por 'old' o de la BD)
            cargarGrupos(idCarreraActual, idGrupoSeleccionado);
        }
    });
</script>
@endpush
@endsection
