<?php

namespace App\Livewire\Transaction;

use App\Models\Transaction;
use Livewire\Component;

class TransactionIndex extends Component
{

//    public function mount()
//    {
//        $this->transactions = Transaction::latest()->paginate(10);
//
//    }

    public function render()
    {
        $transactions = Transaction::latest();
        return view('livewire.transaction.transaction-index', compact('transactions'));
    }
}
