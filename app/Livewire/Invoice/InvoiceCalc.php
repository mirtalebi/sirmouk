<?php

namespace App\Livewire\Invoice;

use App\Models\Invoice;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class InvoiceCalc extends Component
{
    public $from_date = null;
    public $to_date = null;
    public $invoices = [];
    public $invoice_calc = 0;

    public function submit(){
        $from_date = Jalalian::fromFormat('Y/m/d', $this->from_date)->toCarbon();
        $to_date = Jalalian::fromFormat('Y/m/d', $this->to_date)->toCarbon()->endOfDay();
//        dd($this->from_date, $this->to_date);

        $this->invoices = Invoice::where('created_at', '>=', $from_date)
            ->where('created_at', '<=', $to_date)->get();

        $this->invoice_calc = 0;
        foreach($this->invoices as $invoice){
            $this->invoice_calc += $invoice->calcFinalPrice();
        }
    }

    public function render()
    {
        return view('livewire.invoice.invoice-calc', ['invoices' => $this->invoices, 'invoice_calc' => $this->invoice_calc]);
    }
}
