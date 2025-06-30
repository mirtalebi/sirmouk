<div>
    <div class="mt-7 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-neutral-900 dark:border-neutral-700">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">ساخت حساب</h1>
            </div>

            <div class="mt-5">
                <!-- Form -->
                <form wire:submit="save">
                    <div class="grid gap-y-4">
                        <!-- Form Group -->
                        <div class="grid grid-cols-4 gap-24 items-end">
                            <div>
                                <label for="name" class="block text-sm mb-2 dark:text-white font-bold">نام حساب</label>
                                <div class="relative">
                                    <input type="text" wire:model="name" class="py-2.5 sm:py-3 px-4 block w-full rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600 border-2 border-slate-700" required>
                                </div>
                                @error('name')
                                <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                                @enderror

                            </div>

                            <div>
                                <label for="balance" class="block text-sm mb-2 dark:text-white font-bold">موجودی حساب</label>
                                <div class="relative">
                                    <input type="number" wire:model="balance" name="balance" class="py-2.5 sm:py-3 px-4 block w-full rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600 border-2 border-slate-700" required>
                                </div>
                                @error('balance')
                                <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="type" class="block text-sm mb-2 dark:text-white font-bold">نوع</label>
                                <select wire:model="type" class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                                    <option value="debtor" selected>بدهکار</option>
                                    <option value="creditor">بستانکار</option>
                                </select>
                                @error('type')
                                <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="">
                                <button type="submit" class="py-3 px-4 items-center text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">ساخت حساب</button>
                            </div>

                        </div>
                        <!-- End Form Group -->
                    </div>
                </form>
                <!-- End Form -->
            </div>
        </div>
    </div></div>
