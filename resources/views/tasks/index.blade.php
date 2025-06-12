@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-semibold mb-4">Mis Tareas</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap gap-4 mb-6">
        <form action="{{ route('tasks.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <select name="status" onchange="this.form.submit()" class="border rounded px-3 py-2 text-sm w-full sm:w-auto">
                <option value="">-- Todos --</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pendientes</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completadas</option>
            </select>
            <select name="sort" onchange="this.form.submit()" class="border rounded px-3 py-2 text-sm w-full sm:w-auto">
                <option value="due_asc" {{ request('sort')=='due_asc'?'selected':'' }}>Vencimiento ↑</option>
                <option value="due_desc" {{ request('sort')=='due_desc'?'selected':'' }}>Vencimiento ↓</option>
            </select>
        </form>
    </div>

    <div class="space-y-4">
    @forelse($tasks as $task)
        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="text-lg font-medium">
                {{ $task->title }}
                @if(auth()->id() !== $task->owner_id)
                    <span class="text-sm text-gray-500">(Compartida)</span>
                @endif
            </h2>
            <p class="text-sm text-gray-600 mb-2">Vence: {{ $task->due_date->format('d/m/Y') }}</p>

            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-2 space-y-2 sm:space-y-0 mb-3">
                <button type="button"
                    onclick="toggleComplete({{ $task->id }}, this)"
                    data-status="{{ $task->status }}"
                    class="w-full sm:w-auto px-4 py-2 rounded text-white text-sm font-semibold transition
                        {{ $task->status === 'completed' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }}">
                    {{ $task->status === 'completed' ? 'Reabrir' : 'Completar' }}
                </button>
                <meta name="csrf-token" content="{{ csrf_token() }}">

                @if(auth()->id() === $task->owner_id)
                    <button type="button"
                        onclick="showEditForm({{ $task->id }})"
                        class="w-full sm:w-auto px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm font-semibold transition">
                        Editar
                    </button>

                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="w-full sm:w-auto">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="w-full sm:w-auto px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-semibold transition">
                            Borrar
                        </button>
                    </form>
                @endif
            </div>

            @if(auth()->id() === $task->owner_id)
                <form action="{{ route('tasks.share', $task) }}" method="POST" class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
                    @csrf
                    <input type="email" name="email" placeholder="Email usuario" required
                           class="flex-1 border rounded px-3 py-2 text-sm w-full sm:w-auto">
                    <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded text-sm font-semibold transition">
                        Compartir
                    </button>
                </form>
            @endif
        </div>

        @if(auth()->id() === $task->owner_id)
            <div id="editForm-{{ $task->id }}" class="hidden bg-gray-50 p-4 rounded">
                <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-3">
                    @csrf @method('PUT')
                    <input type="text" name="title" value="{{ $task->title }}" required class="w-full border rounded px-3 py-2 text-sm">
                    <textarea name="description" required class="w-full border rounded px-3 py-2 text-sm">{{ $task->description }}</textarea>
                    <input type="date" name="due_date" value="{{ $task->due_date->format('Y-m-d') }}" required class="w-full border rounded px-3 py-2 text-sm">
                    <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded text-sm font-semibold">Guardar</button>
                        <button type="button" onclick="document.getElementById('editForm-{{ $task->id }}').classList.add('hidden')" class="w-full sm:w-auto px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded text-sm font-semibold">Cancelar</button>
                    </div>
                </form>
            </div>
        @endif
    @empty
        <p class="text-center text-gray-500">No tienes tareas disponibles.</p>
    @endforelse
    </div>

    {{-- Formulario de creación --}}
    <div id="createForm" class="hidden bg-gray-50 p-6 rounded mt-6">
        <h2 class="text-xl font-semibold mb-4">Crear Tarea</h2>
        <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="text" name="title" placeholder="Título" required class="w-full border rounded px-3 py-2 text-sm">
            <textarea name="description" placeholder="Descripción" required class="w-full border rounded px-3 py-2 text-sm"></textarea>
            <input type="date" name="due_date" required class="w-full border rounded px-3 py-2 text-sm">
            <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm font-semibold">Crear</button>
                <button type="button" onclick="document.getElementById('createForm').classList.add('hidden')" class="w-full sm:w-auto px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded text-sm font-semibold">Cancelar</button>
            </div>
        </form>
    </div>
</div>
@endsection
