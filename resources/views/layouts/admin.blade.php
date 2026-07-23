<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body,
        .font-sans {
            font-family: 'Poppins', sans-serif !important;
        }

        /* Custom Scrollbar for sleekness */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<!-- Alpine state: sidebarOpen (mobile), sidebarCollapsed (desktop) -->

<body class="font-sans antialiased text-slate-800 bg-slate-50"
    x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' }"
    x-init="$watch('sidebarCollapsed', val => localStorage.setItem('sidebarCollapsed', val))">
    <div class="flex h-screen overflow-hidden">

        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-cloak x-transition.opacity class="fixed inset-0 z-20 bg-slate-900/50 lg:hidden"
            @click="sidebarOpen = false"></div>

        <!-- Premium White Sidebar (Collapsible) -->
        <aside :class="[
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                    sidebarCollapsed ? 'lg:w-[80px]' : 'lg:w-[260px]'
                ]"
            class="fixed inset-y-0 left-0 z-30 flex flex-col bg-white border-r border-slate-200 transition-all duration-300 ease-in-out lg:static lg:translate-x-0 w-[260px] shadow-[4px_0_24px_rgba(0,0,0,0.02)]">

            <!-- Logo Area -->
            <div class="flex items-center justify-between h-20 px-4 border-b border-slate-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 object-contain flex-shrink-0">
                    <span x-show="!sidebarCollapsed" x-transition.opacity.duration.300ms
                        class="text-xl font-bold tracking-tight text-slate-800">
                        Geo<span class="text-indigo-600">Admin</span>
                    </span>
                </a>

                <!-- Mobile close btn -->
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-6 space-y-2 overflow-y-auto overflow-x-hidden">
                <p x-show="!sidebarCollapsed" x-transition.opacity
                    class="px-3 text-[10px] font-bold tracking-widest text-slate-400 uppercase mb-4">Main Menu</p>

                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3.5 px-3 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800 font-medium' }}"
                    title="Dashboard">
                    <div class="flex-shrink-0 flex items-center justify-center w-6 h-6">
                        <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                    </div>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap text-sm">Dashboard</span>
                </a>

                <a href="{{ route('layers.index') }}"
                    class="flex items-center gap-3.5 px-3 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('layers.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800 font-medium' }}"
                    title="Kelola Kategori Layer">
                    <div class="flex-shrink-0 flex items-center justify-center w-6 h-6">
                        <svg class="w-5 h-5 {{ request()->routeIs('layers.*') ? 'text-indigo-600' : 'text-slate-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap text-sm">Kelola Layer</span>
                </a>

                <a href="{{ route('peta.index') }}"
                    class="flex items-center gap-3.5 px-3 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('peta.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800 font-medium' }}"
                    title="Kelola Item Peta">
                    <div class="flex-shrink-0 flex items-center justify-center w-6 h-6">
                        <svg class="w-5 h-5 {{ request()->routeIs('peta.*') ? 'text-indigo-600' : 'text-slate-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                            </path>
                        </svg>
                    </div>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap text-sm">Kelola Item Peta</span>
                </a>
            </nav>

            <!-- User Profile & Logout -->
            <div class="p-3 border-t border-slate-100">
                <div class="flex items-center gap-3 p-2 mb-3 rounded-xl hover:bg-slate-50 cursor-pointer overflow-hidden whitespace-nowrap"
                    :class="sidebarCollapsed ? 'justify-center' : ''">
                    <div
                        class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-600 flex-shrink-0">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] font-medium text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-3.5 px-3 py-2.5 text-red-600 hover:bg-red-50 hover:text-red-700 rounded-xl transition-all duration-200 font-medium w-full"
                        :class="sidebarCollapsed ? 'justify-center' : ''" title="Log Out">
                        <div class="flex-shrink-0 flex items-center justify-center w-6 h-6">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap text-sm">Log Out Sesi</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 overflow-hidden h-full">

            <!-- Top Header -->
            <header
                class="flex items-center justify-between px-6 py-4 bg-white border-b border-slate-200 sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <!-- Mobile Hamburger -->
                    <button @click="sidebarOpen = true"
                        class="lg:hidden text-slate-500 hover:text-indigo-600 focus:outline-none transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Desktop Collapse Toggle -->
                    <button @click="sidebarCollapsed = !sidebarCollapsed"
                        class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-500 transition-colors">
                        <svg x-show="!sidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                        </svg>
                        <svg x-show="sidebarCollapsed" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <span class="text-lg font-bold text-slate-800 tracking-tight lg:hidden">GeoAdmin</span>
                </div>
            </header>

            @isset($header)
                <!-- Page Heading -->
                <div class="bg-white border-b border-slate-100 hidden lg:block">
                    <div class="px-8 py-5">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <!-- Main scrollable content -->
            <main class="flex-1 overflow-y-auto overflow-x-hidden relative bg-slate-50/50">
                <div class="w-full h-full lg:px-8 px-4 py-6">
                    {{ $slot }}
                </div>
            </main>
        </div>

    </div>

    @stack('scripts')
</body>

</html>