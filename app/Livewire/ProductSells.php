<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Arr;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class ProductSells extends Component
{
    public $from_date;
    public $to_date;
    public $products = [];
    public $selectedProducts = [];

    public function submit()
    {
        $this->validate([
            'from_date' => 'required',
            'to_date' => 'required',
        ],[
            'required' => 'این فیلد اجباری است!'
        ]);

        $saveSelectedProducts = $this->selectedProducts;
        $this->selectedProducts = Arr::where($this->selectedProducts, function ($value, $key) {
            return $value == true;
        });
        $this->selectedProducts = array_keys($this->selectedProducts);


        $from_date = Jalalian::fromFormat('Y/m/d', $this->from_date)->toCarbon();
        $to_date = Jalalian::fromFormat('Y/m/d', $this->to_date)->toCarbon()->endOfDay();

        $this->products = Product::with(['invoices' => function ($q) use ($from_date, $to_date) {
            $q->whereBetween('invoices.created_at', [$from_date, $to_date]);
        }])
            ->when($this->selectedProducts, function ($q) {
                $q->whereIn('id', $this->selectedProducts);
            })
            ->get()->map(function ($product) {
            return [
                'name' => $product->name,
                'quantity' => $product->invoices->sum('pivot.quantity'),
                'price' => $product->price,
            ];
        });
        $this->selectedProducts = $saveSelectedProducts;
    }

    public function render()
    {
        $products = $this->products;
        $showProducts = Product::all();
        return view('livewire.product-sells', compact('products', 'showProducts'));
    }
}
