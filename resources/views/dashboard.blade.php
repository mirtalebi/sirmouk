<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <a href="{{ route('transactions.filterByDate') }}" class="flex justify-center border rounded-xl py-2 bg-slate-50 hover:bg-slate-100 transition">
                جمع تراکنش ها
            </a>
            <a href="{{ route('invoice.calc') }}" class="flex justify-center border rounded-xl py-2 bg-slate-50 hover:bg-slate-100 transition">
                جمع فاکتور ها
            </a>
            <a href="{{ route('products.sell') }}" class="flex justify-center border rounded-xl py-2 bg-slate-50 hover:bg-slate-100 transition">
                محصولات فروخته شده
            </a>
        </div>

        <div class="flex-1 border border-neutral-200 dark:border-neutral-700 rounded-xl">
            <div class="px-4">
                <livewire:invoice.invoices-chart />
            </div>
        </div>

        <div class="flex-1 border border-neutral-200 dark:border-neutral-700 rounded-xl">
            <div class="mt-4 p-2">
                <h2 class="text-3xl font-bold mt-4 mx-4">حساب ها:</h2>
                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-2">
                    @forelse(\App\Models\Account::all() as $account)
                        <a href="#">
                            <div class="bg-slate-200 hover:bg-slate-300 transition hover:-translate-1 border h-40 my-4 mx-2 rounded-2xl">
                                <div class="bg-black h-8 my-4"></div>
                                <div class="flex text-xl font-bold px-4 mb-2">
                                    <span>{{ $account->name }}</span>
                                </div>
                                <span class="mx-6">موجودی: <span class="font-bold">{{ number_format($account->balance) }}</span></span>
                            </div>
                        </a>
                    @empty
                        حساب بانکی یافت نشد!
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
