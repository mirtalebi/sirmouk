<div class="border-2 rounded-2xl mx-2 py-4 items-center" style="[x-cloak] {display: none !important;}">
{{--    {{ dd($transactions) }}--}}
    @forelse($transactions as $transaction)
        <div class="my-3 mx-4 {{ $transaction->type == 'credit' ? 'bg-green-300 hover:bg-green-500' : 'bg-red-300 hover:bg-red-500' }} rounded-xl px-4 cursor-pointer"
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
{{--    <a href="#">--}}
{{--        <div class="my-2 mx-4 bg-red-400 rounded-2xl py-2 px-4">--}}
{{--            <div>--}}
{{--                <h2 class="font-bold text-xl">بانک صادرات - احسان</h2>--}}
{{--            </div>--}}
{{--            <div class="grid grid-cols-2 my-2 text-sm">--}}
{{--                <div class="mx-4">مبلغ: <span class="font-bold">125,000</span></div>--}}
{{--                <div>--}}
{{--                    <span class="font-bold">واریز وجه</span>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="grid grid-cols-2 my-2 text-sm mx-4">--}}
{{--                <div>موجودی فعلی:<span class="font-bold"> 120,000</span></div>--}}
{{--                <div>دسته: <span class="font-bold"> ساخت و ساز</span></div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </a>--}}
    @empty
        <div class="items-center font-bold text-2xl">
            تراکنشی ثبت نشده
        </div>
    @endforelse
    {{ $transactions->links() }}






</div>
