@extends('layouts.app')

@section('title', 'Gestión de Carreras')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Carreras</h1>
            <a href="{{ route('carreras.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                <span class="font-semibold">Crear Nueva Carrera</span>
            </a>
        </div>

        @if ($message = Session::get('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                <p class="font-semibold">{{ $message }}</p>
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            @if($carreras->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-lg text-gray-600">No hay carreras registradas.</p>
                    <a href="{{ route('carreras.create') }}" class="mt-4 inline-block px-5 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                        ¡Registra la primera!
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($carreras as $carrera)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $carrera->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $carrera->nombre }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $carrera->descripcion }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($carrera->esta_activo)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                ✅ Activa
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                ❌ Desactivada
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-4">
                                        <a href="{{ route('carreras.edit', $carrera->id) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>

                                        @if ($carrera->esta_activo)
                                            <form action="{{ route('carreras.destroy', $carrera->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('¿Quieres DESACTIVAR esta carrera?')" class="text-red-600 hover:text-red-900">
                                                    Desactivar
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('carreras.update', $carrera->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                {{-- Campos ocultos para pasar la validación y reactivar --}}
                                                <input type="hidden" name="esta_activo" value="1">
                                                <input type="hidden" name="nombre" value="{{ $carrera->nombre }}">
                                                <input type="hidden" name="descripcion" value="{{ $carrera->descripcion }}">

                                                <button type="submit" onclick="return confirm('¿Quieres REACTIVAR esta carrera?')" class="text-blue-600 hover:text-blue-900">
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
