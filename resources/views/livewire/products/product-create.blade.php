<div>
    <script src="https://cdn.jsdelivr.net/gh/mahmoud-eskandari/NumToPersian/dist/num2persian.min.js"></script>

{{--   name / description / price / tax / category_id   --}}

    <section class="bg-white dark:bg-gray-900">
        <div class="py-8 px-4 mx-auto max-w-2xl lg:py-16">
            <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">اضافه کردن محصول</h2>
            <form wire:submit="save">
                <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">نام محصول</label>
                        <input type="text" wire:model="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="نام محصول را وارد کنید" required="">
                        @error('name')
                        <div class="text-sm text-red-500">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div>
                        <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">دسته بندی</label>
                        <select id="category" wire:model="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected></option>
                            @forelse($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @empty
                            @endforelse

                        </select>
                        @error('category')
                        <div class="text-sm text-red-500">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="w-full"
                        x-data="{
                            realValue: '',
                            formatted: '',

                            formatNumber(value) {
                                let raw = value.replace(/,/g, '');
                                if (isNaN(raw)) return '';

                                this.formatted = this.realValue;

                                this.realValue = raw;
                                this.formatted = Number(raw).toLocaleString();
                                $wire.price = raw;
                            }

                        }"
                    >
                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">مبلغ</label>
                        <input
                            type="text"
                            x-model="formatted"
                            @input="formatNumber($event.target.value)"
                            name="price" id="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="مبلغ محصول را وارد کنید" required="">
                        <p x-text="num2persian(formatted) + ' تومان'" class="text-xs text-green-700 font-bold mt-1"></p>
                        @error('price')
                        <div class="text-sm text-red-500">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="w-full">
                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">مالیات</label>
                        <input type="number" wire:model="tax" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="درصد مالیات را وارد کنید" required="">
                        @error('tax')
                        <div class="text-sm text-red-500">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">توضحیات</label>
                        <textarea wire:model="description" rows="8" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="توضیحات محصول را وارد کنید"></textarea>
                        @error('description')
                        <div class="text-sm text-red-500">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-green-700 rounded-lg hover:bg-green-800">
                    اضافه کردن محصول
                </button>
            </form>
        </div>
    </section></div>
