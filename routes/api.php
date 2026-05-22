<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Invoice;
use Carbon\Carbon;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/reporter/invoice-data', function (Request $request) {

    $days = $request->days ?? 30;
    $skipDays = $request->skip_days ?? 0;

    $startDate = Carbon::now()
        ->subDays($skipDays + $days); // go back 'days + skipDays'
    $endDate = Carbon::now()
        ->subDays($skipDays);         // stop at skipDays ago

    $invoices = Invoice::whereBetween('created_at', [$startDate, $endDate])->get();
    $result = [];
    foreach ($invoices as $invoice) {
        $r = $invoice->toArray();
        $r['user_name'] = $invoice->user->name ?? json_decode($invoice->snap_user_credentials, true)['username'] ?? '';
        $r['user_mobile'] = $invoice->user->mobile ?? json_decode($invoice->snap_user_credentials, true)['mobile'] ?? '';
        // $r['user_email'] = $invoice->user->email;
        $r['items_count'] = $invoice->products->count();
        $r['paid_amount'] = $invoice->transactions()->sum('amount');
        $result[] = $r;
    }
    return response()->json(['status' => 'success', 'data' => $result]);
});
