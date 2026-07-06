<?php

use App\Livewire\Products\ProductCreate;
use App\Livewire\Transaction\TransactionCreate;
use App\Livewire\Transaction\TransactionIndex;
use App\Livewire\Transaction\TransactionsFilter;
use App\Livewire\Account\AccountIndex;
use App\Livewire\Account\AccountCreate;
use App\Livewire\Account\AccountEdit;
use App\Livewire\Account\TransactionsList;
use App\Livewire\JournalEntry\JournalEntryCreate;
use App\Livewire\JournalEntry\JournalEntryIndex;
use App\Livewire\JournalEntry\JournalEntryShow;
use App\Livewire\Invoice\InvoiceCalc;
use App\Livewire\RecipeCard\Index as RecipeCardIndex;
use App\Livewire\ShoppingList\ShoppingList;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
})->name('home');
// Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/invoice/{invoiceId}/{secretKey?}', function ($invoiceId, $secretKey) {
    $invoice = Invoice::findOrFail($invoiceId);
    if ($invoice->url_secret !== $secretKey) {
        abort(404);
    }
    return view('components.layouts.invoice', compact('invoice'));
})->name('invoice.view');

Route::get('/invoices/aggregate/{invoiceIds}/', function ($invoiceIds) {
    // 1. Convert comma-separated string to an array of IDs
    $idsArray = explode(',', $invoiceIds);

    // 2. Fetch all matching invoices
    $invoices = Invoice::with('products')->whereIn('id', $idsArray)->get();

    // 4. Merge duplicate products and sum quantities
    $mergedProducts = collect();
    
    foreach ($invoices as $invoice) {
        foreach ($invoice->products as $product) {
            $productId = $product->id;

            if ($mergedProducts->has($productId)) {
                // If product already exists in list, just add to the quantity
                $mergedProducts[$productId]->pivot->quantity += $product->pivot->quantity;
            } else {
                // Clone the product object so we don't accidentally mutate Eloquent cache
                $mergedProducts[$productId] = clone $product;
            }
        }
    }

    // 5. Calculate Consolidated Totals
    $totals = [
        'ids'             => $invoices->pluck('id')->toArray(),
        'dates'           => $invoices->map(fn($i) => $i->getCreatedAtDate())->unique()->toArray(),
        'packaging_price' => $invoices->sum('packaging_price'),
        'discount_price'  => $invoices->sum('discount_price'),
        'courier_price'   => $invoices->sum('courier_price'),
        'tax_price'       => $invoices->sum(fn($i) => $i->calcTaxPrice()),
        'total_price'     => $invoices->sum('total_price'),
    ];

    return view('components.layouts.invoice-aggregate', compact('mergedProducts', 'totals'));
})->name('invoice.aggregate');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
//    Accounts Routes
    Route::get('/accounts', AccountIndex::class)->name('accounts.index');
    Route::get('/accounts/create', AccountCreate::class)->name('accounts.create');
    Route::get('/accounts/{id}/edit', AccountEdit::class)->name('accounts.edit');
    Route::get('/account/{id}/transactions/', TransactionsList::class)->name('account.transactions.list');

//    Journal Entry Routes
    Route::get('/journal-entries', JournalEntryIndex::class)->name('journal-entries.index');
    Route::get('/journal-entries/create', JournalEntryCreate::class)->name('journal-entries.create');
    Route::get('/journal-entries/{id}', JournalEntryShow::class)->name('journal-entries.show');

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

    Route::get('/recipe-card', RecipeCardIndex::class)->name('recipe-card.index');

    //        Product Categories
    Route::get('categories', \App\Livewire\Catgories\Categories::class)->name('categories');


//        Users CRUD
    Route::prefix('users/')->group(function () {
        Route::get('', \App\Livewire\Users\UsersList::class)->name('users.list');
        Route::get('{id}', \App\Livewire\Users\UserShow::class)->name('users.show');
    });

    Route::get('/products/sell', \App\Livewire\ProductSells::class)->name('products.sell');
    Route::get('/shopping-list', ShoppingList::class)->name('shopping-list.index');
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

Route::get('/dev', function () {
    
});

Route::get('/cmp', function (Request $request) {
    // Destination URL
    $destination = 'https://sirmok.ir';

    // Prepare log line
    $timestamp = now()->toDateTimeString();
    $ip        = $request->ip();
    $campaign  = $request->query('utm', 'unknown');

    $logLine = sprintf(
        "[%s] IP: %s | Campaign: %s\n",
        $timestamp,
        $ip,
        $campaign
    );

    // Write to a simple log file
    file_put_contents(storage_path('campaigns/clicks'.$campaign.'.log'), $logLine, FILE_APPEND);

    // Redirect to the real destination
    return redirect($destination);
});


require __DIR__ . '/auth.php';
