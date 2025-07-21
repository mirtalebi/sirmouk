
<div class="mt-8">
    <h2 class="font-bold text-2xl mb-4">جمع تراکنش</h2>
    <form wire:submit="submit" >
    <div class="grid grid-cols-2 sm:flex sm:items-end">
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
        <div class="my-2 mx-4">
            <button type="submit" class="py-2 px-3 rounded bg-green-500 hover:bg-green-600 text-white">ادامه</button>
        </div>
    </div>
    </form>



    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    دسته بندی
                </th>
                <th scope="col" class="px-6 py-3">
                    مبلغ
            </tr>
            </thead>
            <tbody>
            @forelse($summaries  as $summary)
                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $summary['name'] }}
                    </th>
                    <td class="px-6 py-4 font-bold {{$summary['total'] > 0 ? 'text-green-600' : 'text-red-500'}}">
                        {{ number_format($summary['total']) }}
                    </td>
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>
    </div>

    @push('scripts')
        <script type="text/javascript" src="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js"></script>


        <script>
            jalaliDatepicker.startWatch();
        </script>
    @endpush




</div>
