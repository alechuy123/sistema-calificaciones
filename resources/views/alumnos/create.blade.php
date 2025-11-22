@extends('layouts.app')

@section('title', 'Registrar Alumno')

@section('content')

{{--
    SOLUCIÓN AL ERROR:
    Movemos la lógica PHP aquí arriba para que no estorbe al @foreach en el HTML.
    Si hay un "old(carrera_id)" (error de validación), cargamos los grupos de esa carrera.
--}}
@php
    $grupos_old = collect([]); // Colección vacía por defecto

    if(old('carrera_id')) {
        $carrera = \App\Models\Carrera::find(old('carrera_id'));
        if($carrera) {
            $grupos_old = $carrera->grupos()->where('esta_activo', 1)->get();
        }
    }
@endphp

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
                            <label for="apellido_materno" class="block text-sm font-medium text-gray-700">Apellido Materno</label>
                            <input type="text" name="apellido_materno" id="apellido_materno" value="{{ old('apellido_materno') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="matricula" class="block text-sm font-medium text-gray-700">Matrícula (Opcional)</label>
                            <input type="text" name="matricula" id="matricula" value="{{ old('matricula') }}"
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

                        <!--
                             El 'disabled' depende de si tenemos grupos cargados (por old) o no.
                             Si $grupos_old tiene datos, NO debe estar disabled.
                        -->
                        <select name="grupo_id" id="grupo_select"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                {{ $grupos_old->isEmpty() ? 'disabled' : '' }}>

                            <option value="">
                                {{ $grupos_old->isEmpty() ? '-- Primero seleccione una carrera --' : '-- No asignar a un grupo aún --' }}
                            </option>

                            <!--
                                Aquí iteramos la variable que preparamos arriba en el @php.
                                Ya no hay lógica compleja aquí, solo un foreach limpio.
                            -->
                            @foreach($grupos_old as $grupo)
                                <option value="{{ $grupo->id }}" {{ old('grupo_id') == $grupo->id ? 'selected' : '' }}>
                                    {{ $grupo->nombre }}
                                </option>
                            @endforeach

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
    document.addEventListener('DOMContentLoaded', function () {

        const carreraSelect = document.getElementById('carrera_select');
        const grupoSelect = document.getElementById('grupo_select');

        function cargarGrupos(carreraId) {
            grupoSelect.innerHTML = '<option value="">Cargando...</option>';
            grupoSelect.disabled = true; // Deshabilitar mientras carga

            if (!carreraId) {
                grupoSelect.innerHTML = '<option value="">-- Primero seleccione una carrera --</option>';
                return;
            }

            fetch(`/api/carreras/${carreraId}/grupos`)
                .then(response => {
                    if (!response.ok) throw new Error('Error en la red');
                    return response.json();
                })
                .then(grupos => {
                    grupoSelect.innerHTML = '';

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
                            option.value = grupo.id;
                            option.textContent = grupo.nombre;
                            grupoSelect.appendChild(option);
                        });
                        grupoSelect.disabled = false; // Habilitar si hay grupos
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    grupoSelect.innerHTML = '<option value="" disabled>-- Error al cargar --</option>';
                });
        }

        // Escuchar cambios
        carreraSelect.addEventListener('change', function() {
            cargarGrupos(this.value);
        });
    });
</script>
@endpush
@endsection
