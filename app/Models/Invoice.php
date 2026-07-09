<?php

namespace App\Models;

use App\Common\Jalalian;
use App\Models\Scopes\DescOrderScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; 

#[ScopedBy([DescOrderScope::class])]
class Invoice extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'card' => 'array',
            'snap_user_credentials' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_product', 'invoice_id', 'product_id')
            ->withPivot(['quantity', 'unit_price', 'discount_price', 'tax'])
            ->withTimestamps();
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        // اگر عبارت جستجو خالی بود، کوری بدون تغییر برمی‌گردد
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $subQuery) use ($term) {
            $subQuery->where('id', 'like', "%{$term}%") // جستجو بر اساس شماره فاکتور
                
                ->orWhereHas('user', function (Builder $userQuery) use ($term) {
                    $userQuery->where('name', 'like', "%{$term}%")
                              ->orWhere('mobile', 'like', "%{$term}%");
                })
                
                ->orWhere('snap_user_credentials->username', 'like', "%{$term}%")
                ->orWhere('snap_user_credentials->mobile', 'like', "%{$term}%");
        });
    }

    public function paidAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => Transaction::where('invoice_id', $this->id)->sum('amount')
        );
    }

    public function calcTaxPrice()
    {
        $tax = 0;
        foreach ($this->products as $product) {
            if ($product) {
                $tax += $product->pivot->tax;
            }
        }
        return $tax;
    }

    public function calcFinalPrice()
    {
        $total = 0;
        $tax = 0;
        foreach ($this->products as $product) {
            if ($product) {
                $total += $product->pivot->unit_price * $product->pivot->quantity;
            }
        }

        $total -= $this->discount_price;
        $total += $this->calcTaxPrice();
        $total += $this->courier_price;
        $total += $this->packaging_price;
        return $total;
    }

    public function getCreatedAtDate()
    {
        $jDate = Jalalian::fromDateTime($this->created_at);
        return $jDate->format('%d %B %Y');
    }

    public function setProdcuts($card)
    {
        $this->products()->detach();
        foreach ($card as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $this->products()->attach($productId, [
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'discount_price' => 0,
                    'tax' => $product->tax * $quantity * $product->price / 100,
                ]);
                $this->save();
            }
        }
    }

    public function getSumPackagingPrice() {
        $result = 0;
        foreach($this->products as $product) {
            $result += $product->pivot->quantity * $product->packaging_amount;
        }
        return $result;
    }

    public function setTotalPrice()
    {
        $this->total_price = $this->calcFinalPrice();
    }

    // public function getCustomerNameMobile() {
    //     return [
    //         'mobile' => $invoice->is_snap ?
    //     ];
    // }
}
