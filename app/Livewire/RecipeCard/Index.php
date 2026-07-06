<?php

namespace App\Livewire\RecipeCard;

use App\Models\RecipeItem;
use Livewire\Component;

class Index extends Component
{
    public $recipeItemId;
    public $name = '';
    public $valueType = 'static';
    public $value = 0;
    public $components = [];
    public $filterType = 'all';

    protected $rules = [
        'name' => 'required|string|max:255',
        'valueType' => 'required|in:static,dynamic',
        'value' => 'nullable|numeric|min:0',
        'components' => 'array',
        'components.*.item_id' => 'required|exists:recipe_items,id',
        'components.*.quantity' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->resetForm();
    }

    public function updatedValueType($value)
    {
        if ($value === 'static') {
            $this->components = [];
        }

        if ($value === 'dynamic' && empty($this->components)) {
            $this->components = [
                ['item_id' => null, 'quantity' => 1],
            ];
            $this->value = 0;
        }
    }

    public function addComponent()
    {
        $this->components[] = ['item_id' => null, 'quantity' => 1];
    }

    public function removeComponent($index)
    {
        unset($this->components[$index]);
        $this->components = array_values($this->components);
    }

    public function editRecipeItem($id)
    {
        $item = RecipeItem::findOrFail($id);

        $this->recipeItemId = $item->id;
        $this->name = $item->name;
        $this->valueType = $item->value_type;
        $this->value = $item->value;
        $this->components = $item->components ?: [];

        if ($this->valueType === 'dynamic' && empty($this->components)) {
            $this->components = [
                ['item_id' => null, 'quantity' => 1],
            ];
        }
    }

    public function deleteRecipeItem($id)
    {
        RecipeItem::destroy($id);
        $this->resetForm();
        session()->flash('success', 'Recipe item removed successfully.');
    }

    public function save()
    {
        $this->validate();

        if ($this->valueType === 'dynamic') {
            $validComponents = array_filter($this->components, function ($component) {
                return ! empty($component['item_id']) && is_numeric($component['quantity']);
            });

            if (empty($validComponents)) {
                $this->addError('components', 'یک یا چند مؤلفه برای آیتم پویا لازم است.');
                return;
            }
        }

        $payload = [
            'name' => $this->name,
            'value_type' => $this->valueType,
            'value' => $this->valueType === 'static' ? (float) $this->value : 0,
            'components' => $this->valueType === 'dynamic' ? $this->components : [],
        ];

        if ($this->recipeItemId) {
            RecipeItem::findOrFail($this->recipeItemId)->update($payload);
            session()->flash('success', 'آیتم دستور پخت با موفقیت به‌روزرسانی شد.');
        } else {
            RecipeItem::create($payload);
            session()->flash('success', 'آیتم دستور پخت با موفقیت ایجاد شد.');
        }

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->recipeItemId = null;
        $this->name = '';
        $this->valueType = 'static';
        $this->value = 0;
        $this->components = [];
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function getAvailableComponentItemsProperty()
    {
        $query = RecipeItem::query();
        if ($this->recipeItemId) {
            $query->where('id', '!=', $this->recipeItemId);
        }

        return $query->orderBy('name')->get();
    }

    public function getFilteredRecipeItemsProperty()
    {
        $query = RecipeItem::orderBy('name');

        if ($this->filterType === 'static') {
            $query->where('value_type', 'static');
        }

        if ($this->filterType === 'dynamic') {
            $query->where('value_type', 'dynamic');
        }

        return $query->get();
    }

    public function getCurrentTotalProperty()
    {
        if ($this->valueType === 'static') {
            return (float) $this->value;
        }

        $total = 0.0;
        foreach ($this->components as $component) {
            if (empty($component['item_id']) || empty($component['quantity'])) {
                continue;
            }

            $item = RecipeItem::find($component['item_id']);
            if (! $item) {
                continue;
            }

            $total += $item->calculateValue() * (float) $component['quantity'];
        }

        return $total;
    }

    public function render()
    {
        $recipeItems = $this->filteredRecipeItems;
        return view('livewire.recipe-card.index', [
            'recipeItems' => $recipeItems,
            'currentTotal' => $this->currentTotal,
            'availableComponentItems' => $this->availableComponentItems,
        ]);
    }
}
