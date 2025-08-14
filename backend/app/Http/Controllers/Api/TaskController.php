<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Events\TaskDeleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index()
    {
        $tasks = $this->taskService->allForUser(Auth::id());
        return response()->json(['data' => $tasks]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $task = $this->taskService->create($data);
        
        // Create notification
        Auth::user()->notifications()->create([
            'type' => 'task_created',
            'data' => [
                'message' => 'Nouvelle tâche créée',
                'description' => "La tâche '{$task->title}' a été créée avec succès",
                'task_id' => $task->id,
                'task_title' => $task->title
            ]
        ]);
        
        // event(new TaskCreated($task)); // Temporarily disabled for Redis
        return response()->json(['data' => $task, 'message' => 'Task created successfully'], 201);
    }

    public function show($id)
    {
        $task = $this->taskService->find($id);
        if (!$task || $task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Not found'], 404);
        }
        return response()->json(['data' => $task]);
    }

    public function update(Request $request, $id)
    {
        $task = $this->taskService->find($id);
        if (!$task || $task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,in_progress,completed,canceled',
            'priority' => 'sometimes|required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $task = $this->taskService->update($task, $request->all());
        
        // Create notification
        Auth::user()->notifications()->create([
            'type' => 'task_updated',
            'data' => [
                'message' => 'Tâche mise à jour',
                'description' => "La tâche '{$task->title}' a été mise à jour",
                'task_id' => $task->id,
                'task_title' => $task->title
            ]
        ]);
        
        // event(new TaskUpdated($task)); // Temporarily disabled for Redis
        return response()->json(['data' => $task, 'message' => 'Task updated successfully']);
    }

    public function destroy($id)
    {
        $task = $this->taskService->find($id);
        if (!$task || $task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Not found'], 404);
        }
        
        $taskTitle = $task->title;
        $this->taskService->delete($task);
        
        // Create notification
        Auth::user()->notifications()->create([
            'type' => 'task_deleted',
            'data' => [
                'message' => 'Tâche supprimée',
                'description' => "La tâche '{$taskTitle}' a été supprimée",
                'task_id' => $id
            ]
        ]);
        
        // event(new TaskDeleted($id, Auth::id())); // Temporarily disabled for Redis
        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function statistics()
    {
        $stats = $this->taskService->getStatistics(Auth::id());
        return response()->json(['data' => $stats]);
    }

    public function getOverdue()
    {
        $tasks = $this->taskService->getOverdueTasks(Auth::id());
        return response()->json(['data' => $tasks]);
    }

    public function getByStatus($status)
    {
        $tasks = $this->taskService->getByStatus(Auth::id(), $status);
        return response()->json(['data' => $tasks]);
    }

    public function getByPriority($priority)
    {
        $tasks = $this->taskService->getByPriority(Auth::id(), $priority);
        return response()->json(['data' => $tasks]);
    }

    public function updateStatus(Request $request, $id)
    {
        $task = $this->taskService->find($id);
        if (!$task || $task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_progress,completed,canceled',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $oldStatus = $task->status;
        $task = $this->taskService->update($task, ['status' => $request->status]);
        
        // Create notification for status change
        $statusLabels = [
            'pending' => 'À faire',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'canceled' => 'Annulé'
        ];
        
        Auth::user()->notifications()->create([
            'type' => 'task_updated',
            'data' => [
                'message' => 'Statut de tâche mis à jour',
                'description' => "La tâche '{$task->title}' est maintenant {$statusLabels[$request->status]}",
                'task_id' => $task->id,
                'task_title' => $task->title,
                'old_status' => $oldStatus,
                'new_status' => $request->status
            ]
        ]);
        
        // event(new TaskUpdated($task)); // Temporarily disabled for Redis
        
        return response()->json(['data' => $task, 'message' => 'Task status updated successfully']);
    }
} 