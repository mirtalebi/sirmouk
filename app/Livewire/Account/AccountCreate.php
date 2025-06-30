<?php

namespace App\Livewire\Account;

use App\Models\Account;
use Livewire\Component;

class AccountCreate extends Component
{
    public $name;
    public $balance;
    public $type;
public function save()
{
    $this->validate([
        'name' => 'required|string',
        'balance' => 'required|integer',
        'type' => 'required',
    ]);

    $account = Account::create([
        'name' => $this->name,
        'balance' => $this->balance,
        'type' => $this->type,
    ]);
    if ($account) {
        return redirect()->route('accounts.index')->with('success', 'حساب با موفقیت ساخته شد!');
    }

    dd($this->name, $this->balance, $this->type);
}

    public function render()
    {
        return view('livewire.account.account-create');
    }
}
