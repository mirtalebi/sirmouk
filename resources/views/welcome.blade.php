<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center justify-center min-h-screen flex-col">
    <header class="bg-white w-full">
        <div class="mx-auto flex h-16 max-w-screen-xl items-center gap-8 px-4 sm:px-6 lg:px-8">
            <a class="block text-teal-600" href="{{ route('home') }}">
                <div class="flex items-center justify-center mb-2">
                    <img src="/assets/logo/main.png" class="size-16">
                </div>
            </a>

            <div class="flex flex-1 items-center justify-end">

                <div class="flex items-center gap-4">
                    <div class="sm:gap-4">
                        <a
                            class="rounded-md bg-gray-100 px-5 py-2.5 text-sm font-medium transition"
                            href="{{ route('login') }}"
                        >
                            ورود
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <section class="bg-white relative overflow-hidden" x-data="{ isOpen: false }">
        <div class="w-full mx-auto 2xl:max-w-7xl flex flex-col justify-center py-24 relative p-8">
            <div class="prose text-gray-500 prose-sm prose-headings:font-normal prose-headings:text-xl mx-auto max-w-sm w-full">
                <div>
                    <h1>Cart</h1>
                    <p class="text-balance">Add items to your cart.</p>
                </div>
            </div>
            <div class="mt-6 border-t pt-12 max-w-sm mx-auto w-full">
                <!-- Starts component -->
                <div x-data="{
                    cart: [],
                    products: [
                    @forelse($products as $product)
                        { id: {{$product->id}}, name: '{{ $product->name }}', price: {{ $product->price }} },
                    @empty
                    @endforelse
                    ],
                    addToCart(product) {
                        let existingItem = this.cart.find(item => item.id === product.id);
                        if (existingItem) {
                            existingItem.quantity++;
                        } else {
                            this.cart.push({ ...product, quantity: 1 });
                        }
                    },
                    removeFromCart(index) {
                        this.cart.splice(index, 1);
                    },
                    increaseQuantity(index) {
                        this.cart[index].quantity++;
                    },
                    decreaseQuantity(index) {
                        if (this.cart[index].quantity > 1) {
                            this.cart[index].quantity--;
                        } else {
                            this.removeFromCart(index);
                        }
                    },
                    totalPrice() {
                        return this.cart.reduce((total, item) => total + item.price * item.quantity, 0);
                    }
                }">
                    <!-- Product List -->
                    <ul>
                        <template x-for="product in products" :key="product.id">
{{--                            <li class="border p-8 flex flex-col gap-4">--}}
{{--                                <div>--}}
{{--                                    <span x-text="product.name"></span> - <span x-text="'$' + product.price"></span>--}}
{{--                                </div>--}}
{{--                                <button @click="addToCart(product)" class="rounded-full bg-orange-50 px-8 py-2 h-12 text-sm font-semibold text-orange-600 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">Add to Cart</button>--}}
{{--                            </li>--}}
                            <article class="group flex rounded-radius max-w-sm flex-col overflow-hidden border border-outline bg-surface-alt text-on-surface dark:border-outline-dark dark:bg-surface-dark-alt dark:text-on-surface-dark">
                                <!-- Image -->
                                <div class="h-44 md:h-64 overflow-hidden">
                                    <img src="https://penguinui.s3.amazonaws.com/component-assets/card-img-3.webp" class="object-cover transition duration-700 ease-out group-hover:scale-105" alt="CASIO G-SHOCK GA2100, Black face, black bands" />
                                </div>
                                <!-- Content -->
                                <div class="flex flex-col gap-4 p-6">
                                    <!-- Header -->
                                    <div class="flex flex-col md:flex-row gap-4 md:gap-12 justify-between">
                                        <!-- Title & Rating -->
                                        <div class="flex flex-col">
                                            <h3 class="text-lg lg:text-xl font-bold text-on-surface-strong dark:text-on-surface-dark-strong" aria-describedby="productDescription">CASIO G-SHOCK GA2100</h3>
                                            <!-- Rating -->
                                            <div class="flex items-center gap-1">
                                                <span class="sr-only">Rated 3 stars</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 text-amber-500" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                                </svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 text-amber-500" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                                </svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 text-amber-500" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                                </svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 text-on-surface/50 dark:text-on-surface-dark/50" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                                </svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 text-on-surface/50 dark:text-on-surface-dark/50" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                        <span class="text-xl"><span class="sr-only">Price</span>$99.99</span>
                                    </div>
                                    <p id="productDescription" class="mb-2 text-pretty text-sm">
                                        The Casio G-Shock GA2100 is simply designed for easy
                                        timekeeping, featuring a sleek profile and clear display.
                                    </p>
                                    <!-- Button -->
                                    <button @click="addToCart(product)" type="button" class="flex items-center justify-center gap-2 whitespace-nowrap bg-primary px-4 py-2 text-center text-sm font-medium tracking-wide text-on-primary transition hover:opacity-75 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary active:opacity-100 active:outline-offset-0 dark:bg-primary-dark dark:text-on-primary-dark dark:focus-visible:outline-primary-dark rounded-radius">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" class="size-3.5">
                                            <path fill-rule="evenodd" d="M5 4a3 3 0 0 1 6 0v1h.643a1.5 1.5 0 0 1 1.492 1.35l.7 7A1.5 1.5 0 0 1 12.342 15H3.657a1.5 1.5 0 0 1-1.492-1.65l.7-7A1.5 1.5 0 0 1 4.357 5H5V4Zm4.5 0v1h-3V4a1.5 1.5 0 0 1 3 0Zm-3 3.75a.75.75 0 0 0-1.5 0v1a3 3 0 1 0 6 0v-1a.75.75 0 0 0-1.5 0v1a1.5 1.5 0 1 1-3 0v-1Z" clip-rule="evenodd" />
                                        </svg>
                                        Add to Cart
                                    </button>
                                    <template x-if="cart.find(item => item.id === product.id)" class="bg-red-500">
                                        <span>test</span>
                                        <span x-text="`${item.name} x${item.quantity}`"></span> - $<span x-text="item.price * item.quantity" class="mr-4"></span>
                                        <button @click="increaseQuantity(index)" class="px-3 py-1 bg-gray-100 rounded-md text-sm font-semibold text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">+</button>
                                        <button @click="decreaseQuantity(index)" class="px-3 py-1 bg-gray-100 rounded-md text-sm font-semibold text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">-</button>
                                        <button @click="removeFromCart(index)" class="px-3 py-1 bg-gray-200 rounded-md text-sm font-semibold text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">Remove</button>
                                    </template>
                                </div>
                            </article>
                        </template>
                    </ul>

                    <!-- Cart -->
                    <div class="prose text-gray-500 prose-sm prose-headings:font-normal mt-6 border-t pt-6 prose-headings:text-xl mx-auto max-w-sm w-full">
                        <h4>Your items</h4>
                        <ul>
                            <template x-for="(item, index) in cart" :key="index">
                                <li>
                                    <span x-text="`${item.name} x${item.quantity}`"></span> - $<span x-text="item.price * item.quantity" class="mr-4"></span>
                                    <button @click="increaseQuantity(index)" class="px-3 py-1 bg-gray-100 rounded-md text-sm font-semibold text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">+</button>
                                    <button @click="decreaseQuantity(index)" class="px-3 py-1 bg-gray-100 rounded-md text-sm font-semibold text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">-</button>
                                    <button @click="removeFromCart(index)" class="px-3 py-1 bg-gray-200 rounded-md text-sm font-semibold text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">Remove</button>
                                </li>
                            </template>
                        </ul>
                        <div>Total Price: $<span x-text="totalPrice()"></span></div>
                    </div>
                </div>

                <!-- Ends component -->
            </div>
            <!-- Starts links to tutorial -->
            <div class="pointer-events-none fixed inset-x-0 bottom-0 sm:flex sm:justify-center p-2">
                <div class="pointer-events-auto flex w-full max-w-md divide-x divide-neutral-200 rounded-lg bg-white shadow-xl border">
                    <div class="flex w-0 flex-1 items-center p-4">
                        <div class="w-full">
                            <p class="text-sm font-medium text-neutral-900">Tutorial</p>
                            <p class="mt-1 text-sm text-neutral-500">
                                How to add items to your cart with Tailwind CSS and Alpinejs
                            </p>
                            <p class="mt-2 text-xs text-orange-600 underline">
                                <a href="https://lexingtonthemes.com">
                                    by © Lexington Themes</a>
                            </p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex flex-col divide-y divide-neutral-200">
                            <div class="flex h-0 flex-1">
                                <a href="https://lexingtonthemes.com/tutorials/hhow-to-add-item-to-your-cart-with-tailwind-css-and-alpinejs/" type="button" class="flex w-full items-center justify-center rounded-none rounded-tr-lg border border-transparent px-4 py-3 text-sm font-medium text-orange-600 hover:text-orange-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-orange-600">Tutorial</a>
                            </div>
                            <div class="flex h-0 flex-1">
                                <a href="https://github.com/Lexington-Themes/lexington-tutorials/blob/main/src/pages/cart/index.astro" class="flex w-full items-center justify-center rounded-none rounded-br-lg border border-transparent px-4 py-3 text-sm font-medium text-neutral-700 hover:text-neutral-500 focus:outline-none focus:ring-2 focus:ring-orange-600">Get the code</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Ends links to tutorial -->
        </div>
    </section>
        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
