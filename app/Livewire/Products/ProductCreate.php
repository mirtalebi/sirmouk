<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\RecipeItem;
use Livewire\Component;

class ProductCreate extends Component
{
    public $name;
    public $description;
    public $price;
    public $tax;
    public $category;
    public $packaging_amount;
    public $materials = [];

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'tax' => 'required',
            'category' => 'required|exists:product_categories,id',
            'materials' => 'array',
            'materials.*.item_id' => 'required|exists:recipe_items,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
        ],[
            'required' => 'لطفا این فیلد را پر کنید!'
        ]);

        $materialsCost = 0;
        foreach ($this->materials as $material) {
            $item = RecipeItem::find($material['item_id'] ?? null);
            if (! $item) {
                continue;
            }
            $quantity = (float) ($material['quantity'] ?? 0);
            $materialsCost += $item->calculateValue() * $quantity;
        }

        $product = Product::create([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'tax' => $this->tax,
            'category_id' => $this->category,
            'materials' => $this->materials,
            'profit' => $this->price - $materialsCost,
            'packaging_amount' => $this->packaging_amount,
        ]);

        if ($product) {
            return redirect()->route('products')->with('success', 'محصول مورد نطر با موفقیت ساخته شد!');
        }

        return redirect()->back()->with('fail', 'اضافه کردن محصول با مشکل مواجه شد! دوباره تلاش کنید');
    }

    private function getRecipeItemsForForm()
    {
        return RecipeItem::orderBy('name')->get()->map(function (RecipeItem $item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'cost' => $item->calculateValue(),
            ];
        });
    }

    public function render()
    {
        $categories = ProductCategory::all();
        $recipeItems = $this->getRecipeItemsForForm();
        return view('livewire.products.product-create', compact('categories', 'recipeItems'));
    }
}
