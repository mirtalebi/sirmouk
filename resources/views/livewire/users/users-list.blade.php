<div>


    <div class="relative overflow-x-auto sm:rounded-lg">
        <h2 class="font-bold text-2xl">لیست کاربران:</h2>
    <div>
        <div class="flex px-4 py-3 rounded-md border-2 overflow-hidden max-w-md mx-auto">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192.904 192.904" width="16px"
                 class="fill-gray-600 mr-3 rotate-90">
                <path
                    d="m190.707 180.101-47.078-47.077c11.702-14.072 18.752-32.142 18.752-51.831C162.381 36.423 125.959 0 81.191 0 36.422 0 0 36.423 0 81.193c0 44.767 36.422 81.187 81.191 81.187 19.688 0 37.759-7.049 51.831-18.751l47.079 47.078a7.474 7.474 0 0 0 5.303 2.197 7.498 7.498 0 0 0 5.303-12.803zM15 81.193C15 44.694 44.693 15 81.191 15c36.497 0 66.189 29.694 66.189 66.193 0 36.496-29.692 66.187-66.189 66.187C44.693 147.38 15 117.689 15 81.193z">
                </path>
            </svg>
            <input wire:model.live.debounce.500ms="search" type="text" placeholder="نام یا شماره موبایل کاربر را جستجو کنید..." class="w-full outline-none bg-transparent text-gray-600 text-sm mr-2" />
        </div>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 mt-2">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    نام
                </th>
                <th scope="col" class="px-6 py-3">
                    شماره موبایل
                </th>
                <th scope="col" class="px-6 py-3">
                    عملیات
                </th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $user->name }}
                    </th>
                    <td class="px-6 py-4 font-bold text-black">
                        {{ $user->mobile }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('users.show', $user->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">مشاهده فاکتور ها</a>
                    </td>
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>
        <div class="flex justify-center">{{ $users->links() }}</div>
    </div>
    </div>

</div>
