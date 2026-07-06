<?php

namespace App\Livewire\Account;

use App\Models\Account;
use Livewire\Component;

class CreateDetailedAccount extends Component
{
    public $name = '';
    public $parentAccountId = null;
    public $accountableType = null;
    public $accountableId = null;

    public $generalAccounts = [];
    public $showModal = false;

    public function mount()
    {
        // Load only general/control accounts (level 2) for parent selection
        $this->loadGeneralAccounts();
    }

    public function loadGeneralAccounts()
    {
        // Get all accounts with parent_id not null and their parent is root (parent_id = null)
        // This gives us the General/Control level accounts (level 2)
        $this->generalAccounts = Account::where('parent_id', '!=', null)
            ->with('parent')
            ->get()
            ->filter(function ($account) {
                // Keep only accounts whose parent is a root account
                return $account->parent && $account->parent->parent_id === null;
            })
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'code' => $account->code,
                    'name' => $account->name,
                ];
            })
            ->values()
            ->toArray();
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->parentAccountId = null;
        $this->accountableType = null;
        $this->accountableId = null;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'parentAccountId' => 'required|integer|exists:accounts,id',
            'accountableType' => 'nullable|string|in:App\Models\User',
            'accountableId' => 'nullable|integer',
        ]);

        try {
            // Create detailed account
            $account = Account::create([
                'name' => $this->name,
                'code' => null, // Detailed accounts don't always have codes
                'type' => $this->getParentType(),
                'parent_id' => $this->parentAccountId,
                'accountable_type' => $this->accountableType,
                'accountable_id' => $this->accountableId,
            ]);

            $this->closeModal();
            session()->flash('success', 'حساب تفصیلی با موفقیت ایجاد شد!');
            $this->dispatch('accountCreated');
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در ایجاد حساب: ' . $e->getMessage());
        }
    }

    private function getParentType()
    {
        $parent = Account::find($this->parentAccountId);
        return $parent ? $parent->type : 'asset';
    }

    public function render()
    {
        return view('livewire.account.create-detailed-account', [
            'generalAccounts' => $this->generalAccounts,
        ]);
    }
}
