@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center h-full">
    <div class="text-center bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-4xl font-bold mb-4">Bienvenido a tu Gestor de Tareas</h1>
        <p class="text-gray-600 mb-6">Organiza tus tareas, establece metas y mejora tu productividad.</p>
        <a href="{{ route('login') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Login</a>
    </div>
</div>
@endsection