<?php

namespace App\Models;

use App\Support\PublicUploads;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'featured_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_image',
        'focus_keyword',
        'noindex',
        'status',
        'published_at',
        'author_id',
        'category_id',
        'reading_minutes',
        'view_count',
        'is_featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'noindex' => 'boolean',
        'is_featured' => 'boolean',
        'view_count' => 'integer',
        'reading_minutes' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function isPubliclyVisible(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        return $this->published_at === null || $this->published_at->lte(now());
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return PublicUploads::url($this->featured_image);
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return PublicUploads::url($this->og_image);
    }
}
