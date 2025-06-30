<?php

namespace App\Livewire\Account;

use App\Models\Account;
use Livewire\Component;

class AccountIndex extends Component
{

    public $accounts;
    public function mount(){
        $this->loadAcounts();
    }
    public function loadAcounts()
    {
        $this->accounts = Account::all()->sortByDesc('created_at');
    }

    public function delete($id)
    {
        $delete = Account::find($id)->delete();
        if($delete){
            $this->loadAcounts();
            session()->flash('success', 'حساب با موفقیت حذف شد!');

        }
    }

    public function render()
    {
        return view('livewire.account.account-index');
    }
}
