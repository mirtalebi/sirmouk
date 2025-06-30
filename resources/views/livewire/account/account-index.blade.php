<div>
    <!-- Table Section -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        @if (session()->has('success'))
        <div class="flex">
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 3000)"
                x-show="show"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                         class="bg-green-200 text-green-800 p-3 rounded shadow-md my-2 mx-4">
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif
        <!-- Card -->
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-2xs overflow-hidden dark:bg-neutral-900 dark:border-neutral-700">
                        <!-- Header -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
                            <!-- Input -->
                            <div class="sm:col-span-1">
                                <h3 class="font-bold">حساب ها:</h3>
                            </div>
                            <!-- End Input -->
                            <div>
                                <div class="inline-flex gap-x-2">
                                    <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" href="{{ route('accounts.create') }}">
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                        ساخت حساب
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- End Header -->

                        <!-- Table -->
                        <div class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 border-collapse">
                            <div class="bg-gray-50 dark:bg-neutral- grid grid-cols-4">


                                    <div class="flex items-center gap-x-2 mx-4 py-2">
                                        <span class="text-s font-bold text-gray-800 dark:text-neutral-200">
                                          نام حساب:
                                        </span>
                                    </div>
                                <div class="flex items-center gap-x-2 mx-4 py-2">
                                        <span class="text-s font-bold text-gray-800 dark:text-neutral-200">
                                          موجودی:
                                        </span>
                                </div>
                                <div class="flex items-center gap-x-2 mx-4 py-2">
                                        <span class="text-s font-bold text-gray-800 dark:text-neutral-200">
                                          نوع:
                                        </span>
                                </div>

                            </div>

                            <div class="divide-gray-200 dark:divide-neutral-700">
                            @forelse($accounts as $account)
                                    <div class="grid grid-cols-4 border rounded px-4 py-2 my-2 mx-2 items-center hover:bg-slate-100">
                                        <a href="{{ route('account.transactions.list', $account->id) }}">
                                        <div>{{ $account->name }}</div>
                                        </a>
                                        <div>{{ number_format($account->balance) }}</div>
                                        <div>{{ $account->type == 'debtor' ? 'بدهکار' : 'بستانکار' }}</div>
                                        <div class="flex gap-4">
                                            <div class="inline-flex">
                                                <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-orange-400 text-slate-900 hover:bg-orange-500 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" href="{{ route('accounts.edit', $account->id) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                    </svg>
                                                    ویرایش
                                                </a>
                                            </div>
                                            <div class="inline-flex">
                                                <button type="button" wire:click="delete({{$account->id}})" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-slate-900 hover:bg-red-900 focus:outline-hidden focus:bg-red-900 disabled:opacity-50 disabled:pointer-events-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    حذف
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                            @empty
                                <span class="font-semibold text-red-600">There is no accounts</span>
                            @endforelse

                            </div>
                        </div>
                        <!-- End Table -->

                        <!-- Footer -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-neutral-700">
                            <div class="max-w-sm space-y-3">
                                تعداد: {{ $accounts->count() }}
                            </div>

                        </div>
                        <!-- End Footer -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>
    <!-- End Table Section -->
</div>
