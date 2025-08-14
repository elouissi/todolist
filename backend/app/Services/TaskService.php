<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;

class TaskService
{
    protected $tasks;

    public function __construct(TaskRepository $tasks)
    {
        $this->tasks = $tasks;
    }

    public function allForUser($userId)
    {
        return $this->tasks->allForUser($userId);
    }

    public function find($id)
    {
        return $this->tasks->find($id);
    }

    public function create(array $data): Task
    {
        return $this->tasks->create($data);
    }

    public function update(Task $task, array $data): Task
    {
        return $this->tasks->update($task, $data);
    }

    public function delete(Task $task): bool
    {
        return $this->tasks->delete($task);
    }

    public function getStatistics($userId)
    {
        $tasks = $this->tasks->allForUser($userId);
        
        $total = $tasks->count();
        $pending = $tasks->where('status', 'pending')->count();
        $inProgress = $tasks->where('status', 'in_progress')->count();
        $completed = $tasks->where('status', 'completed')->count();
        $canceled = $tasks->where('status', 'canceled')->count();
        
        // Calculate overdue tasks
        $overdue = $tasks->filter(function ($task) {
            if ($task->status === 'completed') return false;
            return $task->due_date && now()->isAfter($task->due_date);
        })->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'in_progress' => $inProgress,
            'completed' => $completed,
            'canceled' => $canceled,
            'overdue' => $overdue
        ];
    }

    public function getOverdueTasks($userId)
    {
        return $this->tasks->getOverdueTasks($userId);
    }

    public function getByStatus($userId, $status)
    {
        return $this->tasks->getByStatus($userId, $status);
    }

    public function getByPriority($userId, $priority)
    {
        return $this->tasks->getByPriority($userId, $priority);
    }
} 