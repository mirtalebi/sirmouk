<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold">لیست خرید</h2>
            <p class="text-sm text-gray-500">موارد خود را اضافه کنید و وضعیت انجام شدن را ثبت کنید.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="createItem" class="grid gap-4">
        <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
            <div class="grid gap-3">
                <label class="block text-sm font-medium text-gray-700">شرح آیتم</label>
                <input type="text" wire:model.defer="description"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    placeholder="مثلاً: نان، شیر، تخم مرغ" />
                @error('description')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-3">
                <label class="block text-sm font-medium text-gray-700">اولویت</label>
                <select wire:model.defer="priority"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="low">کم</option>
                    <option value="medium">متوسط</option>
                    <option value="high">بالا</option>
                </select>
                @error('priority')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    اضافه کردن آیتم
                </button>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model.live="showDone"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                نمایش موارد انجام شده
            </label>
        </div>
    </form>

    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        @php
            $priorityLabels = [
                'high' => 'بالا',
                'medium' => 'متوسط',
                'low' => 'کم',
            ];
            $totalItems = $itemsByPriority->flatten()->count();
        @endphp

        <div class="mb-4 flex items-center justify-between gap-4">
            <h3 class="text-lg font-semibold">آیتم‌های لیست</h3>
            <span class="text-sm text-gray-500">{{ $totalItems }} مورد</span>
        </div>

        @if ($totalItems === 0)
            <div
                class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-sm text-gray-500">
                هنوز هیچ آیتمی اضافه نشده است.
            </div>
        @else
            <div class="space-y-6">
                @foreach (['high', 'medium', 'low'] as $priorityKey)
                    @php
                        $sectionItems = $itemsByPriority->get($priorityKey, collect());
                    @endphp

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="mb-3 flex items-center justify-between gap-4">
                            <div>
                                <h4 class="text-base font-semibold">اولویت {{ $priorityLabels[$priorityKey] }}</h4>
                                <p class="text-sm text-gray-500">{{ $sectionItems->count() }} مورد</p>
                            </div>
                        </div>

                        @if ($sectionItems->isEmpty())
                            <div
                                class="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-center text-sm text-gray-500">
                                هیچ آیتمی با اولویت {{ $priorityLabels[$priorityKey] }} وجود ندارد.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($sectionItems as $item)
                                    <div
                                        class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-blue-300">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="space-y-2">
                                                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                                                    <span>نویسنده:
                                                        {{ $item->user?->name ?? auth()->user()->name }}</span>
                                                    <span>تاریخ: {{ $item->created_at->format('Y-m-d H:i') }}</span>
                                                </div>
                                                <p
                                                    class="text-base font-medium {{ $item->done ? 'line-through text-gray-500' : 'text-gray-900' }}">
                                                    {{ $item->description }}
                                                </p>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2 text-sm">
                                                <span class="rounded-full bg-gray-100 px-3 py-1 text-gray-700">
                                                    اولویت: {{ $item->priority_label }}
                                                </span>
                                                <span
                                                    class="rounded-full {{ $item->done ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} px-3 py-1">
                                                    {{ $item->done ? 'انجام شده' : 'در انتظار' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <button wire:click="toggleDone({{ $item->id }})" type="button"
                                                class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                                {{ $item->done ? 'بازگرداندن' : 'علامت‌گذاری انجام شده' }}
                                            </button>

                                            <button wire:click="deleteItem({{ $item->id }})" type="button"
                                                class="rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-200">
                                                حذف
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
