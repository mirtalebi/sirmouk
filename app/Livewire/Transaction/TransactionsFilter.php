<?php

namespace App\Livewire\Transaction;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use Livewire\Attributes\On;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class TransactionsFilter extends Component
{
    public $from_date = null;
    public $to_date = null;
    public $transactions;
    public $summaries = [];

    public function submit()
    {

        $this->validate([
            'from_date' => 'required',
            'to_date' => 'required',
        ],[
            'required' => 'این فیلد اجباری است!'
        ]);

        $from_date = Jalalian::fromFormat('Y/m/d', $this->from_date)->toCarbon();
        $to_date = Jalalian::fromFormat('Y/m/d', $this->to_date)->toCarbon()->endOfDay();

        $this->summaries = TransactionCategory::with(['transactions' => function ($q) use ($from_date, $to_date) {
            $q->whereBetween('transaction_date', [$from_date, $to_date]);
        }])->get()->map(function ($category) {
            return [
                'name' => $category->name,
                'total' => $category->transactions->sum('amount'),
            ];
        });
    }



    public function render()
    {
        $summaries  = $this->summaries;
        return view('livewire.transaction.transactions-filter', compact('summaries'));
    }
}
