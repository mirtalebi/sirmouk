<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalEntry extends Model
{
    protected $fillable = [
        'entry_date',
        'description',
        'status',
        'reference_type',
        'reference_id'
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    /**
     * ارتباط با سطرهای ریز سند حسابداری
     */
    public function items(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    /**
     * ارتباط با فاکتور یا مرجع صادرکننده سند (پلی‌مورفیک)
     * مثلاً مشخص می‌کند این سند متعلق به کدام فاکتور فروش است
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * چک کردن متوازن (تراز) بودن سند
     * جمع بدهکار باید با جمع بستانکار برابر باشد
     */
    public function isBalanced(): bool
    {
        $totalDebit = $this->items()->sum('debit');
        $totalCredit = $this->items()->sum('credit');
        
        return $totalDebit === $totalCredit;
    }

    /**
     * Check if entry can be posted (validation)
     */
    public function canPost(): bool
    {
        return $this->isBalanced() && 
               $this->entry_date !== null && 
               $this->items()->count() >= 2;
    }

    /**
     * Post the journal entry: set status to posted and update account balances
     */
    public function postEntry(): void
    {
        if (!$this->canPost()) {
            throw new \Exception('Cannot post unbalanced or incomplete entry');
        }

        $this->status = 'posted';
        $this->save();
        $this->updateAccountBalances();
    }

    /**
     * Update balance cache for all affected accounts
     * Called whenever journal entry items are created/modified
     * Calculates from ALL items (draft + posted) for real-time data
     */
    public function updateAccountBalances(): void
    {
        $affectedAccounts = $this->items()
            ->select('account_id')
            ->distinct()
            ->pluck('account_id')
            ->toArray();

        foreach ($affectedAccounts as $accountId) {
            $account = Account::find($accountId);
            if ($account) {
                $account->recalculateBalance();
            }
        }
    }

    /**
     * Get total debit for this entry
     */
    public function getTotalDebit(): int
    {
        return (int) $this->items()->sum('debit');
    }

    /**
     * Get total credit for this entry
     */
    public function getTotalCredit(): int
    {
        return (int) $this->items()->sum('credit');
    }

    /**
     * Get total amount (debit or credit, whichever is larger)
     */
    public function getTotalAmount(): int
    {
        return max($this->getTotalDebit(), $this->getTotalCredit());
    }
}