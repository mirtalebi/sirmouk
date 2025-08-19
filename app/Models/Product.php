<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'tax'
    ];
    public function invoices()
    {
        return $this->BelongsToMany(Invoice::class, 'invoice_product', 'product_id', 'invoice_id')
            ->withPivot(['quantity', 'unit_price', 'discount_price', 'tax']);
    }
}
