<div class="p-4 space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">کارت دستور پخت</h1>
            <p class="text-sm text-gray-600">ایجاد و مدیریت آیتم‌های ایستا یا پویا با محاسبه هزینه تولید.</p>
        </div>
        <div class="inline-flex items-center gap-2">
            <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700">تعداد آیتم‌ها:
                {{ $recipeItems->count() }}</span>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1.3fr_0.9fr]">
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">آیتم‌های دستور پخت</h2>
                    <span class="text-sm text-gray-500">به‌روزرسانی زنده</span>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <label class="text-sm font-medium text-gray-700">نمایش بر اساس نوع:</label>
                    <select wire:model.live="filterType"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                        <option value="all">همه</option>
                        <option value="static">ایستا</option>
                        <option value="dynamic">پویا</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-gray-700">
                    <thead class="border-b border-gray-200 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-2">نام</th>
                            <th class="px-3 py-2">نوع</th>
                            <th class="px-3 py-2">ارزش پایه</th>
                            <th class="px-3 py-2">هزینه تولید</th>
                            <th class="px-3 py-2">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recipeItems as $item)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-3 py-3 font-medium text-gray-900">{{ $item->name }}</td>
                                <td class="px-3 py-3 uppercase text-gray-500">
                                    {{ $item->value_type === 'static' ? 'ایستا' : 'پویا' }}</td>
                                <td class="px-3 py-3 text-gray-700">{{ number_format($item->value, 0) }}</td>
                                <td class="px-3 py-3 text-gray-900 font-semibold">
                                    {{ number_format($item->calculateValue(), 0) }}</td>
                                <td class="px-3 py-3 space-x-2">
                                    <button wire:click="editRecipeItem({{ $item->id }})"
                                        class="rounded-md bg-sky-600 px-3 py-2 text-sm text-white hover:bg-sky-700">ویرایش</button>
                                    <button wire:click="deleteRecipeItem({{ $item->id }})"
                                        onclick="return confirm('آیا از حذف این آیتم دستور پخت مطمئن هستید؟')"
                                        class="rounded-md bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700">حذف</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500">آیتمی برای دستور
                                    پخت پیدا نشد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ $recipeItemId ? 'ویرایش' : 'افزودن' }} آیتم دستور
                پخت</h2>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">نام</label>
                    <input type="text" wire:model.defer="name"
                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                        placeholder="نام آیتم را وارد کنید">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">نوع مقدار</label>
                    <select wire:model.live="valueType"
                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                        <option value="static">ایستا</option>
                        <option value="dynamic">پویا</option>
                    </select>
                    @error('valueType')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if ($valueType === 'static')
                    <div x-data="{
                        formatted: '{{ number_format($value, 0) }}',
                        formatNumber(value) {
                            let raw = value.replace(/\D/g, '');
                            this.formatted = raw.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                            $wire.set('value', raw);
                        },
                        init() { this.formatNumber(this.formatted) }
                    }" x-init="init()">
                        <label class="mb-2 block text-sm font-medium text-gray-700">مقدار</label>
                        <input type="text" x-model="formatted" @input="formatNumber($event.target.value)"
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            placeholder="مقدار آیتم را وارد کنید">
                        @error('value')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                @if ($valueType === 'dynamic')
                    <div class="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700">اجزای پویا</p>
                                <p class="text-xs text-gray-500">آیتم‌ها و مقادیری که هزینه این دستور را تشکیل می‌دهند
                                    اضافه کنید.</p>
                            </div>
                            <button type="button" wire:click.prevent="addComponent"
                                class="inline-flex items-center rounded-md bg-sky-600 px-3 py-2 text-sm text-white hover:bg-sky-700">افزودن
                                جز</button>
                        </div>

                        @if ($errors->has('components'))
                            <p class="text-xs text-red-600">{{ $errors->first('components') }}</p>
                        @endif

                        <div class="space-y-3">
                            @foreach ($components as $index => $component)
                                <div wire:key="component-row-{{ $index }}"
                                    class="grid gap-3 sm:grid-cols-[2fr_1fr_auto]">
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-gray-700">آیتم جزء</label>
                                        <select wire:model="components.{{ $index }}.item_id"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                            <option value="">انتخاب آیتم</option>
                                            @foreach ($availableComponentItems as $availableItem)
                                                <option value="{{ $availableItem->id }}">{{ $availableItem->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('components.' . $index . '.item_id')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-gray-700">تعداد</label>
                                        <input type="text" x-data="{ formatted: '{{ isset($component['quantity']) ? number_format($component['quantity'], 2) : '' }}', formatNumber(value) { let raw = value.replace(/,/g, '');
                                                raw = raw.replace(/[^\d\.]/g, ''); let parts = raw.split('.'); if (parts.length > 2) { raw = parts[0] + '.' + parts.slice(1).join('');
                                                    parts = raw.split('.'); } const integer = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ','); const decimal = parts[1] ?? '';
                                                this.formatted = decimal === '' ? integer : integer + '.' + decimal;
                                                $wire.set('components.{{ $index }}.quantity', raw); }, init() { this.formatNumber(this.formatted) } }" x-init="init()"
                                            x-model="formatted" @input="formatNumber($event.target.value)"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                        @error('components.' . $index . '.quantity')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button"
                                            wire:click.prevent="removeComponent({{ $index }})"
                                            class="rounded-md bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700">حذف</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                    <div class="flex items-center justify-between">
                        <span>هزینه تولید محاسبه شده</span>
                        <span class="font-semibold text-gray-900">{{ number_format($currentTotal, 2) }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit"
                        class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">ذخیره
                        آیتم</button>
                    <button type="button" wire:click.prevent="resetForm"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">پاک
                        کردن فرم</button>
                </div>
            </form>
        </section>
    </div>
</div>
