<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::where('user_id', auth()->id());

        if ($request->status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($request->status === 'completed') {
            $query->where('status', 'completed');
        }

        if ($request->sort === 'due_desc') {
            $query->orderBy('due_date', 'desc');
        } else {
            $query->orderBy('due_date', 'asc');
        }

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
        $data['user_id'] = auth()->id();
        $data['status']  = 'pending';
        Task::create($data);

        return redirect()->route('tasks.index');
    }

    public function update(Request $request, Task $task)
    {
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

        $recipient = User::where('email', $request->email)->first();
        if (! $recipient) {
            return redirect()->route('tasks.index')
                            ->withErrors(['email' => 'Usuario no encontrado.']);
        }

        // Duplicar tarea al destinatario, copia carbon
        Task::create([
            'title'       => $task->title,
            'description' => $task->description,
            'due_date'    => $task->due_date,
            'user_id'     => $recipient->id,
            'status'      => 'pending',
        ]);

        return redirect()->route('tasks.index')
                        ->with('success', "Tarea compartida con {$recipient->email}");
    }
}