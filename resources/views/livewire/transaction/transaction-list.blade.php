<div class="border-2 rounded-2xl mx-2 py-4 items-center" style="[x-cloak] {display: none !important;}">
{{--    {{ dd($transactions) }}--}}
    @forelse($transactions as $transaction)
    <div class="flex items-center">
        <div class="mr-2">
            <button wire:click="showModal({{ $transaction->id }})" type="button" class="text-red-500 hover:text-red-700 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="size-8">
                    <path fill="currentColor" d="M7 21q-.825 0-1.412-.587T5 19V6H4V4h5V3h6v1h5v2h-1v13q0 .825-.587 1.413T17 21zm2-4h2V8H9zm4 0h2V8h-2z"/>
                </svg>
            </button>
        </div>
        <div class="my-1 mx-2 w-full {{ $transaction->type == 'credit' ? 'bg-green-300 hover:bg-green-400' : 'bg-red-300 hover:bg-red-400' }} rounded-xl px-4 cursor-pointer transition"
             x-data="{ show: false, iconShow: false}"
             @click="show = !show"
        >
            <div class="grid grid-cols-2">
                <div
                    class="flex gap-2 items-center">
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
                <div class="grid grid-cols-2 my-2 text-sm">
                    <div class="mx-4">مبلغ: <span class="font-bold">{{ number_format($transaction->amount) }}</span></div>
                    {{--                {{ dump($transaction->transaction_date) }}--}}
                    <div><span class="font-bold">
                        {{ \Morilog\Jalali\Jalalian::fromCarbon($transaction->transaction_date)->format('Y/m/d') }}</span></div>
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
                    <div>نام حساب: <span class="font-bold">{{ $transaction->category->name }}</span></div>
                    <div>موجودی: <span class="font-bold">{{ number_format($transaction->current_balance) }}</span></div>
                    <div>کد رهگیری: <span class="font-bold">{{ $transaction->tracking_code }}</span></div>
                </div>
                <div>توضیحات: <span>{{ $transaction->description }}</span></div>
            </div>

        </div>
    </div>
    @empty
        <div class="items-center font-bold text-2xl">
            تراکنشی ثبت نشده
        </div>
    @endforelse
    {{ $transactions->links() }}


    @if($deleteModal)
        <div
            x-data="{ dangerModalIsOpen: @entangle('deleteModal') }"
        >
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
                        <div>آیا از حذف تراکنش اطمینان دارید؟</div>
                        مبلغ <span class="font-bold">{{ $selectedTransaction->amount > 0 ? number_format($selectedTransaction->amount) : number_format($selectedTransaction->amount * -1) }}</span> <span>{{ $selectedTransaction->type == 'credit' ? ' از حساب '.$selectedTransaction->account->name.' کسر می شود!' : ' به حساب '. $selectedTransaction->account->name.' باز میگردد!'}}</span>
                        <form wire:submit="cancelDeleting">
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-slate-600 rounded-lg hover:bg-slate-700">
                                لفو حذف
                            </button>
                            <button wire:click="deleteTransaction({{ $selectedTransaction->id }})" type="button" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800">
                                حذف
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endif



</div>
