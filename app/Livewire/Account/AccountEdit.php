<?php

namespace App\Livewire\Account;

use App\Models\Account;
use Livewire\Component;

class AccountEdit extends Component
{
    public $name;
    public $balance;
    public $type;
    public $account;

    public function mount($id){
        $this->account = Account::find($id);

        $this->name = $this->account->name;
        $this->balance = $this->account->balance;
        $this->type = $this->account->type;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string',
            'balance' => 'required|integer',
            'type' => 'required'
        ]);

        $update = Account::find($this->account->id)->update([
            'name' => $this->name,
            'balance' => $this->balance,
            'type' => $this->type
        ]);

        if ($update) {
            return redirect()->route('accounts.index')->with('success', 'حساب شما با موفقیت ویرایش شد!');
        }

    }

    public function render()
    {
        return view('livewire.account.account-edit');
    }
}
