<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'service_type',
        'budget',
        'final_price',
        'deadline',
        'requirements',
        'contact_phone',
        'contact_email',
        'status',
        'progress',
        'notes',
        'started_at',
        'completed_at',
        'files',
        'technologies',
        'features',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'deadline' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'budget' => 'decimal:2',
        'final_price' => 'decimal:2',
        'progress' => 'integer',
        'files' => 'array',
        'technologies' => 'array',
        'features' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'deleted_at',
        'deadline',
        'started_at',
        'completed_at',
    ];

    /**
     * Project status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_REVIEW = 'review';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ON_HOLD = 'on_hold';

    /**
     * Get all available statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Ожидает',
            self::STATUS_IN_PROGRESS => 'В работе',
            self::STATUS_REVIEW => 'На проверке',
            self::STATUS_COMPLETED => 'Завершен',
            self::STATUS_CANCELLED => 'Отменен',
            self::STATUS_ON_HOLD => 'Приостановлен',
        ];
    }

    /**
     * Get the user that owns the project.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service for this project.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_type', 'slug');
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? 'Неизвестно';
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            self::STATUS_PENDING => 'yellow',
            self::STATUS_IN_PROGRESS => 'blue',
            self::STATUS_REVIEW => 'purple',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
            self::STATUS_ON_HOLD => 'gray',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Check if project is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_REVIEW
        ]);
    }

    /**
     * Check if project is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Get formatted budget.
     */
    public function getFormattedBudgetAttribute(): string
    {
        if (!$this->budget) {
            return 'Не указан';
        }
        return number_format($this->budget, 0, ',', ' ') . ' ₽';
    }

    /**
     * Get formatted final price.
     */
    public function getFormattedFinalPriceAttribute(): string
    {
        if (!$this->final_price) {
            return 'Не указана';
        }
        return number_format($this->final_price, 0, ',', ' ') . ' ₽';
    }

    /**
     * Get days until deadline.
     */
    public function getDaysUntilDeadlineAttribute(): ?int
    {
        if (!$this->deadline) {
            return null;
        }
        return Carbon::now()->diffInDays($this->deadline, false);
    }

    /**
     * Check if project is overdue.
     */
    public function isOverdue(): bool
    {
        if (!$this->deadline || $this->isCompleted()) {
            return false;
        }
        return Carbon::now()->isAfter($this->deadline);
    }

    /**
     * Scope for active projects.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_REVIEW
        ]);
    }

    /**
     * Scope for completed projects.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for overdue projects.
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', Carbon::now())
                    ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Scope by service type.
     */
    public function scopeByService($query, string $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }
}
