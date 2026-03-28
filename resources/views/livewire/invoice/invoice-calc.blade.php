<div>
    <form wire:submit="submit">
        <div class="flex">
            <div>
                <div class="my-2 mx-4">
                    <label class="block text-sm mb-2 dark:text-white font-bold" for="datepicker">از تاریخ:</label>
                    <input type="text" data-jdp class=" border-2 rounded p-2" placeholder="تاریخ شمسی"
                        wire:model.defer="from_date" />
                    @error('from_date')
                        <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="my-2 mx-4">
                    <label class="block text-sm mb-2 dark:text-white font-bold" for="datepicker">تا تاریخ:</label>
                    <input type="text" data-jdp class=" border-2 rounded p-2" placeholder="تا تاریخ"
                        wire:model.defer="to_date" />
                    @error('to_date')
                        <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="my-2 mx-4 w-full">
                <h3 class="mb-4 font-semibold text-gray-900 dark:text-white">محصولات:</h3>
                <div
                    class="items-center w-full grid grid-cols-6 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach ($products as $product)
                        <div class="w-full border-b border-r col-span-1 border-gray-200 dark:border-gray-600">
                            <div class="flex items-center ps-3">
                                <input type="checkbox" wire:model="selectedProducts.{{ $product->id }}"
                                    value="{{ $product->id }}"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm">
                                <label for="vue-checkbox-list"
                                    class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $product->name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
        <div class="my-2 mx-4">
            <button type="submit" class="py-2 px-3 rounded bg-green-500">Submit</button>
        </div>
        <div>مجموع فاکتور <span>{{ number_format($invoice_calc) }}</span></div>
    </form>


    <div class="relative flex-1 overflow-hidden rounded-xl border border-neutral-200 p-3">
        <h3 class="text-xl font-bold">فاکتور های فعلی</h3>
        <div class="overflow-x-auto mt-2">
            <table class="w-full text-right text-sm text-neutral-600">
                <thead class="border-b border-neutral-300 bg-neutral-50 text-sm text-neutral-900 ">
                    <tr>
                        <th scope="col" class="p-4">شماره فاکتور</th>
                        <th scope="col" class="p-4">نام مشتری</th>
                        <th scope="col" class="p-4">شماره تلفن</th>
                        <th scope="col" class="p-4">تاریخ ثبت</th>
                        <th scope="col" class="p-4">مبلغ کل</th>
                        <th scope="col" class="p-4">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-300 dark:divide-neutral-700">
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td class="p-4">{{ $invoice->id }}</td>
                            <td class="p-4">
                                {{ $invoice->user->name ?? (json_decode($invoice->snap_user_credentials, true)['username'] ?? '') }}
                            </td>
                            @if ($invoice->is_snap)
                                @if (empty(json_decode($invoice->snap_user_credentials, true)['mobile']))
                                    <td class="py-4 text-red-400 flex items-center justify-center font-bold">
                                        شماره ای ثبت نشده
                                    </td>
                                @else
                                    <td class="p-4">
                                        {{ json_decode($invoice->snap_user_credentials, true)['mobile'] }}</td>
                                @endif
                            @else
                                @if (empty($invoice->user->mobile))
                                    <td class="py-4 text-red-400 flex items-center justify-center font-bold">
                                        شماره ای ثبت نشده
                                    </td>
                                @else
                                    <td class="p-4">{{ $invoice->user->mobile }}</td>
                                @endif
                            @endif
                            <td class="p-4">{{ $invoice->getCreatedAtDate() }}</td>
                            <td class="p-4 font-bold text-black">
                                {{ number_format($invoice->calcFinalPrice()) }} تومان
                            </td>
                            <td>
                                <a href="{{ route('invoice.view', ['invoiceId' => $invoice->id, 'secretKey' => $invoice->url_secret]) }}"
                                    class="text-blue-600 hover:underline mx-2">مشاهده</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{--            <div class="flex justify-center mt-5"> --}}
            {{--                {{ $invoices->links() }} --}}
            {{--            </div> --}}
        </div>
    </div>


    @push('scripts')
        <script type="text/javascript" src="/assets/js/jalalidatepicker.min.js"></script>


        <script>
            jalaliDatepicker.startWatch();
        </script>
    @endpush
</div>
