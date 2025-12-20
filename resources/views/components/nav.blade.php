<nav class="bg-white border-b border-gray-200 shadow-sm fixed top-0 left-0 right-0 z-40">
    <div class="px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Left side: Logo & Menu button -->
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" type="button" 
                    class="lg:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Logo -->
                <a href="{{ route(Auth::user()->hasRole(['super_admin', 'hr_admin']) ? 'admin.dashboard' : (Auth::user()->hasRole('line_manager') ? 'manager.dashboard' : 'employee.dashboard')) }}" 
                    class="ml-2 lg:ml-0 flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="hidden lg:block">
                        <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">HRMS</span>
                        <span class="block text-xs text-gray-500 font-medium">Human Resources</span>
                    </div>
                </a>
            </div>

            <!-- Right side: Search & User Menu -->
            <div class="flex items-center space-x-3">
                <!-- Search (Desktop) -->
                <div class="hidden lg:block relative">
                    <div class="relative">
                        <input type="search" 
                            class="pl-10 pr-4 py-2 w-64 border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all text-sm"
                            placeholder="Search employees, documents...">
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- User Menu -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" type="button"
                        class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                        <!-- Avatar -->
                        <div class="relative">
                            @if(Auth::user()->employee && Auth::user()->employee->profile_photo)
                                <img class="w-8 h-8 rounded-lg object-cover" 
                                    src="{{ Storage::url(Auth::user()->employee->profile_photo) }}" 
                                    alt="{{ Auth::user()->employee->full_name }}">
                            @else
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold">
                                    {{ Auth::user()->employee ? Auth::user()->employee->initials : substr(Auth::user()->email, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="hidden lg:block text-left">
                            <p class="text-sm font-medium text-gray-900">
                                {{ Auth::user()->employee ? Auth::user()->employee->full_name : 'User' }}
                            </p>
                            <p class="text-xs text-gray-500 capitalize">
                                @foreach(Auth::user()->roles as $role)
                                    {{ $role->name }}@if(!$loop->last), @endif
                                @endforeach
                            </p>
                        </div>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" @click.away="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-2 z-50">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ Auth::user()->employee ? Auth::user()->employee->full_name : 'User' }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                        
                        <div class="py-2">
                            <a href="{{ route('employee.profile.show') }}" 
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                My Profile
                            </a>
                        </div>
                        
                        <div class="border-t border-gray-100 pt-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>