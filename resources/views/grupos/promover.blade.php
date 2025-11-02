@extends('layouts.app')

@section('title', 'Promover Grupo')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <!-- Encabezado y Botón de "Volver" -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Promover Grupo: <span class="text-blue-600">{{ $grupo->nombre }}</span>
            </h1>
            <a href="{{ route('grupos.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 transition duration-300">
                &larr; Volver al listado
            </a>
        </div>

        <!-- Contenedor del Formulario -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 sm:p-8">

                <!-- Mensaje de Errores -->
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                        <strong class="font-bold">¡Atención!</strong>
                        <span class="block sm:inline">Hubo problemas con la promoción.</span>
                        <ul class="mt-3 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Info del Grupo Actual -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Grupo Actual</h2>
                    <p class="text-sm text-gray-600"><strong>Carrera:</strong> {{ $grupo->carrera->nombre }}</p>
                    <p class="text-sm text-gray-600"><strong>Cuatrimestre:</strong> {{ $grupo->cuatrimestre->nombre }}</p>
                    <p class="text-sm text-gray-600"><strong>Alumnos Inscritos:</strong> {{ $grupo->alumnos->count() }}</p>
                    <p class="text-sm text-gray-600"><strong>Materias Asignadas:</strong> {{ $grupo->materias->count() }}</p>
                </div>

                <!-- Formulario de Promoción -->
                <form action="{{ route('grupos.promover', $grupo) }}" method="POST" class="space-y-6">
                    @csrf

                    <p class="text-sm text-gray-600">
                        Al promover este grupo, se creará un **nuevo grupo** con los mismos alumnos y materias,
                        pero asignado a un nuevo cuatrimestre. El grupo actual ({{ $grupo->nombre }}) será desactivado.
                    </p>

                    <!-- Nuevo Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">1. Nuevo Nombre del Grupo</label>
                        <input type="text" name="nombre" id="nombre" placeholder="Ej: {{ $grupo->nombre }}-SIG" value="{{ old('nombre', $grupo->nombre . '-PROMO') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Se sugiere un nuevo nombre (Ej. A10-TI -> A11-TI).</p>
                    </div>

                    <!-- Nuevo Cuatrimestre -->
                    <div>
                        <label for="cuatrimestre_id" class="block text-sm font-medium text-gray-700">2. Asignar al Siguiente Cuatrimestre</label>
                        <select name="cuatrimestre_id" id="cuatrimestre_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccione un cuatrimestre</option>
                            @forelse ($cuatrimestres_siguientes as $cuatrimestre)
                                <option value="{{ $cuatrimestre->id }}" {{ old('cuatrimestre_id') == $cuatrimestre->id ? 'selected' : '' }}>
                                    {{ $cuatrimestre->nombre }} ({{ $cuatrimestre->fecha_inicio }} a {{ $cuatrimestre->fecha_fin }})
                                </option>
                            @empty
                                <option value="" disabled>No hay cuatrimestres activos para promover.</option>
                            @endforelse
                        </select>
                    </div>

                    <!-- Botón de Guardar -->
                    <div class="flex justify-end pt-6">
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition duration-300"
                                onclick="return confirm('¿Estás seguro de promover este grupo? Esta acción creará un nuevo grupo y desactivará el actual.')">
                            Confirmar y Promover Grupo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

