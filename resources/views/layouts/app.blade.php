<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Tareas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
<header class="bg-gray-800 text-white p-4 shadow">
    <nav class="container mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-4">
            @auth
                <button type="button" onclick="showCreateForm()" class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded">Nueva Tarea</button>
            @endauth
        </div>
        <div>
            @auth
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 px-3 py-1 rounded">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="bg-green-500 hover:bg-green-600 px-3 py-1 rounded">Login</a>
                <a href="{{ route('register') }}" class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded ml-2">Registro</a>
            @endauth
        </div>
    </nav>
</header>
<main class="container mx-auto flex-grow py-6">
    @yield('content')
</main>
<script>
    function toggleComplete(id) {
        fetch(`${window.location.origin}/tasks/${id}/toggle`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(response.statusText);
            return response;
        })
        .then(() => location.reload())
        .catch(error => console.error('Toggle failed:', error));
    }
    function showCreateForm() {
        document.getElementById('createForm').classList.remove('hidden');
    }
    function showEditForm(id) {
        document.getElementById(`editForm-${id}`).classList.remove('hidden');
    }
</script>
</body>
</html>
