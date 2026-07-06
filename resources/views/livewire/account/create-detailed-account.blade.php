<div>
    <!-- Button to Open Modal -->
    <button wire:click="openModal"
        class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        ثبت حساب تفصیلی جدید
    </button>

    <!-- Modal -->
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div
                class="bg-white dark:bg-neutral-900 rounded-xl shadow-2xl border border-gray-200 dark:border-neutral-700 w-full max-w-md p-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">ثبت حساب تفصیلی جدید</h2>

                <form wire:submit="save">
                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-bold mb-2 dark:text-white">
                            نام حساب <span class="text-red-600">*</span>
                        </label>
                        <input type="text" wire:model="name" id="name" placeholder="مثال: علی احمدی"
                            class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white focus:border-blue-500">
                        @error('name')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Parent Account -->
                    <div class="mb-4">
                        <label for="parentAccountId" class="block text-sm font-bold mb-2 dark:text-white">
                            حساب مادر <span class="text-red-600">*</span>
                        </label>
                        <select wire:model="parentAccountId" id="parentAccountId"
                            class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white focus:border-blue-500">
                            <option value="">انتخاب حساب مادر</option>
                            @foreach ($generalAccounts as $account)
                                <option value="{{ $account['id'] }}">
                                    {{ $account['code'] }} - {{ $account['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('parentAccountId')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Accountable Type (Optional) -->
                    <div class="mb-4">
                        <label for="accountableType" class="block text-sm font-bold mb-2 dark:text-white">
                            نوع متصل شونده (اختیاری)
                        </label>
                        <select wire:model="accountableType" id="accountableType"
                            class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white focus:border-blue-500">
                            <option value="">هیچ‌کدام</option>
                            <option value="App\Models\User">مشتری</option>
                        </select>
                    </div>

                    <!-- Accountable ID (Optional) -->
                    @if ($accountableType)
                        <div class="mb-4">
                            <label for="accountableId" class="block text-sm font-bold mb-2 dark:text-white">
                                شناسه مشتری (اختیاری)
                            </label>
                            <input type="number" wire:model="accountableId" id="accountableId"
                                placeholder="شناسه مشتری"
                                class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white focus:border-blue-500">
                            @error('accountableId')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold">
                            ثبت
                        </button>
                        <button type="button" wire:click="closeModal"
                            class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold">
                            انصراف
                        </button>
                    </div>
                </form>

                <!-- Errors -->
                @if ($errors->any())
                    <div
                        class="mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-3">
                        <p class="text-red-800 dark:text-red-400 font-bold text-xs mb-2">خطاهای اعتبارسنجی:</p>
                        <ul class="list-disc list-inside space-y-1 text-red-700 dark:text-red-300 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
