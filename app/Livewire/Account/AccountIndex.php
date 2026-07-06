<?php

namespace App\Livewire\Account;

use App\Models\Account;
use Livewire\Component;

class AccountIndex extends Component
{
    public $rootAccounts = [];
    public $expandedAccounts = [];

    public function mount()
    {
        $this->loadAccounts();
    }

    public function loadAccounts()
    {
        // Load only root accounts (parent_id is null)
        $this->rootAccounts = Account::roots()
            ->with('children')
            ->orderBy('code')
            ->get()
            ->toArray();

        // Pre-expand first level of accounts
        $this->expandedAccounts = Account::roots()->pluck('id')->toArray();
    }

    public function toggleExpand($accountId)
    {
        if (in_array($accountId, $this->expandedAccounts)) {
            // Remove from expanded
            $this->expandedAccounts = array_filter(
                $this->expandedAccounts,
                fn($id) => $id !== $accountId
            );
        } else {
            // Add to expanded
            $this->expandedAccounts[] = $accountId;
        }
    }

    public function delete($id)
    {
        $account = Account::find($id);
        
        // Check if account has children or items
        if ($account->children()->count() > 0) {
            session()->flash('error', 'نمی‌توان حساب دارای زیرحساب را حذف کرد!');
            return;
        }
        
        if ($account->items()->count() > 0) {
            session()->flash('error', 'نمی‌توان حساب دارای سطرهای سند را حذف کرد!');
            return;
        }

        if ($account->delete()) {
            $this->loadAccounts();
            session()->flash('success', 'حساب با موفقیت حذف شد!');
        }
    }

    public function render()
    {
        return view('livewire.account.account-index', [
            'rootAccounts' => $this->rootAccounts,
            'expandedAccounts' => $this->expandedAccounts,
        ]);
    }

    /**
     * Get children of an account for display
     */
    public function getChildren($parentId)
    {
        return Account::where('parent_id', $parentId)->orderBy('code')->get();
    }
}

