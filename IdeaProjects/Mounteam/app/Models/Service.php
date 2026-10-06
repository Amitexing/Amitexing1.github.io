<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'icon',
        'image',
        'features',
        'pricing',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'features' => 'array',
        'pricing' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'deleted_at',
    ];

    /**
     * Get projects for this service.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'service_type', 'slug');
    }

    /**
     * Get portfolio items for this service.
     */
    public function portfolioItems(): HasMany
    {
        return $this->hasMany(Portfolio::class, 'service_type', 'slug');
    }

    /**
     * Get the icon URL.
     */
    public function getIconUrlAttribute(): string
    {
        if ($this->icon) {
            return asset('storage/' . $this->icon);
        }
        return asset('images/service-icon-default.svg');
    }

    /**
     * Get the image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/service-placeholder.jpg');
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
     * Get starting price from pricing array.
     */
    public function getStartingPriceAttribute(): ?string
    {
        if (!$this->pricing || !is_array($this->pricing)) {
            return null;
        }

        $prices = array_column($this->pricing, 'price');
        if (empty($prices)) {
            return null;
        }

        $minPrice = min($prices);
        return number_format($minPrice, 0, ',', ' ') . ' ₽';
    }

    /**
     * Get features as comma-separated string.
     */
    public function getFeaturesStringAttribute(): string
    {
        if (!$this->features) {
            return '';
        }
        return implode(', ', $this->features);
    }

    /**
     * Generate slug from name.
     */
    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;
        if (!$this->slug) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    /**
     * Scope for active services.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured services.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope ordered by sort order and name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get route key name for model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get default services data.
     */
    public static function getDefaultServices(): array
    {
        return [
            [
                'name' => 'Сайты',
                'slug' => 'websites',
                'description' => 'Создание сайтов любой сложности от лендингов до интернет-магазинов',
                'short_description' => 'Разработка современных и функциональных веб-сайтов',
                'features' => ['Адаптивный дизайн', 'SEO оптимизация', 'Система управления', 'Техподдержка'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Дизайн',
                'slug' => 'design',
                'description' => 'Создание уникального дизайна для вашего бренда и веб-проектов',
                'short_description' => 'Профессиональный дизайн и брендинг',
                'features' => ['Логотип', 'Фирменный стиль', 'UI/UX дизайн', 'Печатная продукция'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Хостинг',
                'slug' => 'hosting',
                'description' => 'Надежный хостинг для ваших веб-проектов с круглосуточной поддержкой',
                'short_description' => 'Качественный хостинг и серверные решения',
                'features' => ['SSD диски', 'SSL сертификаты', 'Backup', 'Техподдержка 24/7'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Домены',
                'slug' => 'domains',
                'description' => 'Регистрация и управление доменными именами',
                'short_description' => 'Регистрация доменов всех зон',
                'features' => ['Все доменные зоны', 'DNS управление', 'Защита данных', 'Автопродление'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Продвижение',
                'slug' => 'promotion',
                'description' => 'Комплексное продвижение вашего бизнеса в интернете',
                'short_description' => 'SEO, контекстная реклама и SMM',
                'features' => ['SEO оптимизация', 'Яндекс.Директ', 'Google Ads', 'Социальные сети'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 5,
            ],
        ];
    }
}
