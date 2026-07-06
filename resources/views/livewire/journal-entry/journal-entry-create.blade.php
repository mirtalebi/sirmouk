<div class="max-w-7xl mx-auto py-10 px-4">

    <!-- Success/Error Messages -->
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
    <div
        class="bg-white dark:bg-neutral-900 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">ثبت سند حسابداری</h1>

        <form wire:submit="save">
            <!-- Master Fields -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Entry Date -->
                <div>
                    <label for="entryDate" class="block text-sm font-bold mb-2 dark:text-white">
                        تاریخ سند <span class="text-red-600">*</span>
                    </label>
                    <input type="date" wire:model="entryDate" id="entryDate"
                        class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    @error('entryDate')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-bold mb-2 dark:text-white">
                        شرح سند
                    </label>
                    <input type="text" wire:model="description" id="description" placeholder="مثال: فروش تجمیعی"
                        class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    @error('description')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-bold mb-2 dark:text-white">
                        وضعیت سند <span class="text-red-600">*</span>
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" wire:model="status" value="draft"
                                class="form-radio text-blue-600 w-4 h-4">
                            <span class="ml-2 dark:text-white">پیش‌نویس</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" wire:model="status" value="posted"
                                class="form-radio text-blue-600 w-4 h-4">
                            <span class="ml-2 dark:text-white">قطعی‌شده</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Items Table -->
            <div
                class="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-neutral-700 border-b border-gray-200 dark:border-neutral-600">
                            <tr>
                                <th class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">حساب</th>
                                <th class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">مشتری (اختیاری)
                                </th>
                                <th class="px-4 py-3 text-center font-bold text-gray-800 dark:text-white">بدهکار</th>
                                <th class="px-4 py-3 text-center font-bold text-gray-800 dark:text-white">بستانکار</th>
                                <th class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">شرح</th>
                                <th class="px-4 py-3 text-center font-bold text-gray-800 dark:text-white">عمل</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-600">
                            @forelse ($items as $index => $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition">
                                    <!-- Account Select -->
                                    <td class="px-4 py-3">
                                        <select wire:model.live="items.{{ $index }}.account_id"
                                            class="w-full px-2 py-1 rounded border border-gray-300 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white text-sm">
                                            <option value="">انتخاب حساب</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account['id'] }}">
                                                    {{ $account['code'] }} - {{ $account['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <!-- Customer Select (Conditional) -->
                                    <td class="px-4 py-3">
                                        @if ($item['account_id'] && $this->accountRequiresCustomer($item['account_id']))
                                            <select wire:model="items.{{ $index }}.customer_id"
                                                class="w-full px-2 py-1 rounded border border-gray-300 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white text-sm">
                                                <option value="">انتخاب مشتری</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer['id'] }}">
                                                        {{ $customer['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 text-xs">-</span>
                                        @endif
                                    </td>

                                    <!-- Debit Input -->
                                    <td class="px-4 py-3">
                                        <input type="number" wire:model.live="items.{{ $index }}.debit"
                                            min="0"
                                            class="w-full px-2 py-1 rounded border border-gray-300 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white text-sm text-center">
                                    </td>

                                    <!-- Credit Input -->
                                    <td class="px-4 py-3">
                                        <input type="number" wire:model.live="items.{{ $index }}.credit"
                                            min="0"
                                            class="w-full px-2 py-1 rounded border border-gray-300 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white text-sm text-center">
                                    </td>

                                    <!-- Description -->
                                    <td class="px-4 py-3">
                                        <input type="text" wire:model="items.{{ $index }}.description"
                                            placeholder="شرح سطر"
                                            class="w-full px-2 py-1 rounded border border-gray-300 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white text-sm">
                                    </td>

                                    <!-- Delete Button -->
                                    <td class="px-4 py-3 text-center">
                                        @if (count($items) > 2)
                                            <button type="button" wire:click="removeItem({{ $index }})"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-bold">
                                                حذف
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                        هیچ سطری اضافه نشده است
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Add Row Button -->
                <div class="px-4 py-3 bg-gray-50 dark:bg-neutral-700 border-t border-gray-200 dark:border-neutral-600">
                    <button type="button" wire:click="addItem"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-bold">
                        + افزودن سطر جدید
                    </button>
                </div>
            </div>

            <!-- Totals Row -->
            <div
                class="mt-6 bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">جمع بدهکار</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ number_format($totalDebit) }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">جمع بستانکار</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ number_format($totalCredit) }}
                        </p>
                    </div>
                    <div class="text-center md:col-span-2">
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">وضعیت تراز</p>
                        @if ($isBalanced)
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">✓ متوازن است</p>
                        @else
                            <p class="text-xl font-bold text-red-600 dark:text-red-400">⚠ متوازن نیست</p>
                            @if ($totalDebit !== $totalCredit && $totalDebit > 0)
                                <p class="text-sm text-red-500 mt-1">
                                    اختلاف: {{ number_format(abs($totalDebit - $totalCredit)) }}
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div
                    class="mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                    <p class="text-red-800 dark:text-red-400 font-bold mb-2">خطاهای اعتبارسنجی:</p>
                    <ul class="list-disc list-inside space-y-1 text-red-700 dark:text-red-300 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Buttons -->
            <div class="mt-6 flex gap-4">
                <button type="submit" {{ !$isBalanced ? 'disabled' : '' }}
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg font-bold transition">
                    ثبت سند
                </button>
                <a href="{{ route('accounts.index') }}"
                    class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold transition">
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>
