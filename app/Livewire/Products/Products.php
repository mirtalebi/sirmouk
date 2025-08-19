<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;

class Products extends Component
{
    public $modalOpen = false;
    public $product_id;
    public $product_name;


    public function openModal($id)
    {
        $this->product_id = $id;
        $this->product_name = Product::findOrFail($id)->name;

        $this->modalOpen = true;
    }

    public function delete()
    {
        Product::destroy($this->product_id);
        $this->modalOpen = false;
    }

    public function render()
    {
        $products = Product::all();
        return view('livewire.products.products', compact('products'));
    }
}
