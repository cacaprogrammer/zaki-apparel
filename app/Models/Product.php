<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'stock',
        'images', 'colors', 'sizes', 'badge', 'rating', 'reviews_count',
    ];

    protected $casts = [
        'images' => 'array',
        'colors' => 'array',
        'sizes' => 'array',
        'price' => 'integer',
        'stock' => 'integer',
        'rating' => 'float',
        'reviews_count' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Gambar utama produk (dipakai di grid/kartu produk).
     */
    public function getMainImageAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }
}