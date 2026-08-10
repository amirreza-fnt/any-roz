<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class)->withTimestamps();
    }

    public static function findOrCreateFromName(string $name): self
    {
        $name = trim($name);
        if ($name === '') {
            throw new \InvalidArgumentException('Empty tag');
        }

        $existing = static::where('name', $name)->first();
        if ($existing) {
            return $existing;
        }

        $base = Str::slug($name);
        if ($base === '') {
            $base = 'tag-'.Str::lower(Str::random(6));
        }

        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return static::create([
            'name' => $name,
            'slug' => $slug,
        ]);
    }
}
