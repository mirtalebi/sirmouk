<?php

namespace App\Livewire\Transaction;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

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

        $current_balance = Account::find($this->account_id)->balance;
        $current_balance += $this->amount;
        Account::find($this->account_id)->update(['balance' => $current_balance]);



        $create = Transaction::create([
            'amount' => $this->amount,
            'type' => $this->type,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'account_id' => $this->account_id,
            'current_balance' => $current_balance,
            'transaction_date' => $this->transaction_date,
        ]);

        return redirect()->route('transactions.index')->with('success', 'تراکنش با موفقیت ثبت شد!');

    }


    public function render()
    {
        return view('livewire.transaction.transaction-create',[
            'categories' => TransactionCategory::all(),
            'accounts' => Account::all(),
        ]);
    }
}
