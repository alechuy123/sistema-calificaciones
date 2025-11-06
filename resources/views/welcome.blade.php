@extends('layouts.app')

@section('title', 'Panel Principal')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-10">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Panel de Control</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white shadow-lg rounded-xl p-6 flex items-center space-x-4">
                    <span class="text-4xl">🗓️</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Cuatrimestre Actual</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ $cuatrimestreActual ? $cuatrimestreActual->nombre : 'Ninguno' }}
                        </p>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-xl p-6 flex items-center space-x-4">
                    <span class="text-4xl">👤</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Alumnos Activos</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $alumnosActivos }}</p>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-xl p-6 flex items-center space-x-4">
                    <span class="text-4xl">👥</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Grupos Activos</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $gruposActivos }}</p>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-xl p-6 flex items-center space-x-4">
                    <span class="text-4xl">📚</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Carreras Activas</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $carrerasActivas }}</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-12">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Accesos Directos</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <a href="{{ route('alumnos.index') }}"
                   class="md:col-span-1 flex items-center p-6 bg-white rounded-xl shadow-lg transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl border-l-4 border-blue-500">
                    <span class="text-3xl">👤</span>
                    <span class="ml-4 text-xl font-semibold text-gray-800">Registro de Alumnos</span>
                </a>

                <a href="{{ route('grupos.index') }}"
                   class="md:col-span-1 flex items-center p-6 bg-white rounded-xl shadow-lg transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl border-l-4 border-purple-500">
                    <span class="text-3xl">👥</span>
                    <span class="ml-4 text-xl font-semibold text-gray-800">Gestión de Grupos</span>
                </a>

                <div class="md:col-span-2 p-6 bg-white rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Catálogos del Sistema</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <a href="{{ route('carreras.index') }}"
                           class="flex items-center p-4 bg-gray-50 rounded-lg transition-all duration-300 ease-in-out hover:bg-gray-100 hover:shadow-md">
                            <span class="text-2xl">📚</span>
                            <span class="ml-3 font-medium text-gray-700">Carreras</span>
                        </a>

                        <a href="{{ route('materias.index') }}"
                           class="flex items-center p-4 bg-gray-50 rounded-lg transition-all duration-300 ease-in-out hover:bg-gray-100 hover:shadow-md">
                            <span class="text-2xl">📘</span>
                            <span class="ml-3 font-medium text-gray-700">Materias</span>
                        </a>

                        <a href="{{ route('cuatrimestres.index') }}"
                           class="flex items-center p-4 bg-gray-50 rounded-lg transition-all duration-300 ease-in-out hover:bg-gray-100 hover:shadow-md">
                            <span class="text-2xl">🗓️</span>
                            <span class="ml-3 font-medium text-gray-700">Cuatrimestres</span>
                        </a>

                        <a href="{{ route('ciclos.index') }}"
                           class="flex items-center p-4 bg-gray-50 rounded-lg transition-all duration-300 ease-in-out hover:bg-gray-100 hover:shadow-md">
                            <span class="text-2xl">📅</span>
                            <span class="ml-3 font-medium text-gray-700">Ciclos Escolares</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
