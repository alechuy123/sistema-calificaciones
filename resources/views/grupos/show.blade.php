@extends('layouts.app')

@section('title', 'Detalles del Grupo')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Detalles del Grupo: {{ $grupo->nombre }}</h1>
                <div class="mt-2 flex flex-wrap gap-2">
                    @forelse($grupo->materias as $materia)
                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $materia->nombre }}
                        </span>
                    @empty
                        <span class="text-xs text-gray-500">Sin materias asignadas</span>
                    @endforelse
                </div>
                <p class="mt-2 text-lg text-gray-600">
                    <strong>Cuatrimestre:</strong> {{ $grupo->cuatrimestre->nombre }}
                </p>
            </div>
            <a href="{{ route('grupos.index') }}" class="mt-4 sm:mt-0 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 transition duration-300 self-start">
                &larr; Volver a Grupos
            </a>
        </div>

        @if ($message = Session::get('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                <p class="font-semibold">{{ $message }}</p>
            </div>
        @endif

        @if ($grupo->grupoAnterior || $grupo->grupoSiguiente)
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Historial de Promoción</h2>
                <div class="flex items-center space-x-4">

                    @if ($grupo->grupoAnterior)
                        <a href="{{ route('grupos.show', $grupo->grupoAnterior->id) }}" class="flex flex-col items-center text-center p-4 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            <span class="text-sm text-gray-500">Grupo Anterior</span>
                            <span class="text-lg font-semibold text-blue-600">{{ $grupo->grupoAnterior->nombre }}</span>
                            <span class="text-xs text-gray-400">({{ $grupo->grupoAnterior->cuatrimestre->nombre }})</span>
                        </a>
                    @else
                        <div class="flex flex-col items-center text-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-400">Grupo Anterior</span>
                            <span class="text-lg font-semibold text-gray-400">N/A</span>
                        </div>
                    @endif

                    <span class="text-2xl text-gray-400">&rarr;</span>

                    <div class="flex flex-col items-center text-center p-4 bg-blue-100 border border-blue-300 rounded-lg">
                        <span class="text-sm text-blue-500">Grupo Actual</span>
                        <span class="text-lg font-semibold text-blue-700">{{ $grupo->nombre }}</span>
                        <span class="text-xs text-blue-400">({{ $grupo->cuatrimestre->nombre }})</span>
                    </div>

                    <span class="text-2xl text-gray-400">&rarr;</span>

                    @if ($grupo->grupoSiguiente)
                        <a href="{{ route('grupos.show', $grupo->grupoSiguiente->id) }}" class="flex flex-col items-center text-center p-4 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            <span class="text-sm text-gray-500">Grupo Siguiente</span>
                            <span class="text-lg font-semibold text-blue-600">{{ $grupo->grupoSiguiente->nombre }}</span>
                            <span class="text-xs text-gray-400">({{ $grupo->grupoSiguiente->cuatrimestre->nombre }})</span>
                        </a>
                    @else
                        <div class="flex flex-col items-center text-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-400">Grupo Siguiente</span>
                            <span class="text-lg font-semibold text-gray-400">N/A</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <form action="{{ route('grupos.assign_students', $grupo->id) }}" method="POST">
                @csrf

                <div class="p-6 border-b border-gray-200">
                     <h2 class="text-2xl font-bold text-gray-800">Administrar Alumnos Matriculados</h2>
                     <p class="text-sm text-gray-500 mt-1">
                         (Alumnos de la carrera: {{ $grupo->carrera->nombre }})
                     </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="w-16 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matricular</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matrícula</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($alumnos_disponibles as $alumno)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <input type="checkbox" name="alumnos_ids[]" value="{{ $alumno->id }}"
                                               {{ in_array($alumno->id, $alumnos_matriculados_ids) ? 'checked' : '' }}
                                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $alumno->matricula }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $alumno->nombre }} {{ $alumno->apellido_paterno }} {{ $alumno->apellido_materno }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <p class="text-lg text-gray-500">No hay alumnos disponibles en la carrera '{{ $grupo->carrera->nombre }}' para matricular.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(!$alumnos_disponibles->isEmpty())
                    <div class="bg-gray-50 px-6 py-4 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                            Actualizar Matrícula
                        </button>
                    </div>
                @endif

            </form>
        </div>

    </div>
</div>
@endsection
