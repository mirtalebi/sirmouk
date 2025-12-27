<?php

namespace App\Livewire\Order;

use App\Livewire\Invoice\InvoicePayment;
use App\Models\Account;
use App\Models\Address;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SiteSetting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
    public $courierPrice,$discountPrice;

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
    public $snap;
    public $addresses = [];
    public $address_id;
    public $add_address = [];
    public $address_label;
    public $user;

    public $addedPackagingPrice;
    public $total_price = 0;
    public $packaging_price = 0;

//    -----------
    public $userModal = false;


    public function updateUserModal()
    {
        $this->userModal = true;
    }


    public function findUser()
    {
        $this->reset(['customerName', 'addresses', 'add_address']);
        if (!empty($this->customerMobile)){
            $this->user = User::where('mobile', $this->customerMobile)->first();
        }
        if ($this->user){
            $this->customerName = $this->user->name;
            $this->addresses = $this->user->addresses;
            if (!$this->user->addresses->count() > 0){
                $this->addAddressInput();
            } elseif(!isset($this->invoice)){
                $this->address_id = $this->user->addresses->first()->id;
            }
        }else{
            $this->addAddressInput();
        }
    }

    public function addAddressInput()
    {
        if (empty($this->add_address)) {
            $this->add_address[] = null;
        }
    }
    public function saveAddress()
    {
        $this->validate([
            'customerMobile' => 'required|string|max:11|min:11',
            'address_label' => 'required',
        ],[
            'required' => 'این فیلد اجباری است',
        ]);
        if (!empty($this->customerMobile)){
            $this->user = User::getUserByMobile($this->customerMobile);
        }
        if (!$this->user){
            return redirect()->back()->with('fail', 'کاربر مورد نطر پیدا نشد!');
        }
        $address = Address::create([
            'address' => $this->address_label,
            'user_id' => $this->user->id
        ]);
        if ($address){
            $this->reset(['address_label', 'add_address']);
            $this->address_id = $address->id;
            $this->addresses = $this->user->addresses;
            return redirect()->back()->with('success', 'آدرس مورد نظر اضافه شد!');
        }
    }

    public function saveInvoice($tempBasket)
    {
        $this->total_price += (int) $this->addedPackagingPrice;
        $this->packaging_price += (int) $this->addedPackagingPrice;
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerMobile' => 'string|max:11|min:11',
        ],[
            'required' => 'این فیلد اجباری است',
            'min' => 'این فیلد باید شامل 11 کارکتر باشد',
            'max' => 'این فیلد باید شامل 11 کارکتر باشد'
        ]);
        if (!empty($this->customerMobile)){
            $user = User::getUserByMobile($this->customerMobile);
            $user->name = $this->customerName;
            $user->save();
        }

        try {
            DB::beginTransaction();
            if (!empty($this->address_label)){
                $this->saveAddress();
            }
            if (isset($this->invoice)) {
                $invoice = $this->invoice;
            } else {
                $invoice = new Invoice();
            }
            $invoice->user_id = $user->id ?? null;
            $invoice->discount_price = (int)$this->discountPrice ?? 0;
            $invoice->courier_price = (int)$this->courierPrice ?? 0;
            $invoice->packaging_price = (int)$this->packaging_price ?? 0;
            $invoice->url_secret = bin2hex(random_bytes(4));
            $invoice->is_snap = $this->snap ?? false;
            if ($this->snap){ $invoice->snap_user_credentials = json_encode(['username' => $this->customerName, 'mobile' => $this->customerMobile ?? null]); }
            $invoice->address_id = empty($this->address_id) ? null : $this->address_id;
            $invoice->save();
            $invoice->setProdcuts($tempBasket);
            $invoice->setTotalPrice();
//            dd('courier_price:' . $invoice->courier_price, 'discount_price:' . $invoice->discount_price, 'packaging_price:' . $invoice->packaging_price, 'total_price:' . $invoice->total_price);
            $invoice->save();
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception->getMessage());
        }

        $this->reset(['customerName', 'customerMobile', 'invoice', 'courierPrice', 'discountPrice', 'snap', 'addresses', 'address_id', 'addedPackagingPrice', 'total_price']);
        session()->flash('message', 'فاکتور با موفقیت ثبت شد.');
        $this->dispatch('basket-updated', basket: []);
        $this->dispatch('invoiceSaved');
    }

    public function editInvoice($invoiceId)
    {
        $this->reset(['tempOrder', 'customerName', 'customerMobile']);
        $this->invoice = Invoice::findOrFail($invoiceId);
        $basket = [];
        foreach ($this->invoice->products as $product) {
            if (isset($basket[$product->id])) {
                $basket[$product->id] += $product->pivot->quantity;
            } else {
                $basket[$product->id] = $product->pivot->quantity;
            }
        }
        $this->dispatch('basket-updated', basket: $basket);

        $this->courierPrice = $this->invoice->courier_price;
        $this->discountPrice = $this->invoice->discount_price;
        $this->address_id = $this->invoice->address_id;

        $this->snap = boolval($this->invoice->is_snap);
//        dd($this->invoice, $this->snap);
        if ($this->invoice->is_snap) {
            $snapCred = json_decode($this->invoice->snap_user_credentials, true);
            $this->customerMobile = $snapCred['mobile'];
            $this->customerName = $snapCred['username'];
        } else if ($this->invoice->user) {
            $this->customerMobile = $this->invoice->user->mobile;
            $this->customerName = $this->invoice->user->name;
        }
        $this->findUser();
    }

    public function cancelEditingInvoice()
    {
        $this->reset(['tempOrder', 'customerName', 'customerMobile', 'invoice', 'courierPrice', 'discountPrice', 'snap', 'addresses', 'address_id']);
    }

    //    ----------------------------------------------------


    public function showPayment($invoicePayments)
    {
        $this->invoicePayments = Invoice::where('id', $invoicePayments['id'])->first();

        $this->invoice_price = $this->invoicePayments->total_price -= $this->invoicePayments->paid_amount;
        $this->paid_amount = $this->invoicePayments->paid_amount;
        $this->transactions = $this->invoicePayments->transactions;
        $this->amount = $this->invoice_price;

        if (empty($this->payments)){
            $this->payments[] = new Payment();
        }
        if ($this->invoice_price <= 0){
            $this->payments = [];
        }
        $this->showModal = true;
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
            'required' => 'فیلد مبلغ اجباری است!',
        ]);
        $this->transaction_date = Jalalian::fromFormat('Y/m/d', $this->j_date)->toCarbon();

        $invoice_payment_category_id = SiteSetting::getValue('INVOICE_PAYMENT_CATEGORY_ID');

        if ($invoice_payment_category_id == 'null' || $invoice_payment_category_id == null) {
            return redirect()->back()->with('fail', 'مقدار INVOICE_PAYMENT_CATEGORY_ID در دیتابیس تنظیم نشده است!');
        }

        $create = Transaction::makeTransaction($this->amount, 'credit', 'فروش غذا', $invoice_payment_category_id, $this->account, $this->transaction_date, $this->invoicePayments->id);
        if ($create) {
            $this->transactions = $this->invoicePayments->transactions;
            $this->invoice_price -= $this->amount;
            $this->reset('payments', 'amount', 'transaction_date');
            $this->j_date = Jalalian::now()->format('Y/m/d');
            $this->account = SiteSetting::getValue('INVOICE_PAYMENT_ACCOUNT_ID');
            $this->paid_amount = $this->invoicePayments->paid_amount;
        }
    }


    public function mount()
    {
        $this->products = Product::all();
        $this->j_date = Jalalian::now()->format('Y/m/d');
        $this->account = SiteSetting::getValue('INVOICE_PAYMENT_ACCOUNT_ID');
    }

    public function render()
    {
        return view('livewire.order.view', [
            'invoices' => Invoice::simplePaginate(10),
            'categories' => ProductCategory::all(),
        ]);
    }

    public function printInvoice($id) {
        $invoice = Invoice::findOrFail($id);
        $this->dispatch('print-invoice-client', customer: [
            'id' => $invoice->id,
            'name' => $invoice->user?->name ?? json_decode($invoice->snap_user_credentials, true)['username'],
            'mobile' => $invoice->user?->mobile ?? json_decode($invoice->snap_user_credentials, true)['mobile'],
            'address' => $invoice->address?->address,
            'discount_price' => $invoice->discount_price,
            'courier_price' => $invoice->courier_price,
        ] ,basket: $invoice->products);

    }
}
