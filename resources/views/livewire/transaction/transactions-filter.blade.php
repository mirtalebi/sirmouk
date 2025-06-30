
<div>
    <form wire:submit="submit" >
    <div class="flex">
        <div class="my-2 mx-4">
            <label class="block text-sm mb-2 dark:text-white font-bold" for="datepicker">از تاریخ:</label>
            <input
                type="text"
                data-jdp
                class=" border-2 rounded p-2"
                placeholder="تاریخ شمسی"
                wire:model.defer="from_date" />
{{--            @error('transaction_date_jalali')--}}
{{--            <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>--}}
{{--            @enderror--}}
        </div>
        <div class="my-2 mx-4">
            <label class="block text-sm mb-2 dark:text-white font-bold" for="datepicker">تا تاریخ:</label>
            <input
                type="text"
                data-jdp
                class=" border-2 rounded p-2"
                placeholder="تا تاریخ"
                wire:model.defer="to_date" />
{{--            @error('transaction_date_jalali')--}}
{{--            <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>--}}
{{--            @enderror--}}
        </div>
    </div>
        <div class="my-2 mx-4">
            <button type="submit" class="py-2 px-3 rounded bg-green-500">Submit</button>
        </div>
    </form>

    <livewire:transaction.transaction-list />



    @push('scripts')
        <script type="text/javascript" src="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js"></script>


        <script>
            jalaliDatepicker.startWatch();
        </script>
    @endpush
</div>
