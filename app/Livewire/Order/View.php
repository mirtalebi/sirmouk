<?php

namespace App\Livewire\Order;

use App\Livewire\Invoice\InvoicePayment;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Morilog\Jalali\Jalalian;

class View extends Component
{
    use WithPagination;

    public Invoice $invoice;
    // public $invoices = [];
    public $products = [];
    public array $tempOrder = [
        'card' => [],
    ];
    public $customerName = '';
    public $customerMobile = '';
    public $courierPrice, $discountPrice;

    //    ------------------------------------
    public $showModal = false;
    public $invoicePayments;
    public $invoice_price;
    public $account;
    public $j_date;
    public $amount;
    public $transaction_date;
    public $paid_amount;
    public $payments = [];
    public $transactions = [];

    public $transaction_price = 0;



    public function removeBasket($productId)
    {
        if (isset($this->tempOrder['card'][$productId])) {
            $this->tempOrder['card'][$productId] = $this->tempOrder['card'][$productId] - 1;
            if ($this->tempOrder['card'][$productId] < 1) {
                unset($this->tempOrder['card'][$productId]);
            }
        }
    }

    public function addBasket($productId)
    {
        if (isset($this->tempOrder['card'][$productId])) {
            $this->tempOrder['card'][$productId] = $this->tempOrder['card'][$productId] + 1;
        } else {
            $this->tempOrder['card'][$productId] = 1;
        }
    }

    public function saveInvoice()
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerMobile' => 'required|string|max:11|min:11',
            'tempOrder.card' => 'required|array|min:1',
        ]);

        $user = User::getUserByMobile($this->customerMobile);
        $user->name = $this->customerName;
        $user->save();

        if (isset($this->invoice)) {
            $invoice = $this->invoice;
        } else {
            $invoice = new Invoice();
        }
        $invoice->user_id = $user->id;
        $invoice->discount_price = $this->discountPrice ?? 0;
        $invoice->courier_price = $this->courierPrice ?? 0;
        // $invoice->card = $this->tempOrder['card'];
        $invoice->url_secret = bin2hex(random_bytes(4));
        $invoice->save();
        $invoice->setProdcuts($this->tempOrder['card']);
        $invoice->setTotalPrice();
        $invoice->save();


        // $this->invoices = Invoice::all();
        $this->reset(['tempOrder', 'customerName', 'customerMobile', 'invoice', 'courierPrice', 'discountPrice']);
        session()->flash('message', 'فاکتور با موفقیت ثبت شد.');
        $this->dispatch('invoiceSaved');
    }

    public function editInvoice($invoiceId)
    {
        $this->reset(['tempOrder', 'customerName', 'customerMobile']);
        $this->invoice = Invoice::findOrFail($invoiceId);
        $this->tempOrder['card'] = [];
        foreach ($this->invoice->products as $product) {
            if (isset($this->tempOrder['card'][$product->id])) {
                $this->tempOrder['card'][$product->id] += $product->pivot->quantity;
            } else {
                $this->tempOrder['card'][$product->id] = $product->pivot->quantity;
            }
        }
        $this->customerName = $this->invoice->user->name;
        $this->customerMobile = $this->invoice->user->mobile;
        $this->courierPrice = $this->invoice->courier_price;
        $this->discountPrice = $this->invoice->discount_price;
    }

    public function cancelEditingInvoice()
    {
        $this->reset(['tempOrder', 'customerName', 'customerMobile', 'invoice', 'courierPrice', 'discountPrice']);
    }

    public function showPaymentModal($invoice) {}


    //    ----------------------------------------------------


    public function showPayment($invoicePayments)
    {
        $this->invoicePayments = Invoice::where('id', $invoicePayments['id'])->first();
        $this->invoice_price = $this->invoicePayments->total_price -= $this->invoicePayments->paid_amount;
        $this->paid_amount = $this->invoicePayments->paid_amount;
        $this->transactions = $this->invoicePayments->transactions;
        $this->showModal = true;
    }

    public function addPayment()
    {
        $this->payments[] = new Payment();
    }

    public function savePayment()
    {
        $this->validate([
            'account' => 'required|exists:accounts,id',
            'j_date' => 'required',
            'amount' => 'required|integer',
        ], [
            'account' => 'فیلد حساب اجباری است!',
            'j_date' => 'فیلد تاریخ اجباری است!',
            'amount' => 'فیلد مبلغ اجباری است!',
        ]);
        $this->transaction_date = Jalalian::fromFormat('Y/m/d', $this->j_date)->toCarbon();

        $current_balance = Account::find($this->account)->balance;
        $current_balance += $this->amount;
        Account::find($this->account)->update(['balance' => $current_balance]);

        $create = Transaction::create([
            'amount' => $this->amount,
            'type' => 'credit',
            'description' => 'فروش غذا',
            'category_id' => SiteSetting::getValue('INVOICE_PAYMENT_CATEGORY_ID'),
            'account_id' => $this->account,
            'current_balance' => $current_balance,
            'transaction_date' => $this->transaction_date,
            'invoice_id' => $this->invoicePayments->id,
        ]);



        if ($create) {
            // $this->paid_amount += $this->amount;
            // Invoice::where('id', $this->invoicePayments->id)->update([
            //     'paid_amount' => $this->paid_amount,
            // ]);
            $this->transactions = $this->invoicePayments->transactions;
            $this->invoice_price -= $this->amount;
            $this->reset('payments', 'account', 'j_date', 'amount', 'transaction_date');
        }
    }


    public function mount()
    {
        $this->products = Product::all();
    }

    public function render()
    {
        return view('livewire.order.view', [
            'invoices' => Invoice::simplePaginate(10)
        ]);
    }
}
