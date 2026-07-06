<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Account extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_id',
        'accountable_id',
        'accountable_type',
        'balance',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'json',
    ];

    /**
     * Parent account (for hierarchical structure)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Child accounts (for hierarchical tree)
     */
    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * All journal entry items associated with this account
     */
    public function items(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class, 'account_id');
    }

    /**
     * Get current balance from cache
     */
    public function getBalance(): int
    {
        return (int) $this->balance ?? 0;
    }

    /**
     * Recalculate balance from journal_entry_items
     * Balance = Sum(debit) - Sum(credit)
     */
    public function recalculateBalance(): void
    {
        $totalDebit = $this->items()->where('debit', '>', 0)->sum('debit');
        $totalCredit = $this->items()->where('credit', '>', 0)->sum('credit');
        
        $this->balance = $totalDebit - $totalCredit;
        $this->save();
    }

    /**
     * Get all children recursively (for tree building)
     */
    public function getChildrenRecursive()
    {
        $children = collect();
        
        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getChildrenRecursive());
        }
        
        return $children;
    }

    /**
     * Scope: Get root accounts (parent_id is null)
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Get only accounts of a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Find all detailed accounts under a parent account (by parent code)
     */
    public static function findDetailedAccountsByParentCode($parentCode)
    {
        $parentAccount = self::where('code', $parentCode)->first();
        if (!$parentAccount) {
            return collect();
        }
        
        return $parentAccount->children();
    }

    /**
     * Check if this account is a detailed account with receivables parent
     */
    public function isReceivableDetailAccount(): bool
    {
        return $this->parent_id !== null && $this->parent?->code === '102';
    }

    /**
     * اسکوپ برای گرفتن فقط حساب‌های تفصیلی (حساب‌هایی که فرزندی ندارند)
     */
    public function scopeOnlyDetailed($query)
    {
        return $query->whereNotExists(function ($subQuery) {
            $subQuery->select(DB::raw(1))
                ->from('accounts', 'children')
                ->whereRaw('children.parent_id = accounts.id');
        });
    }

    /**
     * چک کردن اینکه آیا این حساب مجاز به ثبت سند هست یا خیر
     */
    public function isDetailedAccount(): bool
    {
        return !\App\Models\Account::where('parent_id', $this->id)->exists();
    }
}

