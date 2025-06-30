<?php

namespace App\Livewire\Transaction;

use App\Models\Transaction;
use Livewire\Component;

class TransactionList extends Component
{

    public $account_id = null;
    public function mount($account_id = null, $from_date = null, $to_date = null)
    {
        $this->account_id = $account_id;
    }

    public function render()
    {
        $transactions = Transaction::query()
            ->when($this->account_id, fn($query) => $query->where('account_id', $this->account_id))
            ->latest()
            ->paginate(10);

        return view('livewire.transaction.transaction-list', compact('transactions'));
    }
}
