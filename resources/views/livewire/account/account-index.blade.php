<div x-data="{ expandedAccounts: @json($expandedAccounts) }" class="max-w-7xl mx-auto py-10 px-4">
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
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">لیست حساب‌ها</h1>
        <div class="flex gap-3">
            <a href="{{ route('accounts.create') }}"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                ساخت حساب جدید
            </a>
            <livewire:account.create-detailed-account />
        </div>
    </div>

    <!-- Table -->
    <div
        class="bg-white dark:bg-neutral-900 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
                    <tr>
                        <th class="px-6 py-4 text-right font-bold text-gray-800 dark:text-white">کد</th>
                        <th class="px-6 py-4 text-right font-bold text-gray-800 dark:text-white">نام حساب</th>
                        <th class="px-6 py-4 text-right font-bold text-gray-800 dark:text-white">نوع</th>
                        <th class="px-6 py-4 text-center font-bold text-gray-800 dark:text-white">موجودی</th>
                        <th class="px-6 py-4 text-center font-bold text-gray-800 dark:text-white">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @forelse ($rootAccounts as $account)
                        @include('livewire.account.account-tree-row', [
                            'account' => $account,
                            'level' => 0,
                        ])
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                هیچ حسابی ثبت نشده است
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-800 border-t border-gray-200 dark:border-neutral-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                تعداد حساب‌های اصلی: <span class="font-bold">{{ count($rootAccounts) }}</span>
            </p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex gap-4">
        <a href="{{ route('accounts.create') }}"
            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition">
            ساخت حساب جدید
        </a>
        <a href="{{ route('journal-entries.create') }}"
            class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold transition">
            ثبت سند حسابداری
        </a>
    </div>
</div>
