<?php

namespace App\Livewire\JournalEntry;

use App\Models\JournalEntry;
use Livewire\Component;
use Livewire\WithPagination;

class JournalEntryIndex extends Component
{
    use WithPagination;

    public $perPage = 15;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $statusFilter = '';

    public function mount()
    {
        $this->loadEntries();
    }

    public function loadEntries()
    {
        // Entries loaded in render method
    }

    public function changeStatus($entryId, $newStatus)
    {
        if (!in_array($newStatus, ['draft', 'posted'])) {
            session()->flash('error', 'وضعیت نامعتبر است.');
            return;
        }

        $entry = JournalEntry::find($entryId);
        if (!$entry) {
            session()->flash('error', 'سند پیدا نشد.');
            return;
        }

        // Only allow changing from draft to posted
        if ($entry->status === 'draft' && $newStatus === 'posted') {
            $entry->postEntry();
            session()->flash('success', 'سند با موفقیت تغییر به قطعی‌شده شد!');
        } elseif ($entry->status === 'posted' && $newStatus === 'draft') {
            session()->flash('error', 'نمی‌توان یک سند قطعی‌شده را به پیش‌نویس تغییر داد.');
        } else {
            session()->flash('warning', 'این تغییر وضعیت امکان‌پذیر نیست.');
        }
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = JournalEntry::query();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $entries = $query
            ->with('items')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.journal-entry.journal-entry-index', [
            'entries' => $entries,
        ]);
    }
}
