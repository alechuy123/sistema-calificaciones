@extends('layouts.app')

@section('title', 'Registrar Alumno')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Registrar Nuevo Alumno</h1>
            <a href="{{ route('alumnos.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 transition duration-300">
                &larr; Volver al listado
            </a>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 sm:p-8">

                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                        <strong class="font-bold">¡Atención!</strong>
                        <span class="block sm:inline">Hubo problemas con tu registro.</span>
                        <ul class="mt-3 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('alumnos.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre(s)</label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="apellido_paterno" class="block text-sm font-medium text-gray-700">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" id="apellido_paterno" value="{{ old('apellido_paterno') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="apellido_materno" class="block text-sm font-medium text-gray-700">Apellido Materno</LAbel>
                            <input type="text" name="apellido_materno" id="apellido_materno" value="{{ old('apellido_materno') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="matricula" class="block text-sm font-medium text-gray-700">Matrícula</label>
                            <input type="text" name="matricula" id="matricula" value="{{ old('matricula') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="carrera_id" class="block text-sm font-medium text-gray-700">1. Carrera</label>
                            <select name="carrera_id" id="carrera_select" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione una Carrera</option>
                                @foreach ($carreras as $carrera)
                                    <option value="{{ $carrera->id }}" {{ old('carrera_id') == $carrera->id ? 'selected' : '' }}>
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
                                    <option value="{{ $ciclo->id }}" {{ old('ciclo_escolar_id') == $ciclo->id ? 'selected' : '' }}>
                                        {{ $ciclo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <div>
                        <label for="grupo_id" class="block text-sm font-medium text-gray-700">2. Asignar a Grupo (Opcional)</label>
                        <select name="grupo_id" id="grupo_select"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                disabled> <option value="">-- Primero seleccione una carrera --</option>

                            @php
                                $grupos_old = []; // Inicializa un array vacío
                                if(old('carrera_id') && old('grupo_id')) {
                                    $carrera_old = \App\Models\Carrera::find(old('carrera_id'));
                                    // Comprueba que la carrera exista antes de llamar a ->grupos()
                                    if ($carrera_old) {
                                        $grupos_old = $carrera_old->grupos()->where('grupos.esta_activo', 1)->get();
                                    }
                                }
                            @endphp

                            @if(!empty($grupos_old))
                                @foreach($grupos_old as $grupo)
                                    <option value="{{ $grupo->id }}" {{ old('grupo_id') == $grupo->id ? 'selected' : '' }}>
                                        {{ $grupo->nombre }}
                                    </option>
                                @endforeach
                            @endif

                        </select>
                         <p class="mt-1 text-sm text-gray-500">
                            (Solo se mostrarán grupos de la carrera seleccionada)
                        </p>
                    </div>
                    <div class="flex justify-end pt-6">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                            Guardar Alumno
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Se ejecuta cuando todo el HTML ha sido cargado
    document.addEventListener('DOMContentLoaded', function () {

        const carreraSelect = document.getElementById('carrera_select');
        const grupoSelect = document.getElementById('grupo_select');

        // Función para cargar grupos
        function cargarGrupos(carreraId) {
            // Limpiar el select de grupos
            grupoSelect.innerHTML = '<option value="">Cargando...</option>';

            if (!carreraId) {
                grupoSelect.innerHTML = '<option value="">-- Primero seleccione una carrera --</option>';
                grupoSelect.disabled = true;
                return;
            }

            // Hacer la llamada a la API que creamos
            fetch(`/api/carreras/${carreraId}/grupos`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta de la red');
                    }
                    return response.json();
                })
                .then(grupos => {
                    grupoSelect.innerHTML = ''; // Limpiar "Cargando..."

                    // Añadir la opción de "No asignar"
                    const defaultOption = document.createElement('option');
                    defaultOption.value = "";
                    defaultOption.textContent = "-- No asignar a un grupo aún --";
                    grupoSelect.appendChild(defaultOption);

                    if (grupos.length === 0) {
                        defaultOption.textContent = "-- Esta carrera no tiene grupos --";
                        grupoSelect.disabled = true;
                    } else {
                        grupos.forEach(grupo => {
                            const option = document.createElement('option');
                            // --- ¡CORRECCIÓN! ---
                            option.value = grupo.id; // <-- Se usa . (punto)
                            option.textContent = grupo.nombre; // <-- Se usa . (punto)
                            // --- FIN CORRECCIÓN ---
                            grupoSelect.appendChild(option);
                        });
                        grupoSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar los grupos:', error);
                    grupoSelect.innerHTML = '<option value="" disabled>-- Error al cargar grupos --</option>';
                    grupoSelect.disabled = true;
                });
        }

        // Añadir un "listener" al <select> de Carreras
        carreraSelect.addEventListener('change', function() {
            cargarGrupos(this.value);
        });

        // Si hay un valor "old" (por un error de validación),
        // disparamos la carga inicial y reactivamos el select.
        @if(old('carrera_id'))
            cargarGrupos(carreraSelect.value);
            grupoSelect.disabled = false;

            // Re-seleccionar el grupo "old" si existe
            @if(old('grupo_id'))
                // Necesitamos un pequeño retraso para asegurar que el fetch termine
                setTimeout(() => {
                    grupoSelect.value = "{{ old('grupo_id') }}";
                }, 500);
            @endif
        @endif
    });
</script>
@endpush
