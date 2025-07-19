<?php

namespace App\Livewire\Invoice;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transaction;
use Livewire\Attributes\On;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class InvoicePayment extends Component
{

    public $payments = [];
    public $invoice;
    public $paid_amount;

    public $transaction_price = 0;
    public $invoice_price;

    public $account;
    public $j_date;
    public $amount;
    public $transaction_date;


//    #[On('show-payment-modal')]
//    public function showPaymentModal($invoice){
//        dd('show payment modal');
//        $this->invoice = $invoice;
//        $this->invoice_price = $invoice->total_price -= $invoice->paid_amount;
//        $this->paid_amount = $invoice->paid_amount;
//    }

    public function mount($invoice){
        $this->invoice = $invoice;
        $this->invoice_price = $invoice->total_price -= $invoice->paid_amount;
        $this->paid_amount = $invoice->paid_amount;
    }

    public function addPayment()
    {
        $this->payments[] = new Transaction();
    }

    public function savePayment()
    {
        $this->validate([
            'account' => 'required|exists:accounts,id',
            'j_date' => 'required',
            'amount' => 'required|integer',
        ]);

        if ($this->amount > $this->invoice_price) {
            session()->flash('error', 'مبلغ وارد شده بیشتر از مبلغ مانده است!');
            return;
        }

        $this->transaction_date = Jalalian::fromFormat('Y/m/d', $this->j_date)->toCarbon();

        $current_balance = Account::find($this->account)->balance;
        $current_balance += $this->amount;
        Account::find($this->account)->update(['balance' => $current_balance]);

        $create = Transaction::create([
            'amount' => $this->amount,
            'type' => 'credit',
            'description' => 'فروش غذا',
            'category_id' => 1,
            'account_id' => $this->account,
            'current_balance' => $current_balance,
            'transaction_date' => $this->transaction_date,
            'invoice_id' => $this->invoice->id,
        ]);

        $this->paid_amount += $this->amount;

        Invoice::where('id', $this->invoice->id)->update([
            'paid_amount' => $this->paid_amount,
        ]);

        if ($create) {
            $this->invoice_price -= $this->amount;
            $this->reset('payments', 'account', 'j_date', 'amount', 'transaction_date');
        }
    }

    public function getRemainingAmountProperty()
    {
        return $this->invoice_price -= $this->transaction_price;
    }

    public function render()
    {
//        dd($this->invoice);
        $invoice = $this->invoice;
//        $transactions = Transaction::where('invoice_id', $invoice->id)->get();
        $transactions = $invoice->transactions;
        return view('livewire.invoice.invoice-payment', compact('invoice', 'transactions'));
    }
}
