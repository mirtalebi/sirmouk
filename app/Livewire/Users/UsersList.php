<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;

class UsersList extends Component
{
    public $search = "";

    public function render()
    {
        $users = User::when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('mobile', 'like', '%' . $this->search . '%');
            });
        })
            ->paginate(10);
        return view('livewire.users.users-list', compact('users'));
    }
}
