@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1>dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>