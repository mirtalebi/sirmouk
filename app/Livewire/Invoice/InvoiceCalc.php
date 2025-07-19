<?php

namespace App\Livewire\Invoice;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Arr;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class InvoiceCalc extends Component
{
    public $from_date = null;
    public $to_date = null;
    public $invoices = [];
    public $invoice_calc = 0;
    public $selectedProducts = [];

    public function submit(){

        $this->validate([
            'from_date' => 'required',
            'to_date' => 'required',
        ],[
            'required' => 'این فیلد اجباری است!'
        ]);
        $this->invoice_calc = 0;

        $saveSelectedProducts = $this->selectedProducts;

        $this->selectedProducts = Arr::where($this->selectedProducts, function ($value, $key) {
            return $value == true;
        });
        $this->selectedProducts = array_keys($this->selectedProducts);


        $from_date = Jalalian::fromFormat('Y/m/d', $this->from_date)->toCarbon();
        $to_date = Jalalian::fromFormat('Y/m/d', $this->to_date)->toCarbon()->endOfDay();


        $this->invoices = Invoice::where('created_at', '>=', $from_date)
            ->where('created_at', '<=', $to_date)
            ->when($this->selectedProducts, function ($query) {
                $query->whereHas('products', function ($query) {
                    $query->whereIn('products.id', $this->selectedProducts);
                });
            })
            ->get();

        foreach ($this->invoices as $invoice) {
            $this->invoice_calc += $invoice->total_price;
        }

        $this->selectedProducts = $saveSelectedProducts;
    }

    public function render()
    {
        $products = Product::all();
        return view('livewire.invoice.invoice-calc', ['invoices' => $this->invoices, 'invoice_calc' => $this->invoice_calc, 'products' => $products]);
    }
}
