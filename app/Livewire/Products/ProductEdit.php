<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\RecipeItem;
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
    public $materials = [];

    public function mount($id)
    {
        $this->product = Product::findOrFail($id);
        $this->name = $this->product->name;
        $this->description = $this->product->description;
        $this->price = $this->product->price;
        $this->category = $this->product->category_id;
        $this->tax = $this->product->tax;
        $this->packaging_amount = $this->product->packaging_amount;

        $decodedMaterials = $this->product->materials;
        $this->materials = is_array($decodedMaterials) ? array_map(function ($material) {
            return [
                'item_id' => $material['item_id'] ?? null,
                'quantity' => $material['quantity'] ?? 0,
            ];
        }, $decodedMaterials) : [];
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
            'materials' => 'array',
            'materials.*.item_id' => 'required|exists:recipe_items,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
        ], [
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

        $profit = $this->price - $materialsCost;

        $update = $this->product->update([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'tax' => $this->tax,
            'category_id' => $this->category,
            'materials' => $this->materials,
            'profit' => $profit,
            'packaging_amount' => $this->packaging_amount ? $this->packaging_amount : null,
        ]);

        if ($update) {
            return redirect()->route('products')->with('success', 'محصول مورد نطر با موفقیت ویرایش شد');
        }

        return redirect()->back()->with('fail', 'ویرایش محصول با مشکل مواجه شد! دوباره تلاش کنید');
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
        return view('livewire.products.product-edit', compact('categories', 'recipeItems'));
    }
}
