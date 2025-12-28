<div
    x-data="{
    deleteModal: false,
    selectedTransaction: { id: null, amount: null, type: null, account: '' }
}"
    class="border-2 rounded-2xl mx-2 py-4 items-center" style="[x-cloak] {display: none !important;}">

    @forelse($transactions as $transaction)
        <div
            class="flex items-center">

            <div class="w-full px-4 cursor-pointer transition border-b bg-slate-50 mb-1"
                 x-data="{ show: false, iconShow: false}"
                 @click="show = !show"
            >
                <div class="grid grid-cols-6">
                    <div
                        class="flex gap-2 items-center col-span-2">
                        <div x-show="show" x-cloak>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 font-bold">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>

                        </div>
                        <div x-show="!show">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 font-bold">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>

                        <h2 class="font-bold text-l">{{ $transaction->account->name }}</h2>
                    </div>
                    <div class="grid grid-cols-2 my-2 text-sm col-span-3">
                        <div class="mx-4">مبلغ: <span class="font-bold {{ $transaction->type == 'credit' ? 'text-green-600' : 'text-red-600' }}">{{ number_format($transaction->amount) }}</span></div>
                        {{--                {{ dump($transaction->transaction_date) }}--}}
                        <div><span class="font-bold">
                        {{ \Morilog\Jalali\Jalalian::fromCarbon($transaction->transaction_date)->format('Y/m/d') }}</span></div>
                    </div>
                    <div class="flex justify-center items-center">
                        @if($transaction->type == 'credit')
                            <svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512" class="size-6 text-green-600">
                                <path fill="currentColor" d="M256 48a208 208 0 1 1 0 416a208 208 0 1 1 0-416m0 464a256 256 0 1 0 0-512a256 256 0 1 0 0 512M151.2 217.4c-4.6 4.2-7.2 10.1-7.2 16.4c0 12.3 10 22.3 22.3 22.3H208v96c0 17.7 14.3 32 32 32h32c17.7 0 32-14.3 32-32v-96h41.7c12.3 0 22.3-10 22.3-22.3c0-6.2-2.6-12.1-7.2-16.4l-91-84c-3.8-3.5-8.7-5.4-13.9-5.4s-10.1 1.9-13.9 5.4l-91 84z"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512" class="size-6 text-red-600">
                                <path fill="currentColor" d="M256 464a208 208 0 1 1 0-416a208 208 0 1 1 0 416m0-464a256 256 0 1 0 0 512a256 256 0 1 0 0-512m120.9 294.6c4.5-4.2 7.1-10.1 7.1-16.3c0-12.3-10-22.3-22.3-22.3H304v-96c0-17.7-14.3-32-32-32h-32c-17.7 0-32 14.3-32 32v96h-57.7c-12.3 0-22.3 10-22.3 22.3c0 6.2 2.6 12.1 7.1 16.3l107.1 99.9c3.8 3.5 8.7 5.5 13.8 5.5s10.1-2 13.8-5.5z"/>
                            </svg>
                        @endif
                    </div>
                </div>
                <div
                    x-show="show"
                    x-cloak
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"

                >
                    <div class="grid grid-cols-3 text-sm">
                        <div>دسته بندی: <span class="font-bold">{{ $transaction->category->name }}</span></div>
                        <div>موجودی: <span class="font-bold">{{ number_format($transaction->current_balance) }}</span></div>
                        <div>کد رهگیری: <span class="font-bold">{{ $transaction->tracking_code }}</span></div>
                    </div>
                    <div class="flex justify-between">
                        <div class="col-span-2">توضیحات: <span>{{ $transaction->description }}</span></div>
                        <div class="ml-8">
                            <button
                                @click="
                                    selectedTransaction.id = {{ $transaction->id }};
                                    selectedTransaction.amount = '{{ number_format($transaction->amount) }}';
                                    selectedTransaction.account = '{{ $transaction->account->name }}';
                                    selectedTransaction.type = '{{ $transaction->type }}';
                                    deleteModal = true;
"
                                type="button"
                                class="flex text-red-500 font-bold text-sm hover:text-red-700 cursor-pointer hover:underline"
                            >
                                حذف تراکنش
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    @empty
        <div class="items-center font-bold text-2xl">
            تراکنشی ثبت نشده
        </div>
    @endforelse

        <div class="w-full p-1 flex justify-center items-center overflow-y-hidden">
            <div class="flex justify-center items-center flex-wrap w-full">
                {{ $transactions->links() }}
            </div>
        </div>




        <div>
            <div x-cloak x-show="deleteModal" x-transition.opacity.duration.200ms x-trap.inert.noscroll="deleteModal" x-on:keydown.esc.window="deleteModal = false" x-on:click.self="deleteModal = false" class="fixed inset-0 z-30 flex items-end justify-center bg-black/20 p-4 pb-8 backdrop-blur-md sm:items-center lg:p-8 sm:min-w-xl" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
                <!-- Modal Dialog -->
                <div x-show="deleteModal" x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="flex max-w-lg flex-col gap-4 overflow-hidden rounded-sm border border-neutral-300 bg-white text-neutral-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 sm:min-w-xl py-4">
                    <!-- Dialog Header -->
                    <div class="flex items-center justify-between border-b border-neutral-300 bg-neutral-50/60 px-4 py-2 dark:border-neutral-700 dark:bg-neutral-950/20"> <div class="flex items-center justify-center rounded-full bg-red-500/20 text-red-500 p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <button x-on:click="deleteModal = false" aria-label="close modal">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="1.4" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Dialog Body -->
                    <div class="px-4 text-center">
                        <div>آیا از حذف تراکنش اطمینان دارید؟</div>

                        مبلغ <span class="font-bold" x-text="selectedTransaction.amount"></span>
                                <span x-text="selectedTransaction.type == 'credit'
                                    ? ' از حساب ' + selectedTransaction.account + ' کسر می‌شود!'
                                    : ' به حساب ' + selectedTransaction.account + ' بازمی‌گردد!'">
                         </span>
                        <div>
                            <button @click="deleteModal = false" type="button" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-slate-600 rounded-lg hover:bg-slate-700"> لفو حذف
                            </button>
                            <button @click="$wire.deleteTransaction(selectedTransaction.id); deleteModal = false" type="button" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800"> حذف </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>



</div>
