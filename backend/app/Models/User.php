<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'avatar',
        'phone_number',
        'address',
        'job_title',
        'department',
        'preferences',
        'last_active_at',
        'timezone',
        'language'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
        'preferences' => 'array',
        'password' => 'hashed',
    ];

    protected $appends = ['avatar_url', 'initials', 'is_online'];

    /**
     * Relations
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /**
     * Accessors
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            $avatarPath = str_replace('avatars/', '', $this->avatar);
            $relativeUrl = Storage::url('avatars/' . $avatarPath);
            return config('app.url') . $relativeUrl;
        }
        
        // Retourner un avatar par défaut avec les initiales
        return "https://ui-avatars.com/api/?name=" . urlencode($this->full_name) . "&background=0ea5e9&color=fff&size=200";
    }

    public function getInitialsAttribute(): string
    {
        $names = explode(' ', trim($this->full_name));
        $initials = '';
        
        foreach (array_slice($names, 0, 2) as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }
        
        return $initials ?: 'TF'; // TaskFlow par défaut
    }

    public function getIsOnlineAttribute(): bool
    {
        return $this->last_active_at && 
               $this->last_active_at->diffInMinutes(Carbon::now()) <= 5;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('last_active_at', '>=', Carbon::now()->subMinutes(5));
    }

    public function scopeByDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    public function scopeWithTasksCount($query)
    {
        return $query->withCount([
            'tasks',
            'tasks as pending_tasks_count' => function ($query) {
                $query->where('status', 'pending');
            },
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'completed');
            }
        ]);
    }

    /**
     * Methods
     */
    public function markAsActive(): void
    {
        $this->update(['last_active_at' => Carbon::now()]);
    }

    public function getTasksStats(): array
    {
        $tasks = $this->tasks();
        
        return [
            'total' => $tasks->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'overdue' => $tasks->where('due_date', '<', Carbon::now())
                             ->whereNotIn('status', ['completed', 'canceled'])
                             ->count(),
            'completion_rate' => $this->getCompletionRate()
        ];
    }

    public function getCompletionRate(): float
    {
        $total = $this->tasks()->count();
        
        if ($total === 0) return 0.0;
        
        $completed = $this->tasks()->where('status', 'completed')->count();
        
        return round(($completed / $total) * 100, 2);
    }

    public function updatePreferences(array $preferences): void
    {
        $currentPreferences = $this->preferences ?? [];
        $this->update([
            'preferences' => array_merge($currentPreferences, $preferences)
        ]);
    }

    public function getPreference(string $key, $default = null)
    {
        return data_get($this->preferences, $key, $default);
    }

    /**
     * JWT Methods
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'user_id' => $this->id,
            'email' => $this->email,
            'full_name' => $this->full_name,
            'department' => $this->department,
            'timezone' => $this->timezone ?? 'UTC'
        ];
    }

    /**
     * Unread notifications
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('read_at');
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Définir des préférences par défaut
            $user->preferences = array_merge([
                'theme' => 'light',
                'language' => 'fr',
                'notifications' => [
                    'email' => true,
                    'push' => true,
                    'task_reminders' => true,
                    'task_assignments' => true
                ],
                'dashboard' => [
                    'show_completed_tasks' => false,
                    'default_view' => 'kanban'
                ]
            ], $user->preferences ?? []);

            $user->timezone = $user->timezone ?? 'Europe/Paris';
            $user->language = $user->language ?? 'fr';
        });

        static::created(function ($user) {
            // Créer une notification de bienvenue
            $user->notifications()->create([
                'type' => 'welcome',
                'data' => [
                    'message' => 'Bienvenue sur TaskFlow Pro',
                    'description' => 'Votre compte a été créé avec succès. Commencez par créer votre première tâche !',
                    'action_url' => '/dashboard'
                ]
            ]);
        });
    }
}