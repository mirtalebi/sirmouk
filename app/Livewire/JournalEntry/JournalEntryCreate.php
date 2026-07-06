<?php

namespace App\Livewire\JournalEntry;

use App\Http\Requests\StoreJournalEntryRequest;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\User;
use Livewire\Component;

class JournalEntryCreate extends Component
{
    public $entryDate;
    public $description = '';
    public $status = 'draft';
    public $items = [];
    public $itemCount = 0;

    public $accounts = [];
    public $customers = [];

    public function mount()
    {
        $this->entryDate = now()->format('Y-m-d');
        $this->addItem();
        $this->addItem();
        
        // Load accounts and customers
        $this->loadOptions();
    }

    public function loadOptions()
    {
        $this->accounts = Account::onlyDetailed()->orderBy('code')->get()->toArray();
        $this->customers = User::orderBy('name')->get()->toArray();
    }

    public function addItem()
    {
        $this->items[] = [
            'id' => $this->itemCount++,
            'account_id' => null,
            'customer_id' => null,
            'debit' => 0,
            'credit' => 0,
            'description' => '',
        ];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 2) {
            unset($this->items[$index]);
            $this->items = array_values($this->items); // Reindex array
        }
    }

    /**
     * Get total debit
     */
    #[\Livewire\Attributes\Computed]
    public function totalDebit()
    {
        return array_sum(array_column($this->items, 'debit'));
    }

    /**
     * Get total credit
     */
    #[\Livewire\Attributes\Computed]
    public function totalCredit()
    {
        return array_sum(array_column($this->items, 'credit'));
    }

    /**
     * Check if entry is balanced
     */
    #[\Livewire\Attributes\Computed]
    public function isBalanced()
    {
        return $this->totalDebit() === $this->totalCredit() && $this->totalDebit() > 0;
    }

    /**
     * Check if account requires customer selection
     */
    public function accountRequiresCustomer($accountId)
    {
        if (!$accountId) {
            return false;
        }
        
        // Find account in the accounts array
        $account = collect($this->accounts)->firstWhere('id', $accountId);
        
        // Check if parent code is 102 (receivables parent account)
        if ($account && $account['parent_id']) {
            $parent = Account::find($account['parent_id']);
            return $parent && $parent->code === '102';
        }
        
        return false;
    }

    /**
     * Save journal entry
     */
    public function save()
    {
        // Validate using form request
        $validator = validator($this->prepareData(), $this->getValidationRules());
        
        if ($validator->fails()) {
            session()->flash('error', 'خطا در اعتبارسنجی سند.');
            return;
        }

        try {
            // Create journal entry
            $entry = JournalEntry::create([
                'entry_date' => $this->entryDate,
                'description' => $this->description,
                'status' => 'draft', // Always start as draft
            ]);

            // Create items
            foreach ($this->items as $item) {
                $entry->items()->create([
                    'account_id' => $item['account_id'],
                    'customer_id' => $item['customer_id'] ?? null,
                    'debit' => $item['debit'] ?? 0,
                    'credit' => $item['credit'] ?? 0,
                    'description' => $item['description'] ?? '',
                ]);
            }

            // Update account balances IMMEDIATELY for real-time display
            $entry->updateAccountBalances();

            // Post if status is posted
            if ($this->status === 'posted') {
                $entry->postEntry();
            }

            session()->flash('success', 'سند حسابداری با موفقیت ثبت شد!');
            return redirect()->route('accounts.index');
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در ثبت سند: ' . $e->getMessage());
        }
    }

    /**
     * Prepare data for validation
     */
    private function prepareData()
    {
        return [
            'entry_date' => $this->entryDate,
            'description' => $this->description,
            'status' => $this->status,
            'items' => $this->items,
        ];
    }

    /**
     * Get validation rules
     */
    private function getValidationRules()
    {
        return [
            'entry_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:draft,posted',
            'items' => 'required|array|min:2',
            'items.*.account_id' => 'required|integer|exists:accounts,id',
            'items.*.customer_id' => 'nullable|integer|exists:users,id',
            'items.*.debit' => 'required|integer|min:0',
            'items.*.credit' => 'required|integer|min:0',
            'items.*.description' => 'nullable|string|max:255',
        ];
    }

    public function render()
    {
        return view('livewire.journal-entry.journal-entry-create', [
            'accounts' => $this->accounts,
            'customers' => $this->customers,
            'totalDebit' => $this->totalDebit(),
            'totalCredit' => $this->totalCredit(),
            'isBalanced' => $this->isBalanced(),
        ]);
    }
}
