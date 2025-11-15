<div x-data='{ tempBasket: {}, 
    products: @json(\App\Models\Product::all()), 
    getProduct(id) { return this.products.find((item) => item.id == id) },
    addToBasket(id) { this.tempBasket[id] = this.tempBasket[id] ? this.tempBasket[id] + 1 : 1; },
    removeFromBasket(id) { 
        if (!this.tempBasket[id]) return;
        this.tempBasket[id] -= 1;
        if (this.tempBasket[id] == 0) delete this.tempBasket[id];
    },
    getSumTempBasket() { 
        sum = 0;
        for(const [key, count] of Object.entries(this.tempBasket)) {
            sum += count * this.getProduct(key).price
        }
        return sum;
    } }'
    @basket-updated.window="tempBasket = $event.detail.basket">

    <script src="https://cdn.jsdelivr.net/gh/mahmoud-eskandari/NumToPersian/dist/num2persian.min.js"></script>
    <div class="relative flex-1 overflow-hidden rounded-xl border border-neutral-200 p-3 mt-5">
        <h3 class="text-xl font-bold">
            @if ($invoice)
                ویرایش فاکتور #{{ $invoice->id }}
            @else
                ثبت فاکتور جدید
            @endif
        </h3>

        <div class="grid xl:grid-cols-2 gap-5">

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
                            <template x-for="(rowValue, rowKey) in tempBasket">
                                <tr>
                                    <td class="p-4">
                                        <div class="flex w-max items-center gap-2 text-start">
                                            <img class="size-10 rounded-full object-cover"
                                                src="https://www.pngmart.com/files/23/Food-Icon-PNG-Pic.png" />
                                            <div class="flex flex-col">
                                                <span class="text-neutral-900" x-text="getProduct(rowKey).name"></span>
                                                <span class="text-sm text-neutral-600 opacity-85">
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-1" x-text="rowValue"></td>
                                    <td class="p-1" x-text="getProduct(rowKey).price"></td>
                                    <td class="p-4 font-bold text-black" x-text="rowValue * getProduct(rowKey).price">
                                    </td>
                                </tr>
                            </template>
                            @php $sumPrice = 0 @endphp

                            <tr>
                                <td class="p-4">
                                </td>
                                <td class="p-1"></td>
                                <td class="p-1 font-bold">مبلغ کل</td>
                                <td class="p-4 font-bold text-black text-lg">
                                    <span x-text="getSumTempBasket()"></span> <span class="text-xs">تومان</span>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="mb-3">
                        <label for="mobile" class="block mb-2 text-sm font-medium text-gray-900">شماره
                            تلفن</label>
                        <input type="text" id="mobile" wire:model="customerMobile"
                            class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="شماره تلفن با صفر" required x-data
                            x-on:input="
                                   if ($el.value.length === 11) {
                                       $wire.call('findUser', $el.value)
                                   }
                               " />
                        @error('customerMobile')
                            <div class="text-sm text-red-500">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div wire:loading wire:target="findUser" class="flex justify-center items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
                            class="size-5 fill-on-surface motion-safe:animate-spin dark:fill-on-surface-dark">
                            <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                opacity=".25" />
                            <path
                                d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                        </svg>
                    </div>
                    <div class="mb-3" wire:loading.remove wire:target="findUser">
                        <div class="flex justify-between">
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">نام مشتری
                            </label>
                            @if (isset($user))
                                <button type="button" wire:click="updateUserModal"
                                    class="text-blue-400 text-sm cursor-pointer">مشاهده</button>
                            @endif
                        </div>
                        <input type="text" id="name" wire:model="customerName"
                            class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="نام و نام خانوادگی ..." required />
                        @error('customerName')
                            <div class="text-sm text-red-500">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="mb-3" x-data="{
                        realValue: @entangle('courierPrice'),
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
                            $wire.courierPrice = raw;
                        }
                    }">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">هزینه پیک</label>
                        <input type="text" id="name" x-model="formatted"
                            @input="formatNumber($event.target.value)"
                            class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="به تومان" required />
                        @error('courierPrice')
                            <div class="text-sm text-red-500">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3" x-data="{
                        realValue: @entangle('discountPrice'),
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
                            $wire.discountPrice = raw;
                        }
                    }">
                        <label for="mobile" class="block mb-2 text-sm font-medium text-gray-900">تخفیف</label>
                        <input type="text" id="mobile" x-model="formatted"
                            @input="formatNumber($event.target.value)"
                            class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="به تومان" required />
                        @error('discountPrice')
                            <div class="text-sm text-red-500">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div wire:loading.remove wire:target="findUser"
                        class="flex items-end col-span-2 gap-2 w-full justify-center">
                        @if (empty($add_address))
                            <div class="relative flex w-full flex-col gap-1 text-neutral-600 dark:text-neutral-300">
                                <label for="os" class="w-fit pl-0.5 text-sm">آدرس</label>
                                <div class="flex items-center gap-2">
                                    <button wire:show="address_id" x-transition.duration.100ms
                                        wire:transition.scale.origin.right wire:click="address_id = null"
                                        type="button">
                                        <svg wire:loading.remove wire:target="deleteAddress"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" class="size-6 text-red-500">
                                            <path fill="currentColor" fill-rule="evenodd"
                                                d="M7 1L1 7l5 5l-5 5l6 6l5-5l5 5l6-6l-5-5l5-5l-6-6l-5 5z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <select wire:model="address_id" id="os" name="os"
                                        class="w-full appearance-none rounded-sm border border-neutral-300 bg-neutral-50 px-4 py-2 text-sm">
                                        <option value="" selected></option>
                                        @forelse($addresses as $address)
                                            <option {{ $address->id == $address_id ? 'selected' : '' }}
                                                value="{{ $address->id }}">{{ $address->address }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                    <button wire:click="addAddressInput()" type="button"
                                        class="whitespace-nowrap rounded-full bg-green-600 border border-green-600 px-4 py-2 text-xs tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500 active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed">
                                        آدرس جدید
                                    </button>
                                </div>
                            </div>
                        @endif
                        @forelse($add_address as $address)
                            <div class="relative flex w-full gap-2 text-neutral-600 dark:text-neutral-300">
                                <input type="text" wire:model="address_label"
                                    class="w-full rounded-sm border border-neutral-300 bg-neutral-50 py-2 pl-10 pr-2 text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black disabled:cursor-not-allowed disabled:opacity-75 dark:border-neutral-700 dark:bg-neutral-900/50 dark:focus-visible:outline-white"
                                    name="search" placeholder="آدرس جدید را وارد کنید!" aria-label="search" />
                            </div>
                        @empty
                        @endforelse
                    </div>
                    <!-- Primary spinner -->
                    <div wire:loading wire:target="findUser" class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
                            class="size-5 fill-primary motion-safe:animate-spin dark:fill-primary-dark">
                            <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                opacity=".25" />
                            <path
                                d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                        </svg>
                    </div>
                </div>
                <div>

                </div>

                <div class="flex gap-2 items-end justify-center">
                    @if ($invoice)
                        <button type="button" wire:click="cancelEditingInvoice"
                            class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mt-2">
                            لغو ویرایش فاکتور
                        </button>
                    @endif
                    <button type="button" wire:click="saveInvoice(tempBasket)"
                        class="text-white mt-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        @if ($invoice)
                            ویرایش فاکتور #{{ $invoice->id }}
                        @else
                            ثبت فاکتور جدید
                        @endif
                    </button>
                    <div class="flex gap-2 items-center rounded-sm dark:border-gray-700 border p-2 text-green-700">
                        <input wire:model="snap" id="snap" type="checkbox" value=""
                            name="bordered-checkbox"
                            class="w-6 h-6 text-green-700 bg-green-700 border-gray-300 rounded-sm">
                        <label for="snap" class="font-bold">اسنپ</label>
                        <label for="snap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" class="size-8 text-green-700">
                                <path fill="currentColor" fill-rule="evenodd"
                                    d="M1 3a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v5h4a5 5 0 0 1 5 5v4a3 3 0 0 1-2.129 2.872a3 3 0 0 1-5.7.128H8.83a3 3 0 0 1-5.7-.128A3 3 0 0 1 1 17v-4h6a1 1 0 1 0 0-2H1V9h4a1 1 0 0 0 0-2H1zm13 15h1.171a3 3 0 0 1 5.536-.293A1 1 0 0 0 21 17v-4a3 3 0 0 0-3-3h-4zm-7 1a1 1 0 1 0-2 0a1 1 0 0 0 2 0m10.293-.707A1 1 0 0 0 17 19a1 1 0 1 0 .293-.707"
                                    clip-rule="evenodd" />
                            </svg>
                        </label>
                    </div>
                </div>
            </form>

            <form class="">
                <div x-data="{ selectedTab: '{{ $categories->first()->id }}' }" class="w-full">
                    <div x-on:keydown.right.prevent="$focus.wrap().next()"
                        x-on:keydown.left.prevent="$focus.wrap().previous()"
                        class="flex gap-2 overflow-x-auto border-b border-neutral-300 dark:border-neutral-700"
                        role="tablist" aria-label="tab options">
                        @forelse($categories as $category)
                            <button x-on:click="selectedTab = '{{ $category->id }}'"
                                x-bind:aria-selected="selectedTab === '{{ $category->id }}'"
                                x-bind:tabindex="selectedTab === '{{ $category->id }}' ? '0' : '-1'"
                                x-bind:class="selectedTab === '{{ $category->id }}' ?
                                    'font-bold text-black border-b-2 border-black dark:border-white dark:text-white' :
                                    'text-neutral-600 font-medium dark:text-neutral-300 dark:hover:border-b-neutral-300 dark:hover:text-white hover:border-b-2 hover:border-b-neutral-800 hover:text-neutral-900'"
                                class="h-min px-4 py-2 text-sm" type="button" role="tab"
                                aria-controls="tabpanelGroups">{{ $category->name }}</button>
                        @empty
                        @endforelse
                    </div>
                    <div class="px-2 py-4 text-neutral-600 dark:text-neutral-300">
                        @forelse($categories as $category)
                            <div x-cloak x-show="selectedTab === '{{ $category->id }}'"
                                class="grid grid-cols-2 xl:grid-cols-3 gap-2" id="tabpanelGroups" role="tabpanel"
                                aria-label="groups">
                                @foreach ($category->products as $product)
                                    <div>
                                        <label for="bedrooms-input" class="sr-only">{{ $product->name }}</label>
                                        <div class="relative flex items-center mb-2">
                                            <button type="button" @click="removeFromBasket('{{ $product->id }}')"
                                                class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>
                                            <input type="text" id="bedrooms-input"
                                                class="bg-gray-50 border-x-0 border-gray-300 h-11 font-medium text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full"
                                                placeholder="" value="{{ $product->name }}" required />

                                            <button type="button" @click="addToBasket('{{ $product->id }}')"
                                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        @empty
                        @endforelse
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="relative flex-1 overflow-hidden rounded-xl border border-neutral-200 p-3 mt-2">
        <div class="overflow-x-auto">
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
                                {{ number_format($invoice->total_price) }} تومان
                            </td>
                            <td>
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('invoice.view', ['invoiceId' => $invoice->id, 'secretKey' => $invoice->url_secret]) }}"
                                        class="text-blue-600 hover:underline mx-2">مشاهده</a>
                                    @if ($invoice->created_at->isToday())
                                        <button type="button" wire:click="editInvoice({{ $invoice->id }})"
                                            class="text-yellow-700 hover:underline ml-2 mx-2">ویرایش</button>
                                    @endif
                                    @if ($invoice->is_snap)
                                        <div class="p-4 text-green-700 flex items-center justify-center font-bold">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                                viewBox="0 0 48 48" class="size-6 ml-2">
                                                <path fill="currentColor" fill-rule="evenodd"
                                                    d="M31.452 10.695a78 78 0 0 0-.2-3.78c-.229-2.894-2.526-5.01-5.358-5.172c-2.2-.125-5.45-.243-9.894-.243s-7.695.118-9.894.243C3.274 1.905.976 4.021.748 6.916C.613 8.626.5 10.964.5 14v11q0 .093.012.185q-.012.72-.012 1.451c0 3.528.211 6.33.444 8.343c.296 2.573 2.201 4.459 4.574 4.977a8 8 0 0 1 15.982.536a413 413 0 0 0 5 0a8 8 0 0 1 15.978-.593c2.4-.647 4.227-2.69 4.47-5.288c.247-2.634.507-6.47.547-11.195c.008-1.028-.2-2.094-.716-3.072c-1.02-1.934-3.483-6.03-7.297-8.546c-1.027-.677-2.191-.94-3.272-.975c-1.49-.05-3.092-.092-4.758-.128m-3.956 3.365C26.812 15.217 25.503 16 24 16c-2.21 0-4-1.691-4-3.778C20 14.31 18.21 16 16 16s-4-1.691-4-3.778C12 14.31 10.21 16 8 16c-1.503 0-2.812-.783-3.496-1.94q-.004.456-.004.94v7.002c0 .83.669 1.498 1.497 1.498h20.006c.828 0 1.497-.669 1.497-1.498V15q0-.484-.004-.94m14.733 7.157c-1.269-1.999-3.18-4.442-5.607-5.878a2.6 2.6 0 0 0-1.33-.339H34a2 2 0 0 0-2 2v5.83c0 .955.676 1.772 1.624 1.888c1.522.187 3.968.387 6.695.219c1.906-.118 2.933-2.107 1.91-3.72M19 40.5a5.5 5.5 0 1 1-11 0a5.5 5.5 0 0 1 11 0M34.5 46a5.5 5.5 0 1 0 0-11a5.5 5.5 0 0 0 0 11"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            اسنپ
                                        </div>
                                    @else
                                        @if ($invoice->paid_amount == 0)
                                            <button type="button" wire:click="showPayment({{ $invoice }})"
                                                class="whitespace-nowrap rounded-2xl bg-green-600 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500 active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-green-500 dark:border-green-500 dark:text-white dark:focus-visible:outline-green-500 flex items-center gap-2">
                                                پرداخت
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    class="size-4">
                                                    <path fill="currentColor"
                                                        d="M19 14V6c0-1.1-.9-2-2-2H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2m-2 0H3V6h14zm-7-7c-1.66 0-3 1.34-3 3s1.34 3 3 3s3-1.34 3-3s-1.34-3-3-3m13 0v11c0 1.1-.9 2-2 2H4v-2h17V7z" />
                                                </svg>
                                            </button>
                                        @elseif($invoice->total_price > $invoice->paid_amount)
                                            <button type="button" wire:click="showPayment({{ $invoice }})"
                                                class="whitespace-nowrap rounded-2xl bg-orange-600 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500 active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-green-500 dark:border-green-500 dark:text-white dark:focus-visible:outline-green-500 flex items-center gap-2">
                                                پرداخت
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    class="size-4">
                                                    <path fill="currentColor"
                                                        d="M12 20a8 8 0 1 0 0-16a8 8 0 0 0 0 16m0 2C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10m-1-6h2v2h-2zm0-10h2v8h-2z" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" wire:click="showPayment({{ $invoice }})"
                                                class="whitespace-nowrap rounded-2xl bg-green-800 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500 active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-green-500 dark:border-green-500 dark:text-white dark:focus-visible:outline-green-500 flex items-center gap-2">
                                                پرداخت شده
                                                <svg xmlns="http://www.w3.org/2000/svg"viewBox="0 0 24 24"
                                                    class="size-4">
                                                    <path fill="currentColor"
                                                        d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8m4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4l8-8z" />
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

    @if ($showModal)
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
            <div x-cloak x-show="modalIsOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="modalIsOpen"
                x-on:keydown.esc.window="modalIsOpen = false" x-on:click.self="modalIsOpen = false"
                class="fixed inset-0 z-30 flex items-end justify-center bg-black/20 p-4 pb-8 backdrop-blur-md sm:items-center lg:p-8"
                role="dialog" aria-modal="true" aria-labelledby="defaultModalTitle">
                <!-- Modal Dialog -->
                <div class="bg-slate-100 w-10/12 p-4 rounded-xl">
                    <div class="flex px-3 mt-5">
                        <h3 class="text-xl grow font-bold">پرداختی ها </h3>
                        <button type="button" wire:click="addPayment"
                            class="text-white bg-green-700 hover:bg-green-800 rounded-lg text-sm px-5 py-2.5 text-center {{ $invoice_price <= 0 ? 'hidden' : '' }}">
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
                                            <select id="countries" wire:model.defer="account"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                @foreach (\App\Models\Account::all() as $account)
                                                    <option value="{{ $account->id }}">
                                                        {{ $account->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-4">
                                            <input type="text" data-jdp class=" border-2 rounded p-2"
                                                placeholder="تاریخ شمسی" wire:model="j_date" />
                                        </td>
                                        <td class="p-4">
                                            <input type="text" id="mobile" x-model="formatted"
                                                @input="formatNumber($event.target.value)"
                                                class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                                                placeholder="مبلغ به تومان" />
                                            <input type="text" id="mobile" x-model="realValue" class="hidden"
                                                placeholder="مبلغ به تومان" />
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
                                    @if (session()->has('error'))
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
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                disabled>
                                                <option>{{ $transaction->account->name }}</option>
                                            </select>
                                        </td>
                                        <td class="p-4">
                                            <input
                                                value="{{ \App\Common\Jalalian::fromDateTime($transaction->transaction_date)->format('%d %B %Y') }}"
                                                class=" border-2 rounded p-2" disabled>
                                        </td>
                                        <td class="p-4">
                                            <input
                                                class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                                                placeholder="مبلغ به تومان"
                                                value="{{ number_format($transaction->amount) }}" required disabled />
                                        </td>

                                    </tr>
                                    @php $sumPrice += $transaction->amount @endphp
                                @endforeach

                                <tr>
                                    <td class="p-4 font-bold">جمع پرداختی</td>
                                    <td class="p-4"></td>
                                    <td class="p-4 font-bold text-black text-lg">
                                        <span class="text-xs">{{ number_format($paid_amount) }} تومان</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-4 font-bold">مبلغ مانده</td>
                                    <td class="p-4"></td>
                                    <td class="p-4 font-bold text-black text-lg">
                                        <span class="text-xs">{{ number_format($invoice_price) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if ($userModal)
        <div x-data="{ modalIsOpen: @entangle('userModal') }">
            <div x-cloak x-show="modalIsOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="modalIsOpen"
                x-on:keydown.esc.window="modalIsOpen = false" x-on:click.self="modalIsOpen = false"
                class="fixed inset-0 z-30 flex items-end justify-center bg-black/20 p-4 pb-8 backdrop-blur-md sm:items-center lg:p-8 rounded-lg"
                role="dialog" aria-modal="true" aria-labelledby="defaultModalTitle">
                <!-- Modal Dialog -->
                <div x-show="modalIsOpen"
                    x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity"
                    x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
                    class="flex w-5xl flex-col gap-4 overflow-hidden rounded-2xl border border-neutral-300 bg-white text-neutral-600">
                    <!-- Dialog Body -->
                    <div class="px-4 py-8 max-h-[80vh] overflow-y-auto">
                        <livewire:users.user-show :id="$user->id" :limit="5" />
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{--   page loading when opens user's profile   --}}
    <div wire:loading wire:target="updateUserModal">
        <div x-transition.opacity.duration.200ms x-trap.inert.noscroll="modalIsOpen"
            x-on:keydown.esc.window="modalIsOpen = false" x-on:click.self="modalIsOpen = false"
            class="fixed inset-0 z-30 flex items-end justify-center bg-black/20 p-4 pb-8 backdrop-blur-md sm:items-center lg:p-8 rounded-lg"
            role="dialog" aria-modal="true" aria-labelledby="defaultModalTitle">
            <!-- Modal Dialog -->
            <div x-show="modalIsOpen"
                x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity"
                x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
                class="flex justify-center items-center w-5xl flex-col gap-4 overflow-hidden rounded-2xl border text-neutral-600">
                <!-- Dialog Body -->
                <div class="px-4 py-8 overflow-y-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
                        class="size-16 fill-on-surface motion-safe:animate-spin dark:fill-on-surface-dark">
                        <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                            opacity=".25" />
                        <path
                            d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
