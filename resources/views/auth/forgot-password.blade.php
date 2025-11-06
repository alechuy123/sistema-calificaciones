@extends('layouts.app')

@section('title', 'Recuperar Contraseña')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Recuperar contraseña
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('password.email') }}" method="POST">
            @csrf
            
            <div class="rounded-md shadow-sm">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        value="{{ old('email') }}"
                        required 
                        autofocus
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('email') border-red-500 @enderror" 
                        placeholder="correo@ejemplo.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                    Enviar enlace de recuperación
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                    Volver al inicio de sesión
                </a>
            </div>
        </form>
    </div>
</div>
@endsection