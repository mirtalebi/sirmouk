<?php

namespace App\Livewire\Transaction;

use Livewire\Component;
use Morilog\Jalali\Jalalian;

class TransactionsFilter extends Component
{
    public $from_date = null;
    public $to_date = null;

    public function submit()
    {
        $this->from_date = Jalalian::fromFormat('Y/m/d', $this->from_date)->toCarbon();
        $this->to_date = Jalalian::fromFormat('Y/m/d', $this->to_date)->toCarbon()->endOfDay();
        dd($this->from_date, $this->to_date);
    }



    public function render()
    {
        $from_date = $this->from_date;
        $to_date = $this->to_date;
        return view('livewire.transaction.transactions-filter', compact('from_date', 'to_date'));
    }
}
