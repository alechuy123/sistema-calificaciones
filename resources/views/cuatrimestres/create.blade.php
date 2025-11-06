@extends('layouts.app')

@section('title', 'Registrar Cuatrimestre')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Registrar Nuevo Cuatrimestre</h1>
            <a href="{{ route('cuatrimestres.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 transition duration-300">
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

                <form action="{{ route('cuatrimestres.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Cuatrimestre</label>
                        <input type="text" name="nombre" id="nombre" placeholder="Ej: Enero-Abril 2026" value="{{ old('nombre') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de Fin</LAbel>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" name="esta_activo" id="esta_activo" value="1" {{ old('esta_activo') ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="esta_activo" class="ml-2 block text-sm font-medium text-gray-900">Activo (Cuatrimestre Actual)</label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">(Si marcas esta opción, los demás cuatrimestres se desactivarán.)</p>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                            Guardar Cuatrimestre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
