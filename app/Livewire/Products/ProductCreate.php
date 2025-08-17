<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Component;

class ProductCreate extends Component
{
    public $name;
    public $description;
    public $price;
    public $tax;
    public $category;

    public function save()
    {
//        dd($this->name, $this->description, $this->price, $this->tax, $this->category);
        $this->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'tax' => 'required',
            'category' => 'required|exists:product_categories,id',
        ],[
            'required' => 'لطفا این فیلد را پر کنید!'
        ]);

        $product = Product::create([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'tax' => $this->tax,
            'category_id' => $this->category,
        ]);
    }


    public function render()
    {
        $categories = ProductCategory::all();
        return view('livewire.products.product-create', compact('categories'));
    }
}
