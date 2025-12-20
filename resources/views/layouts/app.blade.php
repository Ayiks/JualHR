<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false }" @keydown.escape="sidebarOpen = false">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HRMS') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        @include('components.nav')

        <!-- Page Content -->
        <div class="flex pt-16">
            <!-- Sidebar -->
            @include('components.sidebar')

            <!-- Main Content -->
            <main class="flex-1 lg:ml-64 min-h-screen">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <!-- Page Header -->
                    @hasSection('header')
                        <header class="mb-8">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                                        @yield('header')
                                    </h1>
                                    @hasSection('description')
                                        <p class="mt-1 text-sm text-gray-500">
                                            @yield('description')
                                        </p>
                                    @endif
                                </div>
                                @hasSection('header-actions')
                                    <div class="flex items-center gap-3">
                                        @yield('header-actions')
                                    </div>
                                @endif
                            </div>
                        </header>
                    @endif

                    <!-- Alert Messages -->
                    @include('components.alert')

                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>