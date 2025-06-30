<?php

namespace App\Livewire\Account;

use App\Models\Transaction;
use Livewire\Component;

class TransactionsList extends Component
{

    public $id;
    public function mount($id)
    {
        $this->id = $id;
    }

    public function render()
    {
        return view('livewire.account.transactions-list', ['id' => $this->id]);
    }
}
