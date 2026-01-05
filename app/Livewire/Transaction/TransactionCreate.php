<?php

namespace App\Livewire\Transaction;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Morilog\Jalali\Jalalian;
use Livewire\Attributes\On;

class TransactionCreate extends Component
{
    public $amount;
    public $type;
    public $description;
    public $category_id;
    public $account_id;
    public $account;
    public $transaction_date_jalali;
    public $transaction_date;
    public $tracking_code = '';

    public function mount()
    {
        $this->account = Account::all();
    }

    public function createTransaction()
    {
        $this->validate([
            'amount' => 'required|integer',
            'type' => 'required|string|in:debit,credit',
            'description' => 'required|string',
            'category_id' => 'required|integer|exists:transaction_categories,id',
            'account_id' => 'required|integer|exists:accounts,id',
            'transaction_date_jalali' => 'required',
            'tracking_code' => 'string',
        ]);

        $this->transaction_date = Jalalian::fromFormat('Y/m/d', $this->transaction_date_jalali)->toCarbon();


        if ($this->type == 'debit'){
            if ($this->amount > 0){
                $this->amount = $this->amount * -1;
            }
        }else{
            if ($this->amount < 0){
                $this->amount = $this->amount * -1;
            }
        }

        try {
            $create = Transaction::makeTransaction(
                $this->amount,
                $this->type,
                $this->description,
                $this->category_id,
                $this->account_id,
                $this->transaction_date,
                null,
                $this->tracking_code);
            $this->dispatch('toast', type: 'success', message: 'تراکنش ثبت شد');
            $this->dispatch('transaction-created');
            $this->reset();
        } catch (\Throwable $th) {
            $this->dispatch('toast', type: 'failed', message: $th);
        }




    }


    public function render()
    {
        $categories = TransactionCategory::all();
        $accounts = Account::all();
        return view('livewire.transaction.transaction-create', compact('categories', 'accounts'));
    }
}
