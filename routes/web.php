<?php

use App\Livewire\Products\ProductCreate;
use App\Livewire\Transaction\TransactionCreate;
use App\Livewire\Transaction\TransactionIndex;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Account\AccountIndex;
use App\Livewire\Account\AccountCreate;
use App\Livewire\Account\AccountEdit;
use App\Livewire\Account\TransactionsList;
use App\Livewire\Transaction\TransactionsFilter;
use App\Livewire\Invoice\InvoiceCalc;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/invoice/{invoiceId}/{secretKey?}', function ($invoiceId, $secretKey) {
    $invoice = Invoice::findOrFail($invoiceId);
    if ($invoice->url_secret !== $secretKey) {
        abort(404);
    }
    return view('components.layouts.invoice', compact('invoice'));
})->name('invoice.view');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
//    Accounts Routes
    Route::get('/accounts', AccountIndex::class)->name('accounts.index');
    Route::get('/accounts/create', AccountCreate::class)->name('accounts.create');
    Route::get('/accounts/{id}/edit', AccountEdit::class)->name('accounts.edit');
    Route::get('/account/{id}/transactions/', TransactionsList::class)->name('account.transactions.list');


//    Transactions Routes
    Route::get('/transactions', TransactionIndex::class)->name('transactions.index');
    Route::get('/transactions/filterByDate', TransactionsFilter::class)->name('transactions.filterByDate');

//    Invoice
    Route::get('/invoiceCalc', InvoiceCalc::class)->name('invoice.calc');

//    Product CRUD
    Route::prefix('products/')->group(function () {
        Route::get('', \App\Livewire\Products\Products::class)->name('products');
        Route::get('/edit/{id}', \App\Livewire\Products\ProductEdit::class)->name('products.edit');
        Route::get('create', ProductCreate::class)->name('products.create');
    });

    //        Product Categories
    Route::get('categories', \App\Livewire\Catgories\Categories::class)->name('categories');


    Route::get('/products/sell', \App\Livewire\ProductSells::class)->name('products.sell');
});


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('order', 'order.view')->name('order');
    Volt::route('order/{id}', 'invoice.invoice-payment')->name('order.payment');
});

Route::get('convert', function () {
    foreach (Invoice::all() as $invoice) {
        foreach ($invoice->card as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $invoice->products()->attach($productId, [
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'discount_price' => 0,
                    'tax' => $product->tax * $quantity * $product->price / 100,
                ]);
                $invoice->save();
            }
        }
    }
    return 'DONE';
});

require __DIR__ . '/auth.php';
