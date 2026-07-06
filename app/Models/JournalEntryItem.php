<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntryItem extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'customer_id',
        'debit',
        'credit',
        'description'
    ];

    /**
     * اتصال سطر به هدر سند
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    /**
     * اتصال سطر به حساب معین یا تفصیلی مربوطه در جدول accounts
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * سکوپ اختصاصی برای فیلتر کردن تراکنش‌های یک مشتری خاص
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}