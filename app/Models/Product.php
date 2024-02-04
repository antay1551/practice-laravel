<?php

namespace App\Models;

use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'price',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected function priceEur(): Attribute
    {
        return Attribute::make(
            get: fn() => (new CurrencyService())->convert($this->price, 'usd', 'eur'),
        );
    }
}
