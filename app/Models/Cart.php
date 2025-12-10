<?php

namespace App\Models;

use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{

    use MassPrunable;

    public function prunable()
    {
        return static::where('updated_at', '<=', now()->subHours(2));
    }

    protected $fillable = ['session_id'];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function totalQuantity()
    {
        return $this->items->sum('quantity');
    }

    public static function ifExists()
    {
        return static::with('items.product')
            ->where('session_id', session()->getId())
            ->first();
    }

    public static function ensureExists()
    {
        return static::firstOrCreate([
            'session_id' => session()->getId(),
        ]);
    }

    public function incrementItem(Product $product)
    {
        $this->items()->firstOrCreate([
            'product_id' => $product->id,
        ], [
            'quantity' => 0,
        ])->increment('quantity');
    }
}
