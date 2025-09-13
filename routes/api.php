<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Invoice;
use Carbon\Carbon;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/reporter/invoice-data', function () {

    $invoices = Invoice::where('created_at', '>=', Carbon::now()->subDays($request->days ?? 30))->get();
    $result = [];
    foreach ($invoices as $invoice) {
        $r = $invoice->toArray();
        $r['user_name'] = $invoice->user->name;
        $r['user_email'] = $invoice->user->email;
        $r['items_count'] = $invoice->products->count();
        $r['paid_amount'] = $invoice->transactions()->sum('amount');
        $result[] = $r;
    }
    return response()->json($result);

});
