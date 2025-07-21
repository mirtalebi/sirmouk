<?php

namespace App\Livewire\Transaction;

use App\Models\Transaction;
use Livewire\Component;

class TransactionList extends Component
{

    public $account_id = null;
    public $transaction_id = [];

    public function mount($transaction_id = [])
    {
        $this->transaction_id = $transaction_id ?? [];
    }

    public function render()
    {
        $transactions = Transaction::query()
            ->when($this->account_id, fn($query) => $query->where('account_id', $this->account_id))
            ->when($this->transaction_id && count($this->transaction_id) > 0,
                fn($query) => $query->whereIn('id', $this->transaction_id)
            )
            ->latest()
            ->paginate(10);


        return view('livewire.transaction.transaction-list', compact('transactions'));
    }
}
