<?php

namespace App\Livewire\Order;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class View extends Component
{
    use WithPagination;

    public Invoice $invoice;
    // public $invoices = [];
    public $products = [];
    public $payments = [];
    public array $tempOrder = [
        'card' => [],
    ];
    public $customerName = '';
    public $customerMobile = '';
    public $courierPrice, $discountPrice;

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

    public function addPayment()
    {
        $this->payments[] = new Payment();
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
        $invoice->card = $this->tempOrder['card'];
        $invoice->url_secret = bin2hex(random_bytes(4));
        $invoice->save();
        $invoice->setProdcuts($this->tempOrder['card']);

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
