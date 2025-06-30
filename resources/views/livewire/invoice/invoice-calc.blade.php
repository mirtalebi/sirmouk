<div>
    <form wire:submit="submit" >
        <div class="flex">
            <div class="my-2 mx-4">
                <label class="block text-sm mb-2 dark:text-white font-bold" for="datepicker">از تاریخ:</label>
                <input
                    type="text"
                    data-jdp
                    class=" border-2 rounded p-2"
                    placeholder="تاریخ شمسی"
                    wire:model.defer="from_date" />
                {{--            @error('transaction_date_jalali')--}}
                {{--            <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>--}}
                {{--            @enderror--}}
            </div>
            <div class="my-2 mx-4">
                <label class="block text-sm mb-2 dark:text-white font-bold" for="datepicker">تا تاریخ:</label>
                <input
                    type="text"
                    data-jdp
                    class=" border-2 rounded p-2"
                    placeholder="تا تاریخ"
                    wire:model.defer="to_date" />
                {{--            @error('transaction_date_jalali')--}}
                {{--            <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>--}}
                {{--            @enderror--}}
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
                        <td class="p-4">{{ $invoice->user->name }}</td>
                        <td class="p-4">{{ $invoice->user->mobile }}</td>
                        <td class="p-4">{{ $invoice->getCreatedAtDate() }}</td>
                        <td class="p-4 font-bold text-black">
                            {{ number_format($invoice->calcFinalPrice()) }} تومان
                        </td>
                        <td>
                            <a href="{{ route('invoice.view', ['invoiceId' => $invoice->id, 'secretKey' => $invoice->url_secret]) }}"
                               class="text-blue-600 hover:underline mx-2">مشاهده</a>
                            <button type="button" wire:click="editInvoice({{ $invoice->id }})"
                                    class="text-yellow-700 hover:underline ml-2 mx-2">ویرایش</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
{{--            <div class="flex justify-center mt-5">--}}
{{--                {{ $invoices->links() }}--}}
{{--            </div>--}}
        </div>
    </div>


    @push('scripts')
        <script type="text/javascript" src="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js"></script>


        <script>
            jalaliDatepicker.startWatch();
        </script>
    @endpush
</div>
