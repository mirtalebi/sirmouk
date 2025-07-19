<div x-data="{modalIsOpen: false}">
    <button x-on:click="modalIsOpen = true" type="button" class="whitespace-nowrap rounded-sm bg-black border border-black dark:border-white px-4 py-2 text-center text-sm font-medium tracking-wide text-neutral-100 transition hover:opacity-75 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black active:opacity-100 active:outline-offset-0 dark:bg-white dark:text-black dark:focus-visible:outline-white">پرداخت</button>
    <div x-cloak x-show="modalIsOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="modalIsOpen" x-on:keydown.esc.window="modalIsOpen = false" x-on:click.self="modalIsOpen = false" class="fixed inset-0 z-30 flex items-end justify-center bg-black/20 p-4 pb-8 backdrop-blur-md sm:items-center lg:p-8" role="dialog" aria-modal="true" aria-labelledby="defaultModalTitle">
        <!-- Modal Dialog -->
        <div class="bg-slate-100 w-10/12 p-4 rounded-xl">
            <div class="flex px-3 mt-5">
                <h3 class="text-xl grow font-bold">پرداختی ها </h3>
                <button type="button" wire:click="addPayment"
                        class="text-white bg-green-700 hover:bg-green-800 rounded-lg text-sm px-5 py-2.5 text-center {{ $invoice_price <= 0 ? 'hidden' : ''  }}">
                    افزودن</button>
                <span>
                </span>
            </div>
            <div class="p-4">
                <table class="w-full text-right text-sm text-neutral-600 dark:text-neutral-300">
                    <thead
                        class="border-b border-neutral-300 bg-neutral-50 text-sm text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                    <tr>
                        <th scope="col" class="p-4">حساب</th>
                        <th scope="col" class="p-4">تاریخ</th>
                        <th scope="col" class="p-4">مبلغ</th>
                        <th scope="col" class="p-4">عملیات</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-300 dark:divide-neutral-700">
                    @php $sumPrice = 0 @endphp
                    @foreach ($payments as $payment)
                        <tr>
                            <td class="">
                                <select id="countries"
                                        wire:model.defer="account"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option selected></option>
                                    @foreach (\App\Models\Account::all() as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-4">
                                <input
                                    type="text"
                                    data-jdp
                                    class=" border-2 rounded p-2"
                                    placeholder="تاریخ شمسی"
                                    wire:model.defer="j_date" />
                            </td>
                            <td class="p-4">
                                <input type="number" id="mobile"
                                       wire:model.defer="amount"
                                       class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                                       placeholder="مبلغ به تومان" required />
                            </td>
                            <td class="p-4">
                                <div class="flex gap-4">
                                    <div class="inline-flex">
                                        <button type="button" wire:click="savePayment"
                                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                            ثبت
                                        </button>
                                    </div>
                                </div>

                            </td>
                            @php $sumPrice += $payment->price @endphp
                        </tr>
                        @if(session()->has('error'))
                            <div class="bg-red-200 text-red-800 p-2 rounded">
                                {{ session('error') }}
                            </div>
                        @endif

                    @endforeach
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td class="">
                                <select id="countries"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" disabled>
                                        <option>{{ $transaction->account->name }}</option>
                                </select>
                            </td>
                            <td class="p-4">
                                <input
                                    value="{{ \App\Common\Jalalian::fromDateTime($transaction->transaction_date)->format('%d %B %Y') }}"
                                    class=" border-2 rounded p-2"
                                disabled>
                            </td>
                            <td class="p-4">
                                <input
                                       class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                                       placeholder="مبلغ به تومان" value="{{ number_format($transaction->amount) }}" required disabled />
                            </td>
                            <td class="p-4">
                                <div class="flex gap-4">
                                    <div class="inline-flex">
                                        <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-orange-400 text-slate-900 hover:bg-orange-500 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" href="#">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>
                                            ویرایش
                                        </a>
                                    </div>
                                    <div class="inline-flex">
                                        <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-slate-900 hover:bg-red-900 focus:outline-hidden focus:bg-red-900 disabled:opacity-50 disabled:pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            حذف
                                        </button>
                                    </div>
                                </div>


                            </td>
{{--                            @php $sumPrice += $payment->price @endphp--}}
                        </tr>
                        @php $sumPrice += $transaction->amount @endphp
                    @endforeach

                    <tr>
                        <td class="p-4 font-bold">جمع پرداختی</td>
                        <td class="p-4"></td>
                        <td class="p-4 font-bold text-black text-lg">
                            <span class="text-xs">{{ number_format($paid_amount) }} تومان</span></td>
                    </tr>
                    <tr>
                        <td class="p-4 font-bold">مبلغ مانده</td>
                        <td class="p-4"></td>
                        <td class="p-4 font-bold text-black text-lg">
                            <span class="text-xs">{{ number_format($invoice_price) }}</span></td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>


    @push('scripts')
        <script type="text/javascript" src="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js"></script>

        <script>
            jalaliDatepicker.startWatch();
        </script>
    @endpush
</div>
