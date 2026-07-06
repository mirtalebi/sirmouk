<div class="max-w-7xl mx-auto py-10 px-4">
    <!-- Messages -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="bg-green-100 text-green-800 p-4 rounded-lg shadow-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="bg-red-100 text-red-800 p-4 rounded-lg shadow-md mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">لیست اسناد حسابداری</h1>
        <a href="{{ route('journal-entries.create') }}"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            سند جدید
        </a>
    </div>

    <!-- Filter -->
    <div
        class="bg-white dark:bg-neutral-900 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 p-6 mb-6">
        <div class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-bold mb-2 dark:text-white">فیلتر وضعیت</label>
                <select wire:model.live="statusFilter"
                    class="px-4 py-2 rounded-lg border-2 border-gray-300 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white focus:border-blue-500">
                    <option value="">همه</option>
                    <option value="draft">پیش‌نویس</option>
                    <option value="posted">قطعی‌شده</option>
                </select>
            </div>
            <button wire:click="$refresh"
                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold">
                به‌روزرسانی
            </button>
        </div>
    </div>

    <!-- Table -->
    <div
        class="bg-white dark:bg-neutral-900 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
                    <tr>
                        <th class="px-6 py-4 text-right font-bold text-gray-800 dark:text-white cursor-pointer hover:bg-gray-200 dark:hover:bg-neutral-700"
                            wire:click="sortBy('id')">
                            شناسه
                            @if ($sortBy === 'id')
                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th class="px-6 py-4 text-right font-bold text-gray-800 dark:text-white cursor-pointer hover:bg-gray-200 dark:hover:bg-neutral-700"
                            wire:click="sortBy('entry_date')">
                            تاریخ سند
                            @if ($sortBy === 'entry_date')
                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th class="px-6 py-4 text-right font-bold text-gray-800 dark:text-white">شرح</th>
                        <th class="px-6 py-4 text-center font-bold text-gray-800 dark:text-white">وضعیت</th>
                        <th class="px-6 py-4 text-center font-bold text-gray-800 dark:text-white">مبلغ کل</th>
                        <th class="px-6 py-4 text-center font-bold text-gray-800 dark:text-white">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @forelse ($entries as $entry)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition">
                            <td class="px-6 py-4 text-right font-bold dark:text-white">
                                #{{ $entry->id }}
                            </td>
                            <td class="px-6 py-4 text-right dark:text-white">
                                {{ jdate($entry->entry_date)->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 text-right dark:text-white">
                                {{ Str::limit($entry->description ?? '-', 30) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-bold
                                    @if ($entry->status === 'draft') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif ($entry->status === 'posted')
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif
                                ">
                                    {{ $entry->status === 'draft' ? 'پیش‌نویس' : 'قطعی‌شده' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold dark:text-white">
                                {{ number_format($entry->getTotalAmount()) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('journal-entries.show', $entry->id) }}"
                                        class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded font-bold">
                                        نمایش
                                    </a>
                                    @if ($entry->status === 'draft')
                                        <button wire:click="changeStatus({{ $entry->id }}, 'posted')"
                                            class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded font-bold"
                                            onclick="return confirm('آیا مطمئن هستید؟')">
                                            قطعی‌کن
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                هیچ سندی ثبت نشده است
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-800 border-t border-gray-200 dark:border-neutral-700">
            {{ $entries->links() }}
        </div>
    </div>
</div>
