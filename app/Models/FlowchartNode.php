<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlowchartNode extends Model
{
    public const SOURCE_NONE = 'none';
    public const SOURCE_ALL_SALES = 'all_sales';
    public const SOURCE_OWN_SALES = 'own_sales';
    public const SOURCE_MARKETING_SALES = 'marketing_sales';
    public const SOURCE_SITE_SALES = 'site_sales';

    public const PROFIT_SOURCES = [
        self::SOURCE_NONE,
        self::SOURCE_ALL_SALES,
        self::SOURCE_OWN_SALES,
        self::SOURCE_MARKETING_SALES,
        self::SOURCE_SITE_SALES,
    ];

    protected $fillable = [
        'parent_id',
        'title',
        'sort_order',
        'profit_percentage',
        'profit_source',
        'admin_id',
        'color',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'profit_percentage' => 'decimal:2',
        'parent_id' => 'integer',
        'admin_id' => 'integer',
    ];

    public static function profitSourceLabel(string $source): string
    {
        return match ($source) {
            self::SOURCE_NONE => 'بدون سهم سود',
            self::SOURCE_ALL_SALES => 'از کل فروش‌ها',
            self::SOURCE_OWN_SALES => 'فقط از فروش خود',
            self::SOURCE_MARKETING_SALES => 'از فروش بازاریابی',
            self::SOURCE_SITE_SALES => 'از فروش سایت',
            default => $source,
        };
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public static function buildTree(): array
    {
        $nodes = self::with('admin')->orderBy('sort_order')->get();

        $grouped = $nodes->groupBy(fn ($n) => $n->parent_id ?? 0);

        $build = function ($parentId) use (&$build, $grouped) {
            $items = $grouped->get($parentId, collect());

            return $items->map(function ($node) use ($build) {
                return [
                    'id' => $node->id,
                    'title' => $node->title,
                    'sort_order' => $node->sort_order,
                    'profit_percentage' => (float) $node->profit_percentage,
                    'profit_source' => $node->profit_source,
                    'profit_source_label' => self::profitSourceLabel($node->profit_source),
                    'admin_id' => $node->admin_id,
                    'admin_name' => $node->admin?->full_name,
                    'color' => $node->color,
                    'parent_id' => $node->parent_id,
                    'children' => $build($node->id),
                ];
            })->values()->toArray();
        };

        return $build(0);
    }
}
