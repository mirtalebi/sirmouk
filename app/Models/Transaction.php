<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    public function category(){
        return $this->belongsTo(TransactionCategory::class);
    }

}
