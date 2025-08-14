<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'created_by',
        'assigned_to',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'start_date',
        'estimated_hours',
        'actual_hours',
        'category',
        'project',
        'tags',
        'progress_percentage',
        'completion_notes',
        'is_recurring',
        'recurring_pattern',
        'parent_task_id'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'start_date' => 'datetime',
        'tags' => 'array',
        'progress_percentage' => 'integer',
        'is_recurring' => 'boolean',
        'recurring_pattern' => 'array'
    ];

    protected $appends = [
        'is_overdue', 
        'days_until_due', 
        'priority_color',
        'status_color',
        'completion_percentage',
        'time_spent_formatted'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_REVIEW = 'review';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_BLOCKED = 'blocked';

    /**
     * Priority constants
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Relations
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    /**
     * Accessors
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && 
               !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELED]) && 
               Carbon::now()->isAfter($this->due_date);
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date) return null;
        
        $now = Carbon::now();
        $dueDate = Carbon::parse($this->due_date);
        
        if ($now->isAfter($dueDate)) {
            return -$now->diffInDays($dueDate); // Négatif pour les tâches en retard
        }
        
        return $now->diffInDays($dueDate);
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => '#10B981',      // Vert
            self::PRIORITY_MEDIUM => '#F59E0B',   // Orange
            self::PRIORITY_HIGH => '#EF4444',     // Rouge
            self::PRIORITY_URGENT => '#7C2D12',   // Rouge foncé
            default => '#6B7280'                  // Gris
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => '#6B7280',      // Gris
            self::STATUS_IN_PROGRESS => '#3B82F6',  // Bleu
            self::STATUS_REVIEW => '#8B5CF6',       // Violet
            self::STATUS_COMPLETED => '#10B981',    // Vert
            self::STATUS_CANCELED => '#EF4444',     // Rouge
            self::STATUS_BLOCKED => '#F59E0B',      // Orange
            default => '#6B7280'
        };
    }

    public function getCompletionPercentageAttribute(): int
    {
        if ($this->status === self::STATUS_COMPLETED) {
            return 100;
        }
        
        return $this->progress_percentage ?? 0;
    }

    public function getTimeSpentFormattedAttribute(): string
    {
        if (!$this->actual_hours) return '0h';
        
        $hours = floor($this->actual_hours);
        $minutes = ($this->actual_hours - $hours) * 60;
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Scopes
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::now())
                    ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELED]);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', Carbon::today())
                    ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELED]);
    }

    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('due_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ])
                    ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELED]);
    }

    public function scopeByProject($query, string $project)
    {
        return $query->where('project', $project);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeCreatedBy($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeWithTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', [self::STATUS_IN_PROGRESS, self::STATUS_REVIEW]);
    }

    /**
     * Methods
     */
    public function markAsCompleted(string $notes = null): bool
    {
        $updated = $this->update([
            'status' => self::STATUS_COMPLETED,
            'progress_percentage' => 100,
            'completion_notes' => $notes,
            'completed_at' => Carbon::now()
        ]);

        if ($updated) {
            // Créer une notification pour l'assigné
            if ($this->assigned_to && $this->assigned_to !== $this->user_id) {
                $this->assignee->notifications()->create([
                    'type' => 'task_completed',
                    'data' => [
                        'message' => 'Tâche terminée',
                        'description' => "La tâche '{$this->title}' a été marquée comme terminée",
                        'task_id' => $this->id,
                        'task_title' => $this->title
                    ]
                ]);
            }

            // Gérer les tâches récurrentes
            if ($this->is_recurring && $this->recurring_pattern) {
                $this->createRecurringTask();
            }
        }

        return $updated;
    }

    public function updateProgress(int $percentage, string $notes = null): bool
    {
        $percentage = min(100, max(0, $percentage));
        
        $updated = $this->update([
            'progress_percentage' => $percentage,
            'status' => $percentage >= 100 ? self::STATUS_COMPLETED : $this->status
        ]);

        if ($updated && $percentage >= 100) {
            $this->markAsCompleted($notes);
        }

        return $updated;
    }

    public function assignTo(int $userId, string $notes = null): bool
    {
        $updated = $this->update([
            'assigned_to' => $userId,
            'status' => $this->status === self::STATUS_PENDING ? self::STATUS_IN_PROGRESS : $this->status
        ]);

        if ($updated) {
            // Créer une notification pour le nouvel assigné
            $assignee = User::find($userId);
            if ($assignee) {
                $assignee->notifications()->create([
                    'type' => 'task_assigned',
                    'data' => [
                        'message' => 'Nouvelle tâche assignée',
                        'description' => "La tâche '{$this->title}' vous a été assignée",
                        'task_id' => $this->id,
                        'task_title' => $this->title,
                        'notes' => $notes
                    ]
                ]);
            }
        }

        return $updated;
    }

    public function addTimeEntry(float $hours, string $description = null): void
    {
        $this->timeEntries()->create([
            'user_id' => auth()->id(),
            'hours' => $hours,
            'description' => $description,
            'logged_at' => Carbon::now()
        ]);

        // Mettre à jour le temps total
        $this->updateActualHours();
    }

    public function updateActualHours(): void
    {
        $totalHours = $this->timeEntries()->sum('hours');
        $this->update(['actual_hours' => $totalHours]);
    }

    public function duplicate(array $overrides = []): Task
    {
        $attributes = $this->toArray();
        
        // Supprimer les attributs qui ne doivent pas être dupliqués
        unset($attributes['id'], $attributes['created_at'], $attributes['updated_at'], $attributes['deleted_at']);
        
        // Appliquer les remplacements
        $attributes = array_merge($attributes, $overrides);
        
        // Modifier le titre pour indiquer que c'est une copie
        if (!isset($overrides['title'])) {
            $attributes['title'] = "Copie de {$this->title}";
        }
        
        // Réinitialiser le statut et le progrès
        $attributes['status'] = self::STATUS_PENDING;
        $attributes['progress_percentage'] = 0;
        $attributes['actual_hours'] = null;
        $attributes['completion_notes'] = null;

        return self::create($attributes);
    }

    protected function createRecurringTask(): void
    {
        if (!$this->is_recurring || !$this->recurring_pattern) {
            return;
        }

        $pattern = $this->recurring_pattern;
        $newDueDate = null;

        // Calculer la prochaine date d'échéance
        switch ($pattern['type']) {
            case 'daily':
                $newDueDate = $this->due_date->addDays($pattern['interval'] ?? 1);
                break;
            case 'weekly':
                $newDueDate = $this->due_date->addWeeks($pattern['interval'] ?? 1);
                break;
            case 'monthly':
                $newDueDate = $this->due_date->addMonths($pattern['interval'] ?? 1);
                break;
            case 'yearly':
                $newDueDate = $this->due_date->addYears($pattern['interval'] ?? 1);
                break;
        }

        if ($newDueDate) {
            $this->duplicate([
                'due_date' => $newDueDate,
                'start_date' => $this->start_date ? $this->start_date->addDays($newDueDate->diffInDays($this->due_date)) : null
            ]);
        }
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            // Définir l'utilisateur créateur si non défini
            if (!$task->created_by) {
                $task->created_by = auth()->id();
            }
            
            // Définir l'assigné par défaut
            if (!$task->assigned_to) {
                $task->assigned_to = $task->user_id;
            }
        });

        static::created(function ($task) {
            // Créer une notification pour l'utilisateur assigné (si différent du créateur)
            if ($task->assigned_to && $task->assigned_to !== $task->created_by) {
                $task->assignee->notifications()->create([
                    'type' => 'task_created',
                    'data' => [
                        'message' => 'Nouvelle tâche créée',
                        'description' => "Une nouvelle tâche '{$task->title}' vous a été assignée",
                        'task_id' => $task->id,
                        'task_title' => $task->title
                    ]
                ]);
            }
        });

        static::updating(function ($task) {
            $originalStatus = $task->getOriginal('status');
            
            // Si le statut change, créer une notification
            if ($task->isDirty('status') && $originalStatus !== $task->status) {
                $statusLabels = [
                    self::STATUS_PENDING => 'En attente',
                    self::STATUS_IN_PROGRESS => 'En cours',
                    self::STATUS_REVIEW => 'En révision',
                    self::STATUS_COMPLETED => 'Terminée',
                    self::STATUS_CANCELED => 'Annulée',
                    self::STATUS_BLOCKED => 'Bloquée'
                ];

                $task->user->notifications()->create([
                    'type' => 'task_status_changed',
                    'data' => [
                        'message' => 'Statut de tâche modifié',
                        'description' => "La tâche '{$task->title}' est maintenant: {$statusLabels[$task->status]}",
                        'task_id' => $task->id,
                        'task_title' => $task->title,
                        'old_status' => $originalStatus,
                        'new_status' => $task->status
                    ]
                ]);
            }
        });
    }
}