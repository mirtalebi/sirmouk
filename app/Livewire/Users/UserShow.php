<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;

class UserShow extends Component
{
    public $showModal = false;
    public $id;
    public $name;
    public $mobile;

    public function mount($id){}

    public function openModal()
    {
        $this->showModal = true;
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'mobile']);
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'string|max:11|min:11',
        ],[
            'required' => 'این فیلد اجباری است',
            'min' => 'این فیلد باید 11 کارکتر باشد',
            'max' => 'این فیلد باید 11 کارکتر باشد'
        ]);
        $user = User::findOrFail($this->id);
        $update = $user->update([
            'name' => $this->name,
            'mobile' => $this->mobile,
        ]);
        if($update){
            return redirect()->route('users.show', $this->id)->with('success', 'اطلاعات کاربر با موفقیت ویرایش شد!');
        }else{
            $this->reset(['name', 'mobile']);
            return redirect()->back()->with('fail', 'ویرایش کاربر با مشکل مواجه شد! دوباره تلاش کنید');
        }
    }

    public function render()
    {
        $user = User::findOrFail($this->id);
        $invoices = $user->invoices;
        $this->name = $user->name;
        $this->mobile = $user->mobile;
        return view('livewire.users.user-show', compact('user', 'invoices'));
    }
}
