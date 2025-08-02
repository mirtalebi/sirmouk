<?php

use App\Models\Account;
use App\Models\Invoice;
use App\Models\SiteSetting;
use App\Models\Transaction;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $amount = 0;

    $invoices = \App\Models\Invoice::where('is_snap', 1)
        ->whereDate('created_at', \Carbon\Carbon::today())
        ->get();

    if (!$invoices->isEmpty()) {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            foreach ($invoices as $invoice) {
                $amount += $invoice->total_price;
            }

            $snap_commission_percentage = SiteSetting::getValue('SNAP_COMMISSION_PERCENTAGE');
            $amount = $amount - ($amount * $snap_commission_percentage / 100);

            $inoice_payment_category_id = SiteSetting::getValue('INVOICE_PAYMENT_CATEGORY_ID');
            if ($inoice_payment_category_id == 'null' || $inoice_payment_category_id == null) {
                return redirect()->back()->with('fail', 'مقدار INVOICE_PAYMENT_CATEGORY_ID در دیتابیس تنظیم نشده است!');
            }
            $account = SiteSetting::getValue('SNAP_ACCOUNT_ID');

            $current_balance = Account::find($account)->balance;
            $current_balance += $amount;
            Account::find($account)->update(['balance' => $current_balance]);

            $create = Transaction::create([
                'amount' => $amount,
                'type' => 'credit',
                'description' => 'فروش غذا در اسنپ',
                'category_id' => $inoice_payment_category_id,
                'account_id' => $account,
                'current_balance' => $current_balance,
                'transaction_date' => \Carbon\Carbon::today(),
            ]);
            \Illuminate\Support\Facades\Log::info("$amount تراکنش فروش غذا در اسنپ ثبت شد! مجموع قیمت: ");
            \Illuminate\Support\Facades\DB::commit();
        }catch (\Exception $exception){
            \Illuminate\Support\Facades\Log::error($exception);
            \Illuminate\Support\Facades\DB::rollBack();
        }
    }
})->dailyAt('21:00');
