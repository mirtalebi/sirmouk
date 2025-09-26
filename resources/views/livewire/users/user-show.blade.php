<div
    x-data="{ dangerModalIsOpen: @entangle('editUserModal') }"
>
    <div class="relative overflow-x-auto sm:rounded-lg">
        <h2 class="">نام کاربر: <span class="font-bold text-2xl">{{ $user->name }}</span> <button type="button" wire:click="openUserModal()" class="text-sm text-blue-500 mr-2 cursor-pointer">ویرایش</button></h2>
        <h2 class="font-semibold text-gray-900">{{ $user->mobile }}</h2>
        <div>
            <div class="grid gap-2 mt-4">
                <h2><span class="font-bold text-2xl">آدرس ها:</span></h2>
                <table class="w-full text-right text-sm text-neutral-600 mt-2">
                    <thead class="border-b border-neutral-300 bg-neutral-50 text-sm text-neutral-900 ">
                    <tr>
                        <th scope="col" class="p-4">شماره آدرس</th>
                        <th scope="col" class="p-4">آدرس</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-300 dark:divide-neutral-700">
                    @forelse ($addresses as $address)
                        <tr>
                            <td class="p-4">{{ $address->id }}</td>
                            <td class="p-4 font-bold text-black">{{ $address->address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-4 font-bold text-red-500">                آدرسی ثبت نشده!

                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <h2><span class="font-bold text-2xl">فاکتور ها:</span></h2>
                <h2 class="">جمع فاکتور ها: <span class="font-bold">{{ number_format($totalPrice) }} <span class="text-sm font-medium">تومان</span></span></h2>
                <table class="w-full text-right text-sm text-neutral-600 mt-2">
                    <thead class="border-b border-neutral-300 bg-neutral-50 text-sm text-neutral-900 ">
                    <tr>
                        <th scope="col" class="p-4">شماره فاکتور</th>
                        <th scope="col" class="p-4">تاریخ ثبت</th>
                        <th scope="col" class="p-4">مبلغ کل</th>
                        <th scope="col" class="p-4">عملیات</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-300 dark:divide-neutral-700">
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td class="p-4">{{ $invoice->id }}</td>
                            <td class="p-4">{{ $invoice->getCreatedAtDate() }}</td>
                            <td class="p-4 font-bold text-black">
                                {{ number_format($invoice->total_price) }} تومان
                            </td>
                            <td>
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('invoice.view', ['invoiceId' => $invoice->id, 'secretKey' => $invoice->url_secret]) }}"
                                       class="text-blue-600 hover:underline mx-2">مشاهده</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-4 font-bold text-red-500">                فاکتوری ثبت نشده!

                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
</div>
    </div>
    <div x-cloak x-show="dangerModalIsOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="dangerModalIsOpen" x-on:keydown.esc.window="dangerModalIsOpen = false" x-on:click.self="dangerModalIsOpen = false" class="fixed inset-0 z-30 flex items-end justify-center bg-black/20 p-4 pb-8 backdrop-blur-md sm:items-center lg:p-8 sm:min-w-xl" role="dialog" aria-modal="true" aria-labelledby="dangerModalTitle">
        <!-- Modal Dialog -->
        <div x-show="dangerModalIsOpen" x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="flex max-w-lg flex-col gap-4 overflow-hidden rounded-sm border border-neutral-300 bg-white text-neutral-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 sm:min-w-xl py-4">
            <!-- Dialog Header -->
            <div class="flex items-center justify-between border-b border-neutral-300 bg-neutral-50/60 px-4 py-2 dark:border-neutral-700 dark:bg-neutral-950/20">
                <div class="flex items-center justify-center rounded-full bg-red-500/20 text-red-500 p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <button x-on:click="dangerModalIsOpen = false" aria-label="close modal">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="1.4" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <!-- Dialog Body -->
            <div class="px-4 text-center">
                <form wire:submit="saveUserEdit">
                    <div class="grid gap-6">
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">نام کاربر</label>
                            <input type="text" wire:model="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="" required="">
                            @error('name')
                            <div class="text-sm text-red-500">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">شماره موبایل</label>
                            <input type="text" wire:model="mobile" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="" required="">
                            @error('mobile')
                            <div class="text-sm text-red-500">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-green-700 rounded-lg hover:bg-green-800">
                        ثبت ویرایش
                    </button>
                    <button type="button" wire:click="cancelUserEdit()" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800">
                        لفو ویرایش
                    </button>
                </form>
            </div>
        </div>
    </div>

    </div>
