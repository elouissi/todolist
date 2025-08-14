<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Support\Carbon;

class TaskRepository
{
    public function allForUser($userId)
    {
        return Task::where('user_id', $userId)->latest()->get();
    }

    public function find($id)
    {
        return Task::find($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function getOverdueTasks($userId)
    {
        return Task::where('user_id', $userId)
            ->where('status', '!=', 'completed')
            ->where('due_date', '<', Carbon::now())
            ->latest()
            ->get();
    }

    public function getByStatus($userId, $status)
    {
        return Task::where('user_id', $userId)
            ->where('status', $status)
            ->latest()
            ->get();
    }

    public function getByPriority($userId, $priority)
    {
        return Task::where('user_id', $userId)
            ->where('priority', $priority)
            ->latest()
            ->get();
    }
} 