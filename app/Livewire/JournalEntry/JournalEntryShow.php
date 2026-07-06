<?php

namespace App\Livewire\JournalEntry;

use App\Models\JournalEntry;
use Livewire\Component;

class JournalEntryShow extends Component
{
    public $entryId;
    public $entry;

    public function mount($id)
    {
        $this->entryId = $id;
        $this->loadEntry();
    }

    public function loadEntry()
    {
        $this->entry = JournalEntry::with(['items.account'])->find($this->entryId);

        if (!$this->entry) {
            abort(404, 'سند پیدا نشد.');
        }
    }

    public function changeStatus($newStatus)
    {
        if (!in_array($newStatus, ['draft', 'posted'])) {
            session()->flash('error', 'وضعیت نامعتبر است.');
            return;
        }

        if ($this->entry->status === 'draft' && $newStatus === 'posted') {
            $this->entry->postEntry();
            session()->flash('success', 'سند با موفقیت تغییر به قطعی‌شده شد!');
            $this->loadEntry();
        } elseif ($this->entry->status === 'posted' && $newStatus === 'draft') {
            session()->flash('error', 'نمی‌توان یک سند قطعی‌شده را به پیش‌نویس تغییر داد.');
        } else {
            session()->flash('warning', 'این تغییر وضعیت امکان‌پذیر نیست.');
        }
    }

    public function render()
    {
        return view('livewire.journal-entry.journal-entry-show', [
            'entry' => $this->entry,
        ]);
    }
}
