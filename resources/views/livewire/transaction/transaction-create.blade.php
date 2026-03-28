<div class="border-2 mx-2 rounded-2xl" x-data="{
    formatted: '',
    realValue: '',
    formatNumber(value) {
        let raw = value.replace(/,/g, '');
        if (isNaN(raw)) raw = '0';
        this.formatted = Number(raw).toLocaleString();
        this.realValue = raw;
        $wire.amount = raw;
    }
}">
    <script src="https://cdn.jsdelivr.net/gh/mahmoud-eskandari/NumToPersian/dist/num2persian.min.js"></script>
    <form wire:submit="createTransaction">
        <div class="p-4">
            <div class="grid grid-cols-2">
                <div class="my-2 mx-4">
                    <label for="شئخعدف"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">مبلغ</label>
                    <div class="mt-2">
                        <input x-model="formatted" @input="formatNumber($event.target.value)" type="text"
                            name="city" id="city" autocomplete="address-level2"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="به تومان وارد کنید" />
                    </div>
                    <p x-text="num2persian(formatted) + ' تومان'" class="mt-1 text-green-800 font-bold text-sm"></p>
                    @error('amount')
                        <spxan class="text-red-500 text-sm font-bold mt-2">{{ $message }}</spxan>
                    @enderror
                </div>
                <div class="my-2 mx-4">
                    <label for="type"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">نوع</label>
                    <select id="category" wire:model="type"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
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
                <textarea wire:model="description" rows="4"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="توضیحات تراکنش خود را وارد کنید..."></textarea>
                @error('description')
                    <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="grid grid-cols-2">
                <div class="my-2 mx-4">
                    <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">دسته
                        بندی</label>
                    <select id="category_id" wire:model="category_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option selected></option>
                        @foreach ($categories as $categorie)
                            <option value="{{ $categorie->id }}">{{ $categorie->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="my-2 mx-4">
                    <label for="account_id"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">حساب</label>
                    <select id="account_id" wire:model="account_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option selected></option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                    @error('account_id')
                        <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="grid grid-cols-2">
                <div class="my-2 mx-4">
                    <label class="block text-sm mb-2 dark:text-white font-bold" for="datepicker">تاریخ:</label>
                    <input type="text" data-jdp class=" border-2 rounded p-2" placeholder="تاریخ شمسی"
                        wire:model.defer="transaction_date_jalali" />
                    @error('transaction_date_jalali')
                        <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="my-2 mx-4">
                    <label for="tracking_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">کد
                        رهگیری</label>
                    <input type="text" wire:model="tracking_code"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="کد رهگیری را وارد کنید" required="">
                    @error('tracking_code')
                        <div class="text-sm text-red-500">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="flex justify-center my-6">
                <button type="submit"
                    class="py-3 px-5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-green-500 text-slate-900 hover:bg-green-600 focus:outline-hidden cursor-pointer disabled:opacity-50 disabled:pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    ثبت تراکنش
                </button>
            </div>
        </div>
    </form>



    @push('scripts')
        <script type="text/javascript" src="/assets/js/jalalidatepicker.min.js"></script>

        <script>
            jalaliDatepicker.startWatch();
        </script>
    @endpush
</div>
