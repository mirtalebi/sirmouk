<div class="mt-8">
    <h2 class="font-bold text-2xl mb-4">محصولات فروخته شده:</h2>
    <form wire:submit="submit" >
        <div class="flex">
            <div>
                <div class="my-2 mx-4">
                    <label class="block text-sm mb-2 dark:text-white font-bold" for="datepicker">از تاریخ:</label>
                    <input
                        type="text"
                        data-jdp
                        class=" border-2 rounded p-2"
                        placeholder="تاریخ شمسی"
                        wire:model.defer="from_date" />
                    @error('from_date')
                    <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="my-2 mx-4">
                    <label class="block text-sm mb-2 dark:text-white font-bold" for="datepicker">تا تاریخ:</label>
                    <input
                        type="text"
                        data-jdp
                        class=" border-2 rounded p-2"
                        placeholder="تا تاریخ"
                        wire:model.defer="to_date" />
                    @error('to_date')
                    <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="my-2 mx-4 w-full">
                <h3 class="mb-4 font-semibold text-gray-900 dark:text-white">محصولات:</h3>
                <div class="items-center w-full grid grid-cols-6 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @forelse($showProducts as $product)
                        <div class="w-full border-b border-r col-span-1 border-gray-200 dark:border-gray-600">
                            <div class="flex items-center ps-3">
                                <input type="checkbox" wire:model="selectedProducts.{{ $product->id }}" value="{{ $product->id }}" class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded-sm">
                                <label for="vue-checkbox-list" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $product->name }}</label>
                            </div>
                        </div>
                    @empty
                        محصولی یافت نشد!
                    @endforelse
                </div>
            </div>

        </div>
        <div class="my-2 mx-4">
            <button type="submit" class="py-2 px-3 rounded bg-green-500 hover:bg-green-600 text-white">ادامه</button>
        </div>
    </form>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    نام محصول
                </th>
                <th scope="col" class="px-6 py-3">
                    تعداد
                </th>
                <th scope="col" class="px-6 py-3">
                    قیمت واحد
                </th>
                <th scope="col" class="px-6 py-3">
                    مجموع قیمت
                </th>
            </tr>
            </thead>
            <tbody>
            @forelse($products  as $product)
                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $product['name'] }}
                    </th>
                    <td class="px-6 py-4 font-bold">
                        {{ number_format($product['quantity']) }}
                    </td>
                    <td class="px-6 py-4 font-bold">
                        {{ number_format($product['price']) }}
                    </td>
                    <td class="px-6 py-4 font-bold">
                        {{ number_format($product['quantity'] * $product['price']) }}
                    </td>
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>
    </div>


</div>
