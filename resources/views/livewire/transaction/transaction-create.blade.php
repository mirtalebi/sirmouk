<div class="border-2 mx-2 rounded-2xl">
    <form wire:submit="createTransaction">
        <div class="p-4">
            <div class="grid grid-cols-2">
                <div class="my-2 mx-4">
                    <label for="amount" class="block text-sm mb-2 dark:text-white font-bold">مبلغ</label>
                    <div class="relative">
                        <input wire:model="amount" type="number" class="py-2.5 sm:py-3 px-4 block w-full rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600 border-2 border-slate-700" required>
                    </div>
                            @error('amount')
                            <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                            @enderror
                </div>
                <div class="my-2 mx-4">
                    <label for="type" class="block text-sm mb-2 dark:text-white font-bold">نوع</label>
                    <select wire:model="type" class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                        <option selected></option>
                        <option value="debit" selected>برداشت</option>
                        <option value="credit">واریز</option>
                    </select>
                            @error('type')
                            <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                            @enderror
                </div>
            </div>
            <div class="my-2 mx-4">

                <label for="description" class="block text-sm mb-2 dark:text-white font-bold">توضیحات</label>
                <textarea wire:model="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="توضیحات تراکنش خود را وارد کنید..."></textarea>
                @error('description')
                <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="grid grid-cols-2">
                <div class="my-2 mx-4">
                    <label for="category_id" class="block text-sm mb-2 dark:text-white font-bold">دسته</label>
                    <select wire:model="category_id" class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                        <option selected></option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}">{{ $categorie->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="my-2 mx-4">
                    <label for="account_id" class="block text-sm mb-2 dark:text-white font-bold">حساب</label>
                    <select wire:model="account_id" class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                        <option selected></option>
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                    @error('account_id')
                    <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="my-2 mx-4">
                <label class="block text-sm mb-2 dark:text-white font-bold" for="datepicker">تاریخ:</label>
                <input
                    type="text"
                    data-jdp
                    class=" border-2 rounded p-2"
                    placeholder="تاریخ شمسی"
                    wire:model.defer="transaction_date_jalali" />
                @error('transaction_date_jalali')
                <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-center my-6">
                <button type="submit" class="py-3 px-5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-green-500 text-slate-900 hover:bg-green-700 focus:outline-hidden focus:bg-green-700 disabled:opacity-50 disabled:pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    ثبت تراکنش
                </button>
            </div>
        </div>
    </form>



    @push('scripts')
        <script type="text/javascript" src="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js"></script>

        <script>
            jalaliDatepicker.startWatch();
        </script>
    @endpush
</div>
