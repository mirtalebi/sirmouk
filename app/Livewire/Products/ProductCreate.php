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
    public $packaging_amount;

    public function save()
    {
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
            'packaging_amount' => $this->packaging_amount,
        ]);
        if ($product) {
            return redirect()->route('products')->with('success', 'محصول مورد نطر با موفقیت ساخته شد!');
        }else{
            return redirect()->back()->with('fail', 'اضافه کردن محصول با مشکل مواجه شد! دوباره تلاش کنید');
        }
    }


    public function render()
    {
        $categories = ProductCategory::all();
        return view('livewire.products.product-create', compact('categories'));
    }
}
