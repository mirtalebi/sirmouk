<?php

namespace App\Livewire\ShoppingList;

use App\Models\ShoppingListItem;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ShoppingList extends Component
{
    public $description = '';
    public $priority = 'medium';
    public $showDone = true;

    protected $rules = [
        'description' => 'required|string|max:255',
        'priority' => 'required|in:low,medium,high',
    ];

    public function createItem()
    {
        $this->validate();

        ShoppingListItem::create([
            'user_id' => auth()->id(),
            'description' => $this->description,
            'priority' => $this->priority,
            'done' => false,
        ]);

        Session::flash('success', 'آیتم جدید به لیست خرید اضافه شد.');
        $this->reset(['description', 'priority']);
        $this->priority = 'medium';
    }

    public function toggleDone(int $id)
    {
        $item = $this->getItem($id);
        $item->done = !$item->done;
        $item->save();
    }

    public function deleteItem(int $id)
    {
        $this->getItem($id)->delete();
        Session::flash('success', 'آیتم از لیست حذف شد.');
    }

    protected function getItem(int $id): ShoppingListItem
    {
        return ShoppingListItem::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }

    public function render()
    {
        $items = ShoppingListItem::where('user_id', auth()->id())
            ->when(!$this->showDone, fn ($query) => $query->where('done', false))
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('priority');

        return view('livewire.shopping-list.shopping-list', ['itemsByPriority' => $items]);
    }
}
