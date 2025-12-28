<?php

namespace App\Livewire\Transaction;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class TransactionList extends Component
{

    public $account_id = null;
    public $transaction_id = [];
    public $deleteModal = false;
    public $selectedTransaction = [];

    public function mount($transaction_id = [])
    {
        $this->transaction_id = $transaction_id ?? [];
    }

    public function showModal($id)
    {
        $this->deleteModal = true;
        $this->selectedTransaction = Transaction::findOrFail($id);
    }
    public function deleteTransaction($id)
    {
        try {
            DB::beginTransaction();
            $transaction = Transaction::findOrFail($id);
            $account = Account::findOrFail($transaction->account_id);
            $balance = $account->balance;
            $amount = $transaction->amount * -1;
            $new_balance = $balance + $amount;
            $account->update([
                'balance' => $new_balance
            ]);
            $transaction->delete();
            DB::commit();
            $this->deleteModal = false;
            $this->selectedTransaction = [];
        }catch (\Exception $exception){
            DB::rollBack();
            $this->deleteModal = false;
            $this->selectedTransaction = [];
            return redirect()->back()->with('error', 'حذف تراکنش با مشکل مواجه شد! دوباره تلاش کنید.');
        }
    }
    public function cancelDeleting()
    {
        $this->deleteModal = false;
        $this->selectedTransaction = [];
    }


//    #[On('transaction-created')]
//    public function refreshList()
//    {
//        $this->resetPage();
//    }

    #[On('transaction-created')]

    public function render()
    {
        $transactions = Transaction::query()
            ->when($this->account_id, fn($query) => $query->where('account_id', $this->account_id))
            ->when($this->transaction_id && count($this->transaction_id) > 0,
                fn($query) => $query->whereIn('id', $this->transaction_id)
            )
            ->latest()
            ->paginate(15);
        return view('livewire.transaction.transaction-list', compact('transactions'));
    }
}
