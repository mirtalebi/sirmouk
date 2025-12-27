<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    protected $fillable = [
        'amount',
        'description',
        'type',
        'category_id',
        'account_id',
        'current_balance',
        'transaction_date',
        'invoice_id',
        'tracking_code',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];


//    Create transaction
    public static function makeTransaction(int $amount, $type, $description, $category_id, $account_id, $date, $invoice_id = null)
    {
        try {
            DB::beginTransaction();
//            Save new balance
            $account = Account::findOrFail($account_id);
            $current_balance = $account->balance + $amount;
            $account->balance = $current_balance;
            $account->save();

//            Create Transaction
            $transaction = Transaction::create([
                'amount' => $amount,
                'type' => $type,
                'description' => $description,
                'category_id' => $category_id,
                'account_id' => $account_id,
                'current_balance' => (int) $current_balance,
                'transaction_date' => $date,
                'invoice_id' => $invoice_id,
            ]);
            DB::commit();
            return $transaction;
        }catch (\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }


    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    public function category(){
        return $this->belongsTo(TransactionCategory::class);
    }

}
