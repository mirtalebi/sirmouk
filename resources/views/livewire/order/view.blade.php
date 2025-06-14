<div>
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

                {{-- <div class="flex px-3 mt-5">
                    <h3 class="text-xl grow font-bold">پرداختی ها </h3>
                    <button type="button" wire:click="addPayment"
                        class="text-white bg-green-700 hover:bg-green-800 rounded-lg text-sm px-5 py-2.5 text-center">
                        افزودن</button>
                </div>
                <div class="p-4">
                    <table class="w-full text-right text-sm text-neutral-600 dark:text-neutral-300">
                        <thead
                            class="border-b border-neutral-300 bg-neutral-50 text-sm text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                            <tr>
                                <th scope="col" class="p-4">نوع</th>
                                <th scope="col" class="p-4">تاریخ</th>
                                <th scope="col" class="p-4">قیمت</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-300 dark:divide-neutral-700">
                            @php $sumPrice = 0 @endphp
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="">
                                        <select id="countries"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                            @foreach (\App\Models\PaymentType::all() as $paymentType)
                                                <option>{{ $paymentType->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-4">
                                        {{ $payment->date }}</td>
                                    <td class="p-4">
                                        <input type="number" id="mobile"
                                            class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                                            placeholder="مبلغ به تومان" required />
                                    </td>
                                    @php $sumPrice += $payment->price @endphp
                                </tr>
                            @endforeach

                            <tr>
                                <td class="p-4 font-bold">جمع پرداختی</td>
                                <td class="p-4"></td>
                                <td class="p-4 font-bold text-black text-lg">
                                    {{ number_format($sumPrice) }} <span class="text-xs">تومان</span></td>
                            </tr>

                        </tbody>
                    </table>
                </div> --}}

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
            <div class="flex justify-center mt-5">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
