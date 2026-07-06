<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RecipeItem;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'tax',
        'materials',
        'profit',
        'packaging_amount'
    ];

    protected $casts = [
        'materials' => 'array',
    ];

    public function invoices()
    {
        return $this->BelongsToMany(Invoice::class, 'invoice_product', 'product_id', 'invoice_id')
            ->withPivot(['quantity', 'unit_price', 'discount_price', 'tax']);
    }

    public function getMaterialsCostAttribute()
    {
        $materials = $this->materials ?? [];
        if (! is_array($materials) || empty($materials)) {
            return 0;
        }

        $itemIds = collect($materials)
            ->pluck('item_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $items = RecipeItem::whereIn('id', $itemIds)->get()->keyBy('id');
        $total = 0;

        foreach ($materials as $material) {
            if (empty($material['item_id']) || ! isset($material['quantity'])) {
                continue;
            }

            $item = $items[$material['item_id']] ?? null;
            if (! $item) {
                continue;
            }

            $quantity = (float) $material['quantity'];
            $total += $item->calculateValue() * $quantity;
        }

        return $total;
    }
}
