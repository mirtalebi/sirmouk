<?php

namespace App\Livewire\Catgories;

use App\Models\ProductCategory;
use Livewire\Component;

class Categories extends Component
{
    public $name;
    public $category;
    public $modalOpen = false;
    public $category_name;
    public $category_id;


    public function openModal($id)
    {
        $this->category_id = $id;
        $this->category_name = ProductCategory::findOrFail($id)->name;
        $this->modalOpen = true;
    }

    public function delete()
    {
        ProductCategory::destroy($this->category_id);
        $this->reset(['category', 'name', 'modalOpen']);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
        ],[
            'name.required' => 'این فیلد را پر کنید!',
        ]);
        if (isset($this->category)) {
            $category = $this->category;
        } else {
            $category = new ProductCategory();
        }
        $category->name = $this->name;
        $category->save();
        $this->reset(['category', 'name']);
        return redirect()->back()->with('success', 'دسته بندی با موفقیت اضافه شد!');
    }

    public function editCategory($id)
    {
        $this->category = ProductCategory::findOrFail($id);
        $this->name = $this->category->name;
    }

    public function cancelEdit()
    {
        $this->reset(['category', 'name']);
    }

    public function render()
    {
        $categories = ProductCategory::all();
        return view('livewire.catgories.categories', compact('categories'));
    }
}
