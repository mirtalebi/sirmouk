<div x-data='{ tempBasket: {},
    products: @json(\App\Models\Product::all()),
    getProduct(id) { return this.products.find((item) => item.id == id) },
    addToBasket(id) { this.tempBasket[id] = this.tempBasket[id] ? this.tempBasket[id] + 1 : 1; },
    removeFromBasket(id) {
        if (!this.tempBasket[id]) return;
        this.tempBasket[id] -= 1;
        if (this.tempBasket[id] == 0) delete this.tempBasket[id];
    },

{{--    courierPrice: 0, --}}
{{--    discountPrice: 0, --}}
{{--    addedPackagingPrice: 0, --}}
    getSumPackaging() {
        sum = 0;
        for(const [key, count] of Object.entries(this.tempBasket)) {
            sum += count * this.getProduct(key).packaging_amount
        }
{{--        sum -= -(this.addedPackagingPrice); --}}
        $wire.packaging_price = sum;
        return sum;
    },

    getSumTempBasket() {
        sum = 0;
        for(const [key, count] of Object.entries(this.tempBasket)) {
            sum += (count * this.getProduct(key).price);
        }
        sum += this.getSumPackaging();
{{--        sum -= -(this.courierPrice); --}}
{{--        sum -= this.discountPrice; --}}
        $wire.total_price = sum;
        return sum;
    },
    printUsingIframe(customer, basket) {


    }
    }'
    @basket-updated.window="console.log($event.detail.basket); tempBasket = (Array.isArray($event.detail.basket) ? Object.fromEntries($event.detail.basket) : $event.detail.basket);"
    @print-invoice-client.window="
        let customer = $event.detail.customer;
        let basket = $event.detail.basket;
        console.log(customer);

        customer.discount_price = customer.discount_price ? Number(customer.discount_price) : 0;
        customer.courier_price = customer.courier_price ? Number(customer.courier_price) : 0;
        customer.packaging_price = customer.packaging_price ? Number(customer.packaging_price) : 0;

        document.getElementById('customer-name').textContent = customer.name;
        document.getElementById('customer-mobile').textContent = customer.mobile;
        document.getElementById('customer-address').textContent = customer.address;
        document.getElementById('discount-price').textContent = '-' + customer.discount_price.toLocaleString();
        document.getElementById('packaging-price').textContent = customer.packaging_price.toLocaleString();
        document.getElementById('tax-price').textContent = customer.tax_price.toLocaleString();
        document.getElementById('delivery-price').textContent = customer.courier_price.toLocaleString();
        document.getElementById('print-order-id').textContent = '#' + customer.id;
        document.getElementById('print-order-date').textContent = 'ساعت ' + customer.time;

        document.getElementById('discount-box').style.display = customer.discount_price ? 'flex' : 'none';
        document.getElementById('delivery-box').style.display = customer.courier_price ? 'flex' : 'none';
        document.getElementById('packaging-box').style.display = customer.packaging_price ? 'flex' : 'none';
        document.getElementById('tax-box').style.display = customer.tax_price ? 'flex' : 'none';

        // Fill items
        const itemsBody = document.getElementById('items-table');
        itemsBody.innerHTML = ''; // clear previous rows

        let total = Number(customer.courier_price) - Number(customer.discount_price) + Number(customer.packaging_price) + Number(customer.tax_price);
        basket.forEach(item => {
            const row = `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.pivot.quantity}</td>
                    <td>${(item.pivot.quantity * item.pivot.unit_price).toLocaleString()}</td>
                </tr>
            `;
            itemsBody.innerHTML += row;

            total += item.pivot.quantity * item.pivot.unit_price;
        });

        // Fill total
        document.getElementById('final-total').textContent = total.toLocaleString();

        const content = document.getElementById('to-print').innerHTML;
        const iframe = document.getElementById('print-iframe');

        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write(`<html dir='rtl'><body style='font-family: Peyda; padding: 5px;'>${content}`);
        doc.close();

        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    ">

    <script src="/assets/js/num2persian.min.js"></script>
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
                                <td class="p-1 font-bold">مبلغ بسته بندی</td>
                                <td class="p-4 font-bold text-black text-lg">
                                    <span x-text="getSumPackaging()"></span> <span class="text-xs">تومان</span>
                                </td>
                            </tr>

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
                        <button type="button" wire:click="openSnapModal"
                            class="mt-2 w-full px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition">
                            وارد کردن از لینک اسنپ
                        </button>
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
                            {{--                            courierPrice = raw; --}}
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
                            {{--                            discountPrice = raw; --}}
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
                    <div class="mb-3" x-data="{
                        realValue: @entangle('addedPackagingPrice'),
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
                            {{--                            addedPackagingPrice = this.realValue; --}}
                            this.formatted = Number(raw).toLocaleString();
                            $wire.addedPackagingPrice = raw;
                        }
                    }">
                        <label for="mobile" class="block mb-2 text-sm font-medium text-gray-900">مبلغ بسته بندی
                            مازاد</label>
                        <input type="text" id="mobile" x-model="formatted"
                            @input="formatNumber($event.target.value)"
                            class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="به تومان" required />
                        @error('addedPackagingPrice')
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

        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="relative w-full sm:max-w-xs">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-neutral-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.603 10.601z" />
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="جستجوی شماره فاکتور، نام یا تلفن..."
                    class="w-full rounded-xl border border-neutral-300 bg-white py-2 pr-10 pl-8 text-sm text-neutral-900 placeholder-neutral-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                @if ($search)
                    <button type="button" wire:click="$set('search', '')"
                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400 hover:text-neutral-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>

        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm text-neutral-600">
                <thead class="border-b border-neutral-300 bg-neutral-50 text-sm text-neutral-900">
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
                    @forelse ($invoices as $invoice)
                        <tr wire:key="invoice-{{ $invoice->id }}" class="hover:bg-neutral-50 transition-colors">
                            <td class="p-4">{{ $invoice->id }}</td>
                            <td class="p-4">
                                {{ $invoice->user?->name ?? ($invoice->snap_user_credentials['username'] ?? '') }}
                            </td>
                            <td class="p-4 text-right">
                                {{ $invoice->user?->mobile ?? ($invoice->snap_user_credentials['mobile'] ?? '') }}
                            </td>
                            <td class="p-4">{{ $invoice->getCreatedAtDate() }}</td>
                            <td class="p-4 font-bold text-black">
                                {{ number_format($invoice->total_price) }} تومان
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('invoice.view', ['invoiceId' => $invoice->id, 'secretKey' => $invoice->url_secret]) }}"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-200 bg-white text-blue-600 shadow-sm transition hover:border-blue-300 hover:bg-blue-50"
                                        aria-label="فاکتور">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5">
                                            <path fill="currentColor"
                                                d="M21 8V7l-3-3H6L3 7v11a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V8zm-2 0H5l1-3h12l1 3zM9 13h6v2H9v-2zm0-4h6v2H9V9z" />
                                        </svg>
                                        <span class="sr-only">فاکتور</span>
                                    </a>

                                    <button type="button" wire:click="printInvoice({{ $invoice->id }})"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-200 bg-white text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50"
                                        aria-label="پرینت فیش">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5">
                                            <path fill="currentColor"
                                                d="M19 8h-1V3H6v5H5c-1.1 0-2 .9-2 2v7h4v4h10v-4h4v-7c0-1.1-.9-2-2-2zm-9-4h6v4H10V4zm9 15h-3v3H8v-3H5v-5h14v5zm-5-8H8V9h6v2z" />
                                        </svg>
                                        <span class="sr-only">پرینت فیش</span>
                                    </button>

                                    <button type="button" wire:click="showPreview({{ $invoice->id }})"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-200 bg-white text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50"
                                        aria-label="مشاهده فاکتور">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5">
                                            <path fill="currentColor"
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm1-13h-2v6h2V7zm0 8h-2v2h2v-2z" />
                                        </svg>
                                        <span class="sr-only">مشاهده فاکتور</span>
                                    </button>

                                    @if ($invoice->created_at->isToday() || Auth::user()->hasRole('admin'))
                                        <button type="button" wire:click="editInvoice({{ $invoice->id }})"
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-yellow-300 bg-yellow-50 text-yellow-700 shadow-sm transition hover:border-yellow-400 hover:bg-yellow-100"
                                            aria-label="ویرایش">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                class="h-5 w-5">
                                                <path fill="currentColor"
                                                    d="M3 17.25V21h3.75l10.98-10.98l-3.75-3.75L3 17.25m16.71-11.04c.39-.39.39-1.02 0-1.41l-2.5-2.5a.9959.9959 0 0 0-1.41 0l-1.83 1.83l3.75 3.75l1.99-1.67z" />
                                            </svg>
                                            <span class="sr-only">ویرایش</span>
                                        </button>
                                    @endif

                                    @if ($invoice->is_snap)
                                        <span
                                            class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-2 text-sm font-semibold text-emerald-800">
                                            <span
                                                class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-white">!</span>
                                            اسنپ
                                        </span>
                                    @else
                                        @if ($invoice->paid_amount == 0)
                                            <button type="button" wire:click="showPayment({{ $invoice->id }})"
                                                class="whitespace-nowrap rounded-2xl bg-green-600 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500 active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-green-500 dark:border-green-500 dark:text-white dark:focus-visible:outline-green-500 flex items-center gap-2">
                                                پرداخت
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    class="size-4">
                                                    <path fill="currentColor"
                                                        d="M19 14V6c0-1.1-.9-2-2-2H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2m-2 0H3V6h14zm-7-7c-1.66 0-3 1.34-3 3s1.34 3 3 3s3-1.34 3-3s-1.34-3-3-3m13 0v11c0 1.1-.9 2-2 2H4v-2h17V7z" />
                                                </svg>
                                            </button>
                                        @elseif($invoice->total_price > $invoice->paid_amount)
                                            <button type="button" wire:click="showPayment({{ $invoice->id }})"
                                                class="whitespace-nowrap rounded-2xl bg-orange-600 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-500 active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-orange-500 dark:border-orange-500 dark:text-white dark:focus-visible:outline-orange-500 flex items-center gap-2">
                                                پرداخت ناقص
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    class="size-4">
                                                    <path fill="currentColor"
                                                        d="M12 20a8 8 0 1 0 0-16a8 8 0 0 0 0 16m0 2C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10m-1-6h2v2h-2zm0-10h2v8h-2z" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" wire:click="showPayment({{ $invoice->id }})"
                                                class="whitespace-nowrap rounded-2xl bg-green-800 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500 active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-green-700 dark:border-green-700 dark:text-white dark:focus-visible:outline-green-500 flex items-center gap-2">
                                                پرداخت شده
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
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
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-neutral-400">
                                هیچ فاکتوری با این مشخصات پیدا نشد.
                            </td>
                        </tr>
                    @endforelse
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

    @if ($showPreviewModal)
        <div x-data="{ modalIsOpen: @entangle('showPreviewModal') }">
            <div x-cloak x-show="modalIsOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="modalIsOpen"
                x-on:keydown.esc.window="$wire.call('closePreview')" x-on:click.self="$wire.call('closePreview')"
                class="fixed inset-0 z-40 flex items-end justify-center bg-black/20 p-4 pb-8 backdrop-blur-md sm:items-center lg:p-8"
                role="dialog" aria-modal="true" aria-labelledby="previewModalTitle">
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b px-6 py-4">
                        <div>
                            <h3 id="previewModalTitle" class="text-lg font-bold text-slate-900">پیش‌نمایش سفارش
                                #{{ $previewInvoice?->id ?? '' }}</h3>
                            <p class="text-sm text-slate-500">جزئیات سفارش و هزینه‌ها</p>
                        </div>
                        <button type="button" wire:click="closePreview"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-slate-700 transition hover:bg-slate-100">
                            <span class="sr-only">بستن</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5">
                                <path fill="currentColor"
                                    d="M18.3 5.71a1 1 0 0 0-1.41 0L12 10.59 7.11 5.7A1 1 0 1 0 5.7 7.11L10.59 12l-4.9 4.89a1 1 0 1 0 1.41 1.41L12 13.41l4.89 4.9a1 1 0 0 0 1.41-1.41L13.41 12l4.9-4.89a1 1 0 0 0 0-1.4z" />
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-6 px-6 py-5">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <h4 class="text-sm font-semibold text-slate-700">نام مشتری</h4>
                                <p class="mt-2 text-sm text-slate-900">
                                    {{ $previewInvoice?->user?->name ?? data_get(json_decode($previewInvoice?->snap_user_credentials, true), 'username', '') }}
                                </p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <h4 class="text-sm font-semibold text-slate-700">موبایل</h4>
                                <p class="mt-2 text-sm text-slate-900">
                                    {{ $previewInvoice?->user?->mobile ?? data_get(json_decode($previewInvoice?->snap_user_credentials, true), 'mobile', '') }}
                                </p>
                            </div>
                            <div class="sm:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <h4 class="text-sm font-semibold text-slate-700">آدرس</h4>
                                <p class="mt-2 text-sm text-slate-900">
                                    {{ $previewInvoice?->address?->address ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-slate-200 p-4">
                            <div class="mb-4 flex items-center justify-between text-sm font-semibold text-slate-700">
                                <span>اقلام خرید</span>
                                <span
                                    class="text-slate-500">{{ number_format($previewInvoice?->products->sum(fn($item) => $item->pivot?->quantity * $item->pivot?->unit_price)) }}
                                    تومان</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-right text-sm text-slate-600">
                                    <thead class="border-b border-slate-200 text-slate-900">
                                        <tr>
                                            <th class="py-3 pr-3 text-start">نام کالا</th>
                                            <th class="py-3 px-3">تعداد</th>
                                            <th class="py-3 px-3 text-start">جمع</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @foreach ($previewInvoice?->products ?? [] as $product)
                                            <tr>
                                                <td class="py-3 pr-3 text-start text-sm text-slate-900">
                                                    {{ $product->name }}</td>
                                                <td class="py-3 px-3 text-sm">{{ $product->pivot?->quantity ?? 0 }}
                                                </td>
                                                <td class="py-3 px-3 text-sm text-slate-900">
                                                    {{ number_format(($product->pivot?->quantity ?? 0) * ($product->pivot?->unit_price ?? 0)) }}
                                                    تومان</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center justify-between text-sm text-slate-700">
                                    <span>هزینه بسته‌بندی</span>
                                    <span
                                        class="font-semibold text-slate-900">{{ number_format($previewInvoice?->packaging_price ?? 0) }}
                                        تومان</span>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center justify-between text-sm text-slate-700">
                                    <span>هزینه ارسال</span>
                                    <span
                                        class="font-semibold text-slate-900">{{ number_format($previewInvoice?->courier_price ?? 0) }}
                                        تومان</span>
                                </div>
                            </div>
                            <div class="sm:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center justify-between text-sm text-slate-700">
                                    <span>تخفیف</span>
                                    <span
                                        class="font-semibold text-slate-900">-{{ number_format($previewInvoice?->discount_price ?? 0) }}
                                        تومان</span>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-slate-100 p-4">
                            <div class="flex items-center justify-between text-base font-semibold text-slate-900">
                                <span>جمع نهایی</span>
                                <span>{{ number_format(optional($previewInvoice)->total_price ?? 0) }} تومان</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 border-t px-6 py-4">
                        <button type="button" wire:click="closePreview"
                            class="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            بستن
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{--   page loading when opens user's profile   --}}
    <div wire:loading wire:target="updateUserModal" x-data="{ modalIsOpen: @entangle('userModal') }">
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

    <iframe id="print-iframe" style="display: none;"></iframe>

    <div id="to-print" style="display: none;">

        <style>
            @font-face {
                font-family: "Peyda";
                /*a name to be used later*/
                src: url("/assets/fonts/woff/PeydaWebFaNum-Regular.woff");
                /*URL to font*/
            }

            @font-face {
                font-family: "PeydaBold";
                /*a name to be used later*/
                src: url("/assets/fonts/woff/PeydaWebFaNum-Bold.woff");
                /*URL to font*/
            }

            #to-print {
                width: 260px;
                /* Perfect for 58mm printer */
                font-family: sans-serif;
                direction: rtl;
                padding: 12px;
                background: #fff;
            }

            /* Header layout */
            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 12px;
            }

            .header-left {
                font-size: 12px;
                line-height: 1.5;
                text-align: center;
            }

            .header-right img {
                width: 70px;
            }

            .section-title {
                font-weight: bold;
                margin: 14px 0 6px 0;
                border-bottom: 1px dashed #000;
                padding-bottom: 4px;
                font-size: 14px;
            }

            .info {
                font-size: 13px;
                line-height: 1.7;
            }

            .print-table table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 6px;
                font-size: 13px;
            }

            .print-table table th {
                text-align: right;
                border-bottom: 1px dashed #aaa;
                padding: 6px 0;
            }

            .print-table table td {
                padding: 6px 0;
            }

            .print-table table td:nth-child(3) {
                text-align: left;
                font-weight: bold;
            }

            .print-table table td:last-child {
                text-align: left;
                font-weight: bold;
            }

            .total-box {
                margin-top: 12px;
                padding-top: 10px;
                border-top: 2px dashed #000;
                display: flex;
                justify-content: space-between;
                font-size: 15px;
                font-weight: bold;
            }

            .footer {
                text-align: center;
                margin-top: 16px;
                font-size: 12px;
                color: #555;
            }
        </style>

        <!-- HEADER -->
        <div class="header">

            <!-- LEFT: Date + Order ID -->
            <div class="header-left">
                <div>شماره سفارش<br><strong id="print-order-id"></strong><br>
                    <span id="print-order-date"></span>
                </div>
            </div>

            <!-- RIGHT: LOGO -->
            <div class="header-right">
                <img src="{{ url('/assets/logo/main.png') }}">
            </div>

        </div>

        <!-- ORDER DETAILS -->
        <div class="section-title">مشخصات مشتری</div>
        <div class="info">
            نام مشتری: <strong id="customer-name"></strong><br>
            موبایل: <strong id="customer-mobile"></strong><br>
            <small id="customer-address"></small>
        </div>

        <!-- ITEMS -->
        <div class="section-title">اقلام خرید</div>
        <div class="print-table">
            <table>
                <thead>
                    <tr>
                        <th>نام کالا</th>
                        <th>تعداد</th>
                        <th style="text-align: left;">قیمت (تومان)</th>
                    </tr>
                </thead>
                <tbody id="items-table">
                </tbody>
            </table>
        </div>

        <!-- DISCOUNT -->
        <div id="discount-box" class="total-box">
            <span>تخفیف:</span>
            <span id="discount-price">0</span>
        </div>

        <!-- DELIVERY -->
        <div id="delivery-box" class="total-box">
            <span>هزینه ارسال:</span>
            <span id="delivery-price">0</span>
        </div>

        <div id="packaging-box" class="total-box">
            <span>هزینه بسته بندی:</span>
            <span id="packaging-price">0</span>
        </div>

        <div id="tax-box" class="total-box">
            <span>مالیات بر ارزش افزوده:</span>
            <span id="tax-price">0</span>
        </div>

        <!-- FINAL TOTAL -->
        <div class="total-box" style="border-top: 2px solid #000; margin-top: 6px;">
            <span>جمع نهایی:</span>
            <span id="final-total">0</span>
        </div>

        <div class="footer">سپاس از خرید شما<br>منو دیجیتال: sirmouk.ir</div>

    </div>


    <!-- SnapFood Modal -->
    <div x-cloak x-data="{ isOpen: @entangle('showSnapModal') }" x-show="isOpen" x-transition.opacity.duration.200ms
        x-trap.inert.noscroll="isOpen" x-on:keydown.esc.window="$wire.call('closeSnapModal')"
        x-on:click.self="$wire.call('closeSnapModal')"
        class="fixed inset-0 z-30 flex items-end justify-center bg-black/20 p-4 backdrop-blur-md">
        <div class="w-full max-w-md rounded-t-3xl bg-white p-6 shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">وارد کردن از اسنپ‌فود</h2>
                <button type="button" wire:click="closeSnapModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Error Message -->
            @if ($snapModalError)
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-700">{{ $snapModalError }}</p>
                </div>
            @endif

            <!-- URL Input -->
            <div class="mb-4">
                <label for="snap_url" class="block mb-2 text-sm font-medium text-gray-900">
                    لینک سفارش اسنپ‌فود
                </label>
                <input type="text" id="snap_url" wire:model="snapFoodUrl" placeholder="https://snappfood.ir/..."
                    class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" />
                <p class="text-xs text-gray-500 mt-1">از صفحه پیگیری سفارش خود کپی کنید</p>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-2">
                <button type="button" wire:click="importFromSnapFood" wire:loading.attr="disabled"
                    class="flex-1 px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition disabled:bg-gray-400">
                    <span wire:loading.remove>واردکردن</span>
                    <span wire:loading class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
                            class="w-4 h-4 fill-white motion-safe:animate-spin inline-block mr-1">
                            <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                opacity=".25" />
                            <path
                                d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                        </svg>
                        در حال بارگذاری...
                    </span>
                </button>
                <button type="button" wire:click="closeSnapModal"
                    class="px-4 py-2 bg-gray-200 text-gray-900 text-sm rounded-lg hover:bg-gray-300 transition">
                    انصراف
                </button>
            </div>
        </div>
    </div>



</div>
