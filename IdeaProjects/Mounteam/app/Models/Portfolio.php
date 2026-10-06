<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Portfolio extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'service_type',
        'client_name',
        'client_website',
        'project_url',
        'image',
        'gallery',
        'technologies',
        'features',
        'duration',
        'budget_range',
        'completion_date',
        'is_published',
        'is_featured',
        'meta_title',
        'meta_description',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'completion_date' => 'date',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'gallery' => 'array',
        'technologies' => 'array',
        'features' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'deleted_at',
        'completion_date',
    ];

    /**
     * Get the service for this portfolio item.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_type', 'slug');
    }

    /**
     * Get the main image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/portfolio-placeholder.jpg');
    }

    /**
     * Get gallery images URLs.
     */
    public function getGalleryUrlsAttribute(): array
    {
        if (!$this->gallery) {
            return [];
        }

        return array_map(function ($image) {
            return asset('storage/' . $image);
        }, $this->gallery);
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration) {
            return 'Не указано';
        }

        $duration = (int) $this->duration;

        if ($duration < 7) {
            return $duration . ' ' . Str::plural('день', $duration);
        } elseif ($duration < 30) {
            $weeks = round($duration / 7);
            return $weeks . ' ' . Str::plural('неделя', $weeks);
        } else {
            $months = round($duration / 30);
            return $months . ' ' . Str::plural('месяц', $months);
        }
    }

    /**
     * Get budget range label.
     */
    public function getBudgetRangeLabelAttribute(): string
    {
        $ranges = [
            'small' => 'До 50 000 ₽',
            'medium' => '50 000 - 150 000 ₽',
            'large' => '150 000 - 500 000 ₽',
            'enterprise' => 'От 500 000 ₽',
        ];

        return $ranges[$this->budget_range] ?? 'Не указан';
    }

    /**
     * Get technologies as comma-separated string.
     */
    public function getTechnologiesStringAttribute(): string
    {
        if (!$this->technologies) {
            return '';
        }
        return implode(', ', $this->technologies);
    }

    /**
     * Get excerpt from description.
     */
    public function getExcerptAttribute(): string
    {
        if ($this->short_description) {
            return $this->short_description;
        }
        return Str::limit(strip_tags($this->description), 150);
    }

    /**
     * Generate slug from title.
     */
    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = $value;
        if (!$this->slug) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    /**
     * Scope for published items.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for featured items.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope by service type.
     */
    public function scopeByService($query, string $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    /**
     * Scope by technology.
     */
    public function scopeByTechnology($query, string $technology)
    {
        return $query->whereJsonContains('technologies', $technology);
    }

    /**
     * Scope ordered by sort order and creation date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Get route key name for model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
