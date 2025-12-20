<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Component;

class ProductEdit extends Component
{
    public $id;
    public $name;
    public $description;
    public $price;
    public $category;
    public $tax;
    public $product;
    public $packaging_amount;

    public function mount($id)
    {
        $this->product = Product::findOrFail($id);
        $this->name = $this->product->name;
        $this->description = $this->product->description;
        $this->price = $this->product->price;
        $this->category = $this->product->category_id;
        $this->tax = $this->product->tax;
        $this->packaging_amount = $this->product->packaging_amount;
    }

    public function cancel()
    {
        return redirect()->route('products');
    }
    public function update()
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
        $update = $this->product->update([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'tax' => $this->tax,
            'category_id' => $this->category,
            'packaging_amount' => $this->packaging_amount ? $this->packaging_amount : null,
        ]);

        if ($update) {
            return redirect()->route('products')->with('success', 'محصول مورد نطر با موفقیت ویرایش شد');
        }else{
            return redirect()->back()->with('fail', 'ویرایش محصول با مشکل مواجه شد! دوباره تلاش کنید');
        }
    }

    public function render()
    {
        $categories = ProductCategory::all();
        return view('livewire.products.product-edit', compact('categories'));
    }
}
