<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeItem extends Model
{
    protected $fillable = [
        'name',
        'value_type', // static or dynamic
        'value',
        'components'
    ];

    protected $casts = [
        'value' => 'float',
        'components' => 'array',
    ];

    public function calculateValue(array $visited = [])
    {
        if ($this->value_type === 'static') {
            return (float) $this->value;
        }

        if ($this->value_type !== 'dynamic') {
            return 0.0;
        }

        if ($this->id && in_array($this->id, $visited, true)) {
            return 0.0;
        }

        $visited[] = $this->id;
        $totalValue = 0.0;
        $components = $this->components ?? [];

        foreach ($components as $component) {
            if (empty($component['item_id']) || empty($component['quantity'])) {
                continue;
            }

            $item = self::find($component['item_id']);
            if (! $item) {
                continue;
            }

            $quantity = (float) $component['quantity'];
            $totalValue += $item->calculateValue($visited) * $quantity;
        }

        return $totalValue;
    }

    public function getComputedValueAttribute()
    {
        return $this->calculateValue();
    }
}
