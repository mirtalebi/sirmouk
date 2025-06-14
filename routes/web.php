<?php

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('order', 'order.view')->name('order');
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
