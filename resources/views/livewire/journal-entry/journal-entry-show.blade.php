<div class="max-w-4xl mx-auto py-10 px-4">
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
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">جزئیات سند #{{ $entry->id }}</h1>
        <a href="{{ route('journal-entries.index') }}"
            class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold">
            بازگشت
        </a>
    </div>

    <!-- Entry Details -->
    <div
        class="bg-white dark:bg-neutral-900 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 p-6 mb-6">
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">تاریخ سند</p>
                <p class="text-lg font-bold dark:text-white">
                    {{ jdate($entry->entry_date)->format('Y/m/d') }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">وضعیت</p>
                <span
                    class="inline-block px-4 py-2 rounded-full text-sm font-bold
                    @if ($entry->status === 'draft') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif ($entry->status === 'posted')
                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif
                ">
                    {{ $entry->status === 'draft' ? 'پیش‌نویس' : 'قطعی‌شده' }}
                </span>
            </div>
            <div class="col-span-2">
                <p class="text-sm text-gray-600 dark:text-gray-400">شرح</p>
                <p class="text-lg font-bold dark:text-white">
                    {{ $entry->description ?? '-' }}
                </p>
            </div>
        </div>

        <!-- Status Action -->
        <div class="border-t border-gray-200 dark:border-neutral-700 pt-4">
            @if ($entry->status === 'draft')
                <button wire:click="changeStatus('posted')"
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold"
                    onclick="return confirm('آیا مطمئن هستید که می‌خواهید این سند را قطعی‌کنید؟')">
                    قطعی‌کردن سند
                </button>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    <div
        class="bg-white dark:bg-neutral-900 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">ردیف‌های حسابداری</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
                    <tr>
                        <th class="px-6 py-3 text-right font-bold text-gray-800 dark:text-white">حساب</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-800 dark:text-white">مشتری</th>
                        <th class="px-6 py-3 text-center font-bold text-gray-800 dark:text-white">بدهکار</th>
                        <th class="px-6 py-3 text-center font-bold text-gray-800 dark:text-white">بستانکار</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @foreach ($entry->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition">
                            <td class="px-6 py-3 text-right font-bold dark:text-white">
                                <span class="text-gray-600 dark:text-gray-400">{{ $item->account->code }}</span>
                                <br>
                                {{ $item->account->name }}
                            </td>
                            <td class="px-6 py-3 text-right dark:text-white">
                                @if ($item->customer)
                                    {{ $item->customer->name ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center font-bold dark:text-white">
                                {{ $item->debit ? number_format($item->debit) : '-' }}
                            </td>
                            <td class="px-6 py-3 text-center font-bold dark:text-white">
                                {{ $item->credit ? number_format($item->credit) : '-' }}
                            </td>
                        </tr>
                    @endforeach

                    <!-- Totals Row -->
                    <tr class="bg-gray-100 dark:bg-neutral-800 font-bold">
                        <td colspan="2" class="px-6 py-3 text-right dark:text-white">جمع کل</td>
                        <td class="px-6 py-3 text-center dark:text-white">
                            {{ number_format($entry->getTotalDebit()) }}
                        </td>
                        <td class="px-6 py-3 text-center dark:text-white">
                            {{ number_format($entry->getTotalCredit()) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="p-6 bg-gray-50 dark:bg-neutral-800 border-t border-gray-200 dark:border-neutral-700">
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">کل بدهکار</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">
                        {{ number_format($entry->getTotalDebit()) }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">کل بستانکار</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">
                        {{ number_format($entry->getTotalCredit()) }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">تراز</p>
                    <p class="text-xl font-bold {{ $entry->isBalanced() ? 'text-green-600' : 'text-red-600' }}">
                        {{ $entry->isBalanced() ? '✓ متعادل' : '✗ نامتعادل' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
