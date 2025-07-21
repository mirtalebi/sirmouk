<!DOCTYPE html>
<html dir="rtl" data-theme="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">

    <div>
        <div x-data="{ showSidebar: false }" class="relative flex w-full flex-col md:flex-row">
            <!-- This allows screen readers to skip the sidebar and go directly to the main content. -->
            <a class="sr-only" href="#main-content">skip to the main content</a>

            <!-- dark overlay for when the sidebar is open on smaller screens  -->
            <div x-cloak x-show="showSidebar" class="fixed inset-0 z-10 bg-surface-dark/10 backdrop-blur-xs md:hidden" aria-hidden="true" x-on:click="showSidebar = false" x-transition.opacity ></div>

            <nav x-cloak class="fixed left-0 z-20 flex h-svh w-60 shrink-0 flex-col border-r border-outline bg-surface-alt p-4 transition-transform duration-300 md:w-64 md:translate-x-0 md:relative dark:border-outline-dark dark:bg-surface-dark-alt border-l-1 pt-20" x-bind:class="showSidebar ? 'translate-x-0' : '-translate-x-60'" aria-label="sidebar navigation">
                <!-- sidebar links  -->
                <div class="flex flex-col gap-2 overflow-y-auto pb-6">

                    <a href="{{ route('dashboard') }}" class="hover:-translate-y-1 transition flex items-center rounded-radius gap-2 p-2 text-sm text-on-surface underline-offset-2 hover:bg-primary/5 hover:text-on-surface-strong focus-visible:underline focus:outline-hidden {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-on-surface-strong font-bold' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-6">
                            <path fill="currentColor" d="M13 8V4q0-.425.288-.712T14 3h6q.425 0 .713.288T21 4v4q0 .425-.288.713T20 9h-6q-.425 0-.712-.288T13 8M3 12V4q0-.425.288-.712T4 3h6q.425 0 .713.288T11 4v8q0 .425-.288.713T10 13H4q-.425 0-.712-.288T3 12m10 8v-8q0-.425.288-.712T14 11h6q.425 0 .713.288T21 12v8q0 .425-.288.713T20 21h-6q-.425 0-.712-.288T13 20M3 20v-4q0-.425.288-.712T4 15h6q.425 0 .713.288T11 16v4q0 .425-.288.713T10 21H4q-.425 0-.712-.288T3 20m2-9h4V5H5zm10 8h4v-6h-4zm0-12h4V5h-4zM5 19h4v-2H5zm4-2"/>
                        </svg>
                        <span>داشبورد</span>
                    </a>

                    <a href="{{ route('order') }}" class="hover:-translate-y-1 transition p-2 flex items-center rounded-radius gap-2 text-sm text-on-surface underline-offset-2 focus-visible:underline hover:bg-primary/5 hover:text-on-surface-strong focus:outline-hidden dark:bg-primary-dark/10 dark:text-on-surface-dark-strong {{ request()->routeIs('order') ? 'bg-primary/10 text-on-surface-strong font-bold' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-6">
                            <g fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="17" x="5" y="4" rx="2"/><path stroke-linecap="round" d="M9 9h6m-6 4h6m-6 4h4"/>
                            </g></svg>
                        <span>سفارش گیری</span>
                        <span class="sr-only">active</span>
                    </a>


                    <a href="{{ route('accounts.index') }}" class="hover:-translate-y-1 transition flex items-center rounded-radius gap-2 p-2 text-sm text-on-surface underline-offset-2 hover:bg-primary/5 hover:text-on-surface-strong focus-visible:underline focus:outline-hidden dark:text-on-surface-dark dark:hover:bg-primary-dark/5 dark:hover:text-on-surface-dark-strong {{ request()->routeIs('accounts.index') ? 'bg-primary/10 text-on-surface-strong font-bold' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-6">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 8.57c0-1.197.482-1.93 1.48-2.486l4.11-2.287C9.743 2.6 10.82 2 12 2s2.257.6 4.41 1.797l4.11 2.287C21.517 6.64 22 7.373 22 8.57c0 .324 0 .487-.035.62c-.186.7-.821.811-1.434.811H3.469c-.613 0-1.247-.11-1.434-.811C2 9.056 2 8.893 2 8.569M11.996 7h.009M4 10v8.5M8 10v8.5m8-8.5v8.5m4-8.5v8.5m-1 0H5a3 3 0 0 0-3 3a.5.5 0 0 0 .5.5h19a.5.5 0 0 0 .5-.5a3 3 0 0 0-3-3"/>
                        </svg>
                        <span>حساب ها</span>
                    </a>

                    <a href="{{ route('transactions.index') }}" class="hover:-translate-y-1 transition flex items-center rounded-radius gap-2 p-2 text-sm text-on-surface underline-offset-2 hover:bg-primary/5 hover:text-on-surface-strong focus-visible:underline focus:outline-hidden dark:text-on-surface-dark dark:hover:bg-primary-dark/5 dark:hover:text-on-surface-dark-strong {{ request()->routeIs('transactions.index') ? 'bg-primary/10 text-on-surface-strong font-bold' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-6">
                            <path fill="currentColor" d="M20 2H10a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V5a3 3 0 0 0-3-3m1 10a1 1 0 0 1-1 1H10a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1Zm-3.5-4a1.5 1.5 0 0 0-1 .39a1.5 1.5 0 1 0 0 2.22a1.5 1.5 0 1 0 1-2.61M16 17a1 1 0 0 0-1 1v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-4h1a1 1 0 0 0 0-2H3v-1a1 1 0 0 1 1-1a1 1 0 0 0 0-2a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1a1 1 0 0 0-1-1M6 18h1a1 1 0 0 0 0-2H6a1 1 0 0 0 0 2"/>
                        </svg>
                        <span>تراکنش ها</span>
                    </a>
                </div>
            </nav>

            <!-- main content  -->
            <div id="main-content" class="h-svh w-full overflow-y-auto p-4 bg-surface dark:bg-surface-dark">
                {{ $slot }}
            </div>

            <!-- toggle button for small screen  -->
            <button class="fixed right-4 top-4 z-20 rounded-full bg-primary p-4 md:hidden text-on-primary dark:bg-primary-dark dark:text-on-primary-dark" x-on:click="showSidebar = ! showSidebar">
                <svg x-show="showSidebar" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-5" aria-hidden="true">
                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                </svg>
                <svg x-show="! showSidebar" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-5" aria-hidden="true">
                    <path d="M0 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm5-1v12h9a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1zM4 2H2a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h2z"/>
                </svg>
                <span class="sr-only">sidebar toggle</span>
            </button>
        </div>

    </div>


        @fluxScripts
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- Toastr JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


        <script type="text/javascript" src="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js"></script>
        <script>
            jalaliDatepicker.startWatch();
        </script>

    </body>
</html>
