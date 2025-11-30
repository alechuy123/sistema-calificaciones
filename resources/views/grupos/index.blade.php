@extends('layouts.app')

@section('title', 'Gestión de Grupos')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Grupos</h1>
            <a href="{{ route('grupos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                <span class="font-semibold">Crear Nuevo Grupo</span>
            </a>
        </div>

        @if ($message = Session::get('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                <p class="font-semibold">{{ $message }}</p>
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            @if($grupos->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-lg text-gray-600">No hay grupos registrados.</p>
                    <a href="{{ route('grupos.create') }}" class="mt-4 inline-block px-5 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                        ¡Crea el primero!
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Carrera</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materias Asignadas</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuatrimestre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($grupos as $grupo)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $grupo->nombre }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $grupo->carrera->nombre }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($grupo->materias as $materia)
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $materia->nombre }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-500">Sin materias asignadas</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $grupo->cuatrimestre->nombre }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($grupo->esta_activo)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                ✅ Activo
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                ❌ Desactivado
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-3">

                                        <a href="{{ route('grupos.promover.form', $grupo->id) }}" class="text-green-600 hover:text-green-900 font-semibold" title="Promover al siguiente cuatrimestre">
                                            Promover
                                        </a>

                                        <a href="{{ route('grupos.show', $grupo->id) }}" class="text-purple-600 hover:text-purple-900 font-semibold" title="Ver detalles y alumnos">
                                            Ver Detalles
                                        </a>
                                        <a href="{{ route('grupos.edit', $grupo->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar Grupo">Editar</a>

                                        @if ($grupo->esta_activo)
                                            <form action="{{ route('grupos.destroy', $grupo->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('¿Quieres DESACTIVAR este grupo?')" class="text-red-600 hover:text-red-900" title="Desactivar">
                                                    Desactivar
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('grupos.update', $grupo->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                
                                                <input type="hidden" name="esta_activo" value="1">
                                                <input type="hidden" name="nombre" value="{{ $grupo->nombre }}">
                                                @foreach($grupo->materias as $materia)
                                                    <input type="hidden" name="materias[]" value="{{ $materia->id }}">
                                                @endforeach
                                                <input type="hidden" name="cuatrimestre_id" value="{{ $grupo->cuatrimestre_id }}">
                                                <input type="hidden" name="carrera_id" value="{{ $grupo->carrera_id }}">

                                                <button type="submit" onclick="return confirm('¿Quieres REACTIVAR este grupo?')" class="text-blue-600 hover:text-blue-900" title="Reactivar">
                                                    Reactivar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection