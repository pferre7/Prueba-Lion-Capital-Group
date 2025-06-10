@extends('layouts.app')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-semibold mb-4">Mis Tareas</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap gap-4 mb-6">
        <form action="{{ route('tasks.index') }}" method="GET" class="flex space-x-2">
            <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1">
                <option value="">-- Todos --</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pendientes</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completadas</option>
            </select>
            <select name="sort" onchange="this.form.submit()" class="border rounded px-2 py-1">
                <option value="due_asc" {{ request('sort')=='due_asc'?'selected':'' }}>Vencimiento ↑</option>
                <option value="due_desc" {{ request('sort')=='due_desc'?'selected':'' }}>Vencimiento ↓</option>
            </select>
        </form>
    </div>

    <div class="space-y-4">
    @forelse($tasks as $task)
        <div class="flex justify-between items-start p-4 border rounded {{ $task->status === 'completed' ? 'bg-gray-100' : '' }}">
            <div class="flex-1">
                <h2 class="text-lg font-medium {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</h2>
                <p class="text-sm text-gray-600 mb-2">Vence: {{ $task->due_date->format('d/m/Y') }}</p>

                {{-- Formulario de compartir tarea --}}
                <form action="{{ route('tasks.share', $task) }}" method="POST" class="flex space-x-2 mb-2">
                    @csrf
                    <input type="email" name="email" placeholder="Email usuario" required class="border rounded px-2 py-1 flex-1">
                    <button type="submit" class="px-3 py-1 bg-indigo-500 hover:bg-indigo-600 text-white rounded">Compartir</button>
                </form>

            </div>
            <div class="flex items-center space-x-2">
                <button type="button" onclick="toggleComplete({{ $task->id }})" class="px-3 py-1 rounded {{ $task->status === 'completed' ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                    {{ $task->status === 'completed' ? 'Reabrir' : 'Completar' }}
                </button>

                @if(auth()->id() == $task->user_id)
                    <button type="button" onclick="showEditForm({{ $task->id }})" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded">Editar</button>
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded">Borrar</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Formulario de edición --}}
        <div id="editForm-{{ $task->id }}" class="hidden bg-gray-50 p-4 rounded">
            <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-2">
                @csrf @method('PUT')
                <input type="text" name="title" value="{{ $task->title }}" required class="w-full border rounded px-2 py-1">
                <textarea name="description" required class="w-full border rounded px-2 py-1">{{ $task->description }}</textarea>
                <input type="date" name="due_date" value="{{ $task->due_date->format('Y-m-d') }}" required class="w-full border rounded px-2 py-1">
                <div class="flex space-x-2">
                    <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded">Guardar</button>
                    <button type="button" onclick="document.getElementById('editForm-{{ $task->id }}').classList.add('hidden')" class="px-3 py-1 bg-gray-400 hover:bg-gray-500 text-white rounded">Cancelar</button>
                </div>
            </form>
        </div>
    @empty
        <p class="text-center text-gray-500">No tienes tareas creadas.</p>
    @endforelse
    </div>

    {{-- Formulario de creación --}}
    <div id="createForm" class="hidden bg-gray-50 p-6 rounded mt-6">
        <h2 class="text-xl font-semibold mb-4">Crear Tarea</h2>
        <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="text" name="title" placeholder="Título" required class="w-full border rounded px-2 py-1">
            <textarea name="description" placeholder="Descripción" required class="w-full border rounded px-2 py-1"></textarea>
            <input type="date" name="due_date" required class="w-full border rounded px-2 py-1">
            <div class="flex space-x-2">
                <button type="submit" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded">Crear</button>
                <button type="button" onclick="document.getElementById('createForm').classList.add('hidden')" class="px-3 py-1 bg-gray-400 hover:bg-gray-500 text-white rounded">Cancelar</button>
            </div>
        </form>
    </div>
</div>
@endsection