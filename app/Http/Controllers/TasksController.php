<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::where(function($q) {
                $q->where('owner_id', auth()->id())
                ->orWhereHas('sharedWith', function($q2) {
                    $q2->where('user_id', auth()->id());
                });
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $direction = $request->sort === 'due_desc' ? 'desc' : 'asc';
        $query->orderBy('due_date', $direction);

        $tasks = $query->get();

        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'due_date'    => 'required|date',
        ]);
        $data['user_id']  = auth()->id();
        $data['owner_id'] = auth()->id();
        $data['status']   = 'pending';
        Task::create($data);

        return redirect()->route('tasks.index');
    }

    public function update(Request $request, Task $task)
    {
        // Solo el owner original puede editar
        if (auth()->id() !== $task->owner_id) {
            abort(403);
        }

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'due_date'    => 'required|date',
        ]);

        $task->update($data);

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        // Solo el owner original puede borrar
        if (auth()->id() !== $task->owner_id) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index');
    }

    public function toggle(Task $task)
    {
        $task->update(['status' => $task->status === 'completed' ? 'pending' : 'completed']);

        return response()->noContent();
    }

    public function share(Request $request, Task $task)
    {
        $request->validate(['email' => 'required|email']);
        $recipient = User::where('email', $request->email)->firstOrFail();

        // Vincular usuario a la tarea sin duplicar
        $task->sharedWith()->syncWithoutDetaching([
            $recipient->id
        ]);

        return redirect()->route('tasks.index')
                         ->with('success', "Tarea compartida con {$recipient->email}");
    }
}