<div>

    <script src="https://cdn.jsdelivr.net/gh/mahmoud-eskandari/NumToPersian/dist/num2persian.min.js"></script>
    <div class="relative flex-1 overflow-hidden rounded-xl border border-neutral-200 p-3 mt-5">
        <h3 class="text-xl font-bold">
            @if ($invoice)
                ویرایش فاکتور #{{ $invoice->id }}
            @else
                ثبت فاکتور جدید
            @endif
        </h3>

        <div class="grid grid-cols-2 gap-5">

            <form class="">

                <div class="p-4">
                    <table class="w-full text-right text-sm text-neutral-600">
                        <thead class="border-b border-neutral-300 bg-neutral-50 text-sm text-neutral-900 ">
                            <tr>
                                <th scope="col" class="p-4">سرویس</th>
                                <th scope="col" class="p-1">تعداد</th>
                                <th scope="col" class="p-1">قیمت واحد</th>
                                <th scope="col" class="p-4">قیمت کل</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-300 dark:divide-neutral-700">
                            @php $sumPrice = 0 @endphp
                            @foreach ($tempOrder['card'] as $productId => $productCount)
                                @php $product = \App\Models\Product::find($productId) @endphp
                                <tr>
                                    <td class="p-4">
                                        <div class="flex w-max items-center gap-2 text-start">
                                            <img class="size-10 rounded-full object-cover"
                                                src="https://www.pngmart.com/files/23/Food-Icon-PNG-Pic.png"
                                                alt="{{ $product->name }}" />
                                            <div class="flex flex-col">
                                                <span class="text-neutral-900">
                                                    {{ $product->name }}</span>
                                                <span
                                                    class="text-sm text-neutral-600 opacity-85">{{ $product->description }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-1">{{ $productCount }}</td>
                                    <td class="p-1">{{ number_format($product->price) }}</td>
                                    <td class="p-4 font-bold text-black">
                                        {{ number_format($product->price * $productCount) }}</td>
                                    @php $sumPrice += $product->price * $productCount @endphp
                                </tr>
                            @endforeach

                            <tr>
                                <td class="p-4">
                                </td>
                                <td class="p-1"></td>
                                <td class="p-1">مالیات</td>
                                <td class="p-4 font-bold text-black">
                                    {{ number_format($sumPrice / 10) }}</td>
                                @php $sumPrice += $sumPrice / 10 @endphp
                            </tr>

                            <tr>
                                <td class="p-4">
                                </td>
                                <td class="p-1"></td>
                                <td class="p-1 font-bold">مبلغ کل</td>
                                <td class="p-4 font-bold text-black text-lg">
                                    {{ number_format($sumPrice) }} <span class="text-xs">تومان</span></td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="flex items-center ps-4 border border-gray-200 rounded-sm dark:border-gray-700">
                    <input wire:model="snap" type="checkbox" value="" name="bordered-checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">اسنپ</label>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="mb-5">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">نام مشتری</label>
                        <input type="text" id="name" wire:model="customerName"
                            class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="نام و نام خانوادگی ..." required />
                    </div>

                    <div class="mb-5">
                        <label for="mobile" class="block mb-2 text-sm font-medium text-gray-900">شماره
                            تلفن</label>
                        <input type="text" id="mobile" wire:model="customerMobile"
                            class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="شماره تلفن با صفر" required />
                    </div>

                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="mb-5">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">هزینه پیک</label>
                        <input type="text" id="name" wire:model="courierPrice"
                            class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="به تومان" required />
                    </div>

                    <div class="mb-5">
                        <label for="mobile" class="block mb-2 text-sm font-medium text-gray-900">تخفیف</label>
                        <input type="text" id="mobile" wire:model="discountPrice"
                            class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="به تومان" required />
                    </div>
                </div>


                <button type="button" wire:click="saveInvoice"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    @if ($invoice)
                        ویرایش فاکتور #{{ $invoice->id }}
                    @else
                        ثبت فاکتور جدید
                    @endif
                </button>
                @if ($invoice)
                    <button type="button" wire:click="cancelEditingInvoice"
                        class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mt-2">
                        لغو ویرایش فاکتور
                    </button>
                @endif
            </form>

            <form class="grid grid-cols-3 gap-2">

                @foreach ($products as $product)
                    <div>
                        <label for="bedrooms-input" class="sr-only">{{ $product->name }}</label>
                        <div class="relative flex items-center mb-2">
                            <button type="button" wire:click="removeBasket({{ $product->id }})"
                                class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M1 1h16" />
                                </svg>
                            </button>
                            <input type="text" id="bedrooms-input"
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 font-medium text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full"
                                placeholder="" value="{{ $product->name }}" required />

                            <button type="button" wire:click="addBasket({{ $product->id }})"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M9 1v16M1 9h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach


            </form>
        </div>
    </div>

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
                            <td class="p-4">{{ $invoice->user->name ?? json_decode($invoice->snap_user_credentials, true)['username'] ?? '' }}</td>
                            @if($invoice->is_snap)
                                @if(empty(json_decode($invoice->snap_user_credentials, true)['mobile']))
                                    <td class="py-4 text-red-400 flex items-center justify-center font-bold">
                                        شماره ای ثبت نشده
                                    </td>
                                @else
                                    <td class="p-4">{{ json_decode($invoice->snap_user_credentials, true)['mobile'] }}</td>
                                @endif
                            @else
                                @if(empty($invoice->user->mobile))
                                    <td class="py-4 text-red-400 flex items-center justify-center font-bold">
                                        شماره ای ثبت نشده
                                    </td>
                                @else
                                    <td class="p-4">{{ $invoice->user->mobile }}</td>
                                @endif
                            @endif
                            <td class="p-4">{{ $invoice->getCreatedAtDate() }}</td>
                            <td class="p-4 font-bold text-black">
                                {{ number_format($invoice->total_price) }} تومان
                            </td>
                            <td>
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('invoice.view', ['invoiceId' => $invoice->id, 'secretKey' => $invoice->url_secret]) }}"
                                       class="text-blue-600 hover:underline mx-2">مشاهده</a>
                                    <button type="button" wire:click="editInvoice({{ $invoice->id }})"
                                            class="text-yellow-700 hover:underline ml-2 mx-2">ویرایش</button>
                                    @if($invoice->is_snap)
                                        <div class="p-4 text-green-700 flex items-center justify-center font-bold">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" class="size-6 ml-2"><path fill="currentColor" fill-rule="evenodd" d="M31.452 10.695a78 78 0 0 0-.2-3.78c-.229-2.894-2.526-5.01-5.358-5.172c-2.2-.125-5.45-.243-9.894-.243s-7.695.118-9.894.243C3.274 1.905.976 4.021.748 6.916C.613 8.626.5 10.964.5 14v11q0 .093.012.185q-.012.72-.012 1.451c0 3.528.211 6.33.444 8.343c.296 2.573 2.201 4.459 4.574 4.977a8 8 0 0 1 15.982.536a413 413 0 0 0 5 0a8 8 0 0 1 15.978-.593c2.4-.647 4.227-2.69 4.47-5.288c.247-2.634.507-6.47.547-11.195c.008-1.028-.2-2.094-.716-3.072c-1.02-1.934-3.483-6.03-7.297-8.546c-1.027-.677-2.191-.94-3.272-.975c-1.49-.05-3.092-.092-4.758-.128m-3.956 3.365C26.812 15.217 25.503 16 24 16c-2.21 0-4-1.691-4-3.778C20 14.31 18.21 16 16 16s-4-1.691-4-3.778C12 14.31 10.21 16 8 16c-1.503 0-2.812-.783-3.496-1.94q-.004.456-.004.94v7.002c0 .83.669 1.498 1.497 1.498h20.006c.828 0 1.497-.669 1.497-1.498V15q0-.484-.004-.94m14.733 7.157c-1.269-1.999-3.18-4.442-5.607-5.878a2.6 2.6 0 0 0-1.33-.339H34a2 2 0 0 0-2 2v5.83c0 .955.676 1.772 1.624 1.888c1.522.187 3.968.387 6.695.219c1.906-.118 2.933-2.107 1.91-3.72M19 40.5a5.5 5.5 0 1 1-11 0a5.5 5.5 0 0 1 11 0M34.5 46a5.5 5.5 0 1 0 0-11a5.5 5.5 0 0 0 0 11" clip-rule="evenodd"/></svg>
                                            اسنپ
                                        </div>
                                    @else
                                        @if($invoice->paid_amount == 0)
                                            <button type="button" wire:click="showPayment({{ $invoice }})"
                                                    class="whitespace-nowrap rounded-2xl bg-green-600 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500 active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-green-500 dark:border-green-500 dark:text-white dark:focus-visible:outline-green-500 flex items-center gap-2">
                                                پرداخت
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4">
                                                    <path fill="currentColor" d="M19 14V6c0-1.1-.9-2-2-2H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2m-2 0H3V6h14zm-7-7c-1.66 0-3 1.34-3 3s1.34 3 3 3s3-1.34 3-3s-1.34-3-3-3m13 0v11c0 1.1-.9 2-2 2H4v-2h17V7z"/>
                                                </svg>
                                            </button>
                                        @elseif($invoice->total_price > $invoice->paid_amount)
                                            <button type="button" wire:click="showPayment({{ $invoice }})"
                                                    class="whitespace-nowrap rounded-2xl bg-orange-600 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500 active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-green-500 dark:border-green-500 dark:text-white dark:focus-visible:outline-green-500 flex items-center gap-2">
                                                پرداخت
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4">
                                                    <path fill="currentColor" d="M12 20a8 8 0 1 0 0-16a8 8 0 0 0 0 16m0 2C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10m-1-6h2v2h-2zm0-10h2v8h-2z"/>
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" wire:click="showPayment({{ $invoice }})"
                                                    class="whitespace-nowrap rounded-2xl bg-green-800 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500 active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-green-500 dark:border-green-500 dark:text-white dark:focus-visible:outline-green-500 flex items-center gap-2">
                                                پرداخت شده
                                                <svg xmlns="http://www.w3.org/2000/svg"viewBox="0 0 24 24" class="size-4">
                                                    <path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8m4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4l8-8z"/>
                                                </svg>
                                            </button>
                                        @endif
                                    @endif

                                    </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex justify-center mt-5">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>

    @if($showModal)
        <div x-data="{
            modalIsOpen: @entangle('showModal'),
            realValue: @entangle('amount'),
            formatted: '',

            init() {
            this.formatted = this.realValue;

                this.$watch('realValue', value => {
                    this.formatted = value;
                });
            },

            formatNumber(value) {
                let raw = value.replace(/,/g, '');
                if (isNaN(raw)) return '';

                this.formatted = this.realValue;

                this.$watch('realValue', value => {
                    this.formatted = Number(value).toLocaleString();
                });

                this.realValue = raw;
                this.formatted = Number(raw).toLocaleString();
                $wire.amount = raw;
            }
        }">
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
                                            @foreach (\App\Models\Account::all() as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-4">
                                        <input
                                            type="text"
                                            data-jdp
                                            class=" border-2 rounded p-2"
                                            placeholder="تاریخ شمسی"
                                            wire:model="j_date" />
                                    </td>
                                    <td class="p-4">
                                        <input type="text" id="mobile"
                                               x-model="formatted"
                                               @input="formatNumber($event.target.value)"
                                               class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                                               placeholder="مبلغ به تومان"
                                        />
                                        <input type="text" id="mobile"
                                               x-model="realValue"
                                               class="hidden"
                                               placeholder="مبلغ به تومان"
                                        />
                                        <p x-text="num2persian(formatted) + ' تومان'" class="mt-1"></p>
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
                                @error('account')
                                <div class="bg-red-200 text-red-800 p-2 rounded">
                                    {{ $message }}
                                </div>
                                @enderror
                                @error('j_date')
                                <div class="bg-red-200 text-red-800 p-2 rounded">
                                    {{ $message }}
                                </div>
                                @enderror
                                @error('amount')
                                <div class="bg-red-200 text-red-800 p-2 rounded">
                                    {{ $message }}
                                </div>
                                @enderror

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
        </div>
    @endif

</div>
