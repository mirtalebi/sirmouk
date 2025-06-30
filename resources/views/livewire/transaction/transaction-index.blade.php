<div>
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
    <div class="grid grid-cols-2">
{{--      Add TransActions                   --}}
        <livewire:transaction.transaction-create />
{{--     TransActions List           --}}
{{--        {{ dd($transactions) }}--}}
{{--        {{ dd($transactions) }}--}}
{{--        @foreach($transactions as $transaction)--}}
{{--            {{ $transaction->amount }}--}}
{{--        @endforeach--}}
        <livewire:transaction.transaction-list />
    </div>
</div>
