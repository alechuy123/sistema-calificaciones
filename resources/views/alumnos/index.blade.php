@extends('layouts.app')

@section('title', 'Gestión de Alumnos')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Alumnos</h1>
            <a href="{{ route('alumnos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                <span class="font-semibold">Registrar Nuevo Alumno</span>
            </a>
        </div>

        @if ($message = Session::get('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                <p class="font-semibold">{{ $message }}</p>
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            @if($alumnos->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-lg text-gray-600">No hay alumnos registrados.</p>
                    <a href="{{ route('alumnos.create') }}" class="mt-4 inline-block px-5 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                        ¡Registra el primero!
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matrícula</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Carrera</th>

                                <!-- --- ¡NUEVA COLUMNA! --- -->
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
                                <!-- --- FIN NUEVA COLUMNA --- -->

                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciclo Escolar</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($alumnos as $alumno)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $alumno->matricula }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $alumno->nombre }} {{ $alumno->apellido_paterno }} {{ $alumno->apellido_materno }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $alumno->carrera->nombre }}</td>

                                    <!-- --- ¡NUEVA CELDA! --- -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{--
                                          $alumno->grupos es una colección (incluso si solo tiene 1)
                                          Usamos first() para tomar el primer grupo.
                                          Usamos @if para evitar errores si no tiene grupo.
                                        --}}
                                        @if($grupo = $alumno->grupos->first())
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $grupo->nombre }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Sin grupo</span>
                                        @endif
                                    </td>
                                    <!-- --- FIN NUEVA CELDA --- -->

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $alumno->cicloEscolar->nombre }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($alumno->esta_activo)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                ✅ Activo
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                ❌ Desactivado
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-4">
                                        <a href="{{ route('alumnos.edit', $alumno->id) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>

                                        @if ($alumno->esta_activo)
                                            <form action="{{ route('alumnos.destroy', $alumno->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('¿Quieres DESACTIVAR a este alumno?')" class="text-red-600 hover:text-red-900">
                                                    Desactivar
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('alumnos.update', $alumno->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                {{-- Campos ocultos para pasar la validación y reactivar --}}
                                                <input type="hidden" name="esta_activo" value="1">
                                                <input type="hidden" name="nombre" value="{{ $alumno->nombre }}">
                                                <input type="hidden" name="apellido_paterno" value="{{ $alumno->apellido_paterno }}">
                                                <input type="hidden" name="apellido_materno" value="{{ $alumno->apellido_materno }}">
                                                <input type="hidden" name="matricula" value="{{ $alumno->matricula }}">
                                                <input type="hidden" name="carrera_id" value="{{ $alumno->carrera_id }}">
                                                <input type="hidden" name="ciclo_escolar_id" value="{{ $alumno->ciclo_escolar_id }}">

                                                <button type="submit" onclick="return confirm('¿Quieres REACTIVAR a este alumno?')" class="text-blue-600 hover:text-blue-900">
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
