@extends('layouts.app')

@section('title', 'Crear Nuevo Grupo')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Crear Nuevo Grupo</h1>
            <a href="{{ route('grupos.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 transition duration-300">
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

                <form action="{{ route('grupos.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Grupo</label>
                        <input type="text" name="nombre" id="nombre" placeholder="Ej: G-ISC-2A" value="{{ old('nombre') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="carrera_id" class="block text-sm font-medium text-gray-700">1. Carrera del Grupo</label>
                            <select name="carrera_id" id="carrera_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione la Carrera</option>
                                @foreach ($carreras as $carrera)
                                    <option value="{{ $carrera->id }}" {{ old('carrera_id') == $carrera->id ? 'selected' : '' }}>
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
                                    <option value="{{ $cuatrimestre->id }}" {{ old('cuatrimestre_id') == $cuatrimestre->id ? 'selected' : '' }}>
                                        {{ $cuatrimestre->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="materias_select" class="block text-sm font-medium text-gray-700">
                            2. Materias del Grupo (Se cargarán al elegir carrera)
                        </label>
                        <select name="materias[]" id="materias_select" required multiple disabled
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 h-40 disabled:bg-gray-100">

                            <option value="" disabled>-- Primero seleccione una carrera --</option>

                        </select>
                        <p class="mt-1 text-sm text-gray-500">Puedes seleccionar varias materias manteniendo presionada la tecla 'Ctrl' (o 'Cmd' en Mac).</p>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                            Guardar Grupo
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
                    } else {
                        materias.forEach(materia => {
                            const option = document.createElement('option');
                            option.value = materia.id;
                            option.textContent = materia.nombre;
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

        carreraSelect.addEventListener('change', function() {
            cargarMaterias(this.value);
        });

        // Recargar si hay error de validación (old)
        @if(old('carrera_id'))
            cargarMaterias('{{ old("carrera_id") }}');
        @endif
    });
</script>
@endpush
@endsection
