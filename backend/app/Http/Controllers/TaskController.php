<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    // GET /api/v1/tasks
    public function index(Request $request)
    {
        $query = Task::query();

        //  Status filter (optional)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_date', 'asc')
            ->get();

        //  Empty state handling
        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'No tasks found.'
            ], 404);
        }

        return response()->json([
            'data' => $tasks
        ]);
    }

    // POST /api/v1/tasks
    public function store(Request $request)
    {
        $request->validate([
            'title' => [
                'required',
                'string',
                Rule::unique('tasks')->where(function ($query) use ($request) {
                    return $query->where('due_date', $request->due_date);
                }),
            ],
            'due_date' => 'required|date|after_or_equal:today',
            'priority' => 'required|in:low,medium,high',
        ]);

        $task = Task::create($request->all());

        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    // PATCH /api/v1/tasks/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,done',
        ]);

        $task = Task::findOrFail($id);

        if (!$task->canTransitionTo($request->status)) {
            return response()->json([
                'error' => 'Invalid status transition'
            ], 400);
        }

        $task->status = $request->status;
        $task->save();

        return response()->json([
            'message' => 'Status updated successfully',
            'data' => $task
        ]);
    }

    // DELETE /api/v1/tasks/{id}
    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if ($task->status !== 'done') {
            return response()->json([
                'error' => 'Only completed tasks can be deleted'
            ], 403); // 
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

    // GET /api/v1/tasks/report?date=YYYY-MM-DD
    public function report(Request $request)
    {
        //  Validation added
        $request->validate([
            'date' => 'nullable|date|date_format:Y-m-d'
        ]);

        $date = $request->query('date', now()->toDateString());

        $tasks = Task::whereDate('due_date', $date)->get();

        $summary = [
            'high' => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
            'medium' => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
            'low' => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
        ];

        foreach ($tasks as $task) {
            $summary[$task->priority][$task->status]++;
        }

        return response()->json([
            'date' => $date,
            'summary' => $summary
        ]);
    }
}