<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('js/notification-constants.js') }}"></script>
    <script src="{{ asset('js/notifications.js') }}"></script>
    <link href="{{ asset('css/notifications.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900" x-data="{ isNotificationsPanelOpen: false }">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-30 bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-4 py-3 lg:px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <img src="{{ asset('asset/logo.png') }}" alt="SmartCab Logo" class="h-8 w-8 lg:h-10 lg:w-10 mr-2">
                    <div class="flex flex-col">
                        <span class="text-base lg:text-xl font-semibold dark:text-white">SMARTCAB</span>
                        <span class="text-xs lg:text-sm font-medium dark:text-gray-400">Smart Cabin Security & Monitoring</span>
                    </div>
                </div>
                
                <!-- Mobile Right Menu -->
                <div class="flex items-center gap-2 lg:hidden">
                    {{-- <a href="{{route('ai.chat')}}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                        AI
                    </a> --}}
                    <!-- History Button Mobile (Changed from Notification) -->
                    <a href="{{route('reports')}}" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </a>
                    <a href="{{ route('view.cards') }}" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h6m2 0h6m-16 4h16a2 2 0 002-2V8a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </a>
                    

                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuBtn" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center gap-4">
                    <!-- Menu Items -->
                    {{-- <a href="{{route('ai.chat')}}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                        AI
                    </a> --}}
                    
                    
                    <!-- History Button (Changed from Notification) -->
                    <a href="{{route('reports')}}" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </a>
                    <a href="{{ route('view.cards') }}" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h6m2 0h6m-16 4h16a2 2 0 002-2V8a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </a>

                    <!-- Dark Mode Toggle -->
                    <button id="darkModeToggle" class="p-2 text-gray-500 dark:text-gray-400">
                        <svg class="w-6 h-6 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <svg class="w-6 h-6 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button class="flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                            <img class="w-8 h-8 rounded-full" src="{{ asset('asset/foto.jpg') }}" alt="profile">
                            <span class="ml-2 hidden lg:block"></span>
                        </button>
                        <div class="absolute right-0 hidden mt-2 w-48 bg-white rounded-md shadow-lg dark:bg-gray-700" id="profileMenu">
                            <div class="px-4 py-3 border-b dark:border-gray-600">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Hi!Vicky</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">vickynando12@gmail.com</p>
                            </div>
                            <a href="{{route('profile')}}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">Your Profile</a>
                            <a href="{{route('logout')}}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">Logout</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden lg:hidden mt-4 space-y-4">
                <!-- Profile Section Mobile -->
                <div class="border-b pb-4 dark:border-gray-700">
                    <div class="flex items-center space-x-3 px-4">
                        <img class="w-10 h-10 rounded-full" src="{{ asset('asset/foto.jpg') }}" alt="profile">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Hi!Vicky</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">vickynando12@gmail.com</p>
                        </div>
                    </div>
                    <div class="mt-4 space-y-2">
                        <a href="{{route('profile')}}" class="block px-4 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">Your Profile</a>
                        <a href="{{route('logout')}}" class="block px-4 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">Logout</a>
                    </div>
                </div>

                <div class="flex flex-col space-y-4 px-4">
                    <a href="#" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">Dashboard</a>
                    {{-- <a href="#" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">Chat</a> --}}
                    <!-- Dark Mode Toggle Mobile -->
                    <button id="mobileDarkModeToggle" class="flex items-center justify-between w-full text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                        <span>Dark Mode</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-20 p-4 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto transition-colors duration-200">
            <!-- Info Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Temperature Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="p-2 sm:p-3 mr-3 sm:mr-4 bg-red-100 rounded-full">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Temperature</p>
                            <p class="text-base sm:text-lg font-semibold text-gray-700 dark:text-gray-200" id="temp-value">{{ $temperature }}°C</p>
                        </div>
                    </div>
                </div>

                <!-- Wemos D1 Mini Status -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-amber-100 rounded-full">
                            <img src="{{ asset('asset/wemos.png') }}" class="w-6 h-6 sm:w-10 sm:h-10" alt="">
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Wemos D1 Mini</p>
                            <p class="text-sm sm:text-lg font-semibold" id="wemos-status">
                                {{ $systemWemos == 'Device Online' ? 'Online' : 'Offline' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- NodeMCU ESP8266 Status -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-amber-100 rounded-full">
                            <img src="{{ asset('asset/esp.png') }}" class="w-6 h-6 sm:w-10 sm:h-10" alt="">
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">NodeMCU ESP8266</p>
                            <p class="text-sm sm:text-lg font-semibold" id="esp-status">
                                {{ $systemESP == 'Device online' ? 'Online' : 'Offline' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Security Status -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-green-100 rounded-full">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 00-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Security Status</p>
                            <p class="text-sm sm:text-lg font-semibold" id="security-status">
                                {{ $status }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile-specific order for remaining sections -->
            <div class="md:hidden mt-4">
                <!-- Door Status Section for Mobile -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6 mb-4">
                    <div class="flex items-center mb-6">
                        <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-green-100 rounded-full">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Door Status</p>
                            <p class="text-sm sm:text-lg font-semibold" id="door-status">
                                {{ $servo_status }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Akses Terakhir</p>
                            <p class="text-sm sm:text-lg font-semibold text-green-500" id="last-access">{{ $last_access }}</p>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Card ID terdeteksi</p>
                            <p class="text-sm sm:text-lg font-semibold text-green-500" id="status-device">{{ $status_device }}</p>
                        </div>
                    </div>
                </div>

                <!-- Status Indicators for Mobile - Dipindahkan setelah door status -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <!-- Exhaust Fan Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 sm:p-4">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 mr-3 sm:mr-4 bg-green-100 rounded-full w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.071 4.929a10 10 0 10-14.142 14.142 10 10 0 0014.142-14.142z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8M8 12h8"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Exhaust fan</p>
                                <p class="text-sm sm:text-lg font-semibold" id="fan-status">
                                    {{ $fan == 'ON' ? 'Aktif' : 'Mati' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Alarm System Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 sm:p-4">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 mr-3 sm:mr-4 bg-red-100 rounded-full w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Alarm System</p>
                                <p class="text-sm sm:text-lg font-semibold" id="motion-status">
                                    {{ $motion == 'clear' ? 'Aman' : 'Terdeteksi' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Device and Sensor Information for Mobile -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6 mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                        <span class="inline-block mr-2">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                        </span>
                        Informasi perangkat dan sensor
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <!-- DHT 11 -->
                        <div class="flex items-center">
                            <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-cyan-100 rounded-full flex items-center justify-center w-10 h-10 sm:w-14 sm:h-14">
                                <img src="{{ asset('asset/dht.png') }}" class="w-6 h-6 sm:w-10 sm:h-10 cursor-pointer" alt="DHT11" onclick="openImageModal('{{ asset('asset/dht.png') }}')">
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">DHT 11</p>
                                <p class="dht-status text-sm sm:text-lg font-semibold {{ $dhtStatus == 'connected' ? 'text-green-500' : 'text-red-500' }}">{{ $dhtStatus == 'connected' ? 'Connected' : 'Disconnected' }}</p>
                            </div>
                        </div>

                        <!-- MPU 6050 -->
                        <div class="flex items-center">
                            <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-cyan-100 rounded-full flex items-center justify-center w-10 h-10 sm:w-14 sm:h-14">
                                <img src="{{ asset('asset/mpu.png') }}" class="w-6 h-6 sm:w-10 sm:h-10 cursor-pointer" alt="MPU6050" onclick="openImageModal('{{ asset('asset/mpu.png') }}')">
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">MPU 6050</p>
                                <p class="mpu-status text-sm sm:text-lg font-semibold {{ $mpuStatus == 'connected' ? 'text-green-500' : 'text-red-500' }}">{{ $mpuStatus == 'connected' ? 'Connected' : 'Disconnected' }}</p>
                            </div>
                        </div>

                        <!-- Servo MG996r -->
                        <div class="flex items-center">
                            <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-cyan-100 rounded-full flex items-center justify-center w-10 h-10 sm:w-14 sm:h-14">
                                <img src="{{ asset('asset/servo.png') }}" class="w-6 h-6 sm:w-10 sm:h-10 cursor-pointer" alt="Servo" onclick="openImageModal('{{ asset('asset/servo.png') }}')">
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Servo MG996r</p>
                                <p class="servo-status text-sm sm:text-lg font-semibold {{ $servoStatus == 'Connected' ? 'text-green-500' : 'text-red-500' }}">{{ $servoStatus }}</p>
                            </div>
                        </div>

                        <!-- RFID Reader -->
                        <div class="flex items-center">
                            <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-cyan-100 rounded-full flex items-center justify-center w-10 h-10 sm:w-14 sm:h-14">
                                <img src="{{ asset('asset/rfid.png') }}" class="w-6 h-6 sm:w-10 sm:h-10 cursor-pointer" alt="RFID" onclick="openImageModal('{{ asset('asset/rfid.png') }}')">
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">RFID Reader</p>
                                <p class="rfid-status text-sm sm:text-lg font-semibold {{ $rfidStatus == 'Connected' ? 'text-green-500' : 'text-red-500' }}">{{ $rfidStatus }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop layout (hidden on mobile) -->
            <div class="hidden md:block">
                <!-- Device Information Section -->
                <div class="mt-6 sm:mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Device and Sensor Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                            <span class="inline-block mr-2">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                            </span>
                            Informasi perangkat dan sensor
                        </h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- DHT 11 -->
                            <div class="flex items-center">
                                <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-cyan-100 rounded-full flex items-center justify-center w-10 h-10 sm:w-14 sm:h-14">
                                    <img src="{{ asset('asset/dht.png') }}" class="w-6 h-6 sm:w-10 sm:h-10 cursor-pointer" alt="DHT11" onclick="openImageModal('{{ asset('asset/dht.png') }}')">
                                </div>
                                <div>
                                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">DHT 11</p>
                                    <p class="dht-status text-sm sm:text-lg font-semibold {{ $dhtStatus == 'connected' ? 'text-green-500' : 'text-red-500' }}">{{ $dhtStatus == 'connected' ? 'Connected' : 'Disconnected' }}</p>
                                </div>
                            </div>

                            <!-- MPU 6050 -->
                            <div class="flex items-center">
                                <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-cyan-100 rounded-full flex items-center justify-center w-10 h-10 sm:w-14 sm:h-14">
                                    <img src="{{ asset('asset/mpu.png') }}" class="w-6 h-6 sm:w-10 sm:h-10 cursor-pointer" alt="MPU6050" onclick="openImageModal('{{ asset('asset/mpu.png') }}')">
                                </div>
                                <div>
                                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">MPU 6050</p>
                                    <p class="mpu-status text-sm sm:text-lg font-semibold {{ $mpuStatus == 'connected' ? 'text-green-500' : 'text-red-500' }}">{{ $mpuStatus == 'connected' ? 'Connected' : 'Disconnected' }}</p>
                                </div>
                            </div>

                            <!-- Servo MG996r -->
                            <div class="flex items-center">
                                <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-cyan-100 rounded-full flex items-center justify-center w-10 h-10 sm:w-14 sm:h-14">
                                    <img src="{{ asset('asset/servo.png') }}" class="w-6 h-6 sm:w-10 sm:h-10 cursor-pointer" alt="Servo" onclick="openImageModal('{{ asset('asset/servo.png') }}')">
                                </div>
                                <div>
                                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Servo MG996r</p>
                                    <p class="servo-status text-sm sm:text-lg font-semibold {{ $servoStatus == 'Connected' ? 'text-green-500' : 'text-red-500' }}">{{ $servoStatus }}</p>
                                </div>
                            </div>

                            <!-- RFID Reader -->
                            <div class="flex items-center">
                                <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-cyan-100 rounded-full flex items-center justify-center w-10 h-10 sm:w-14 sm:h-14">
                                    <img src="{{ asset('asset/rfid.png') }}" class="w-6 h-6 sm:w-10 sm:h-10 cursor-pointer" alt="RFID" onclick="openImageModal('{{ asset('asset/rfid.png') }}')">
                                </div>
                                <div>
                                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">RFID Reader</p>
                                    <p class="rfid-status text-sm sm:text-lg font-semibold {{ $rfidStatus == 'Connected' ? 'text-green-500' : 'text-red-500' }}">{{ $rfidStatus }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Door Status Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6">
                        <div class="flex items-center mb-6">
                            <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-green-100 rounded-full">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Door Status</p>
                                <p class="text-sm sm:text-lg font-semibold" id="door-status">
                                    {{ $servo_status }}
                                </p>
                            </div>
                        </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Akses Terakhir</p>
                                <p class="text-sm sm:text-lg font-semibold text-green-500" id="last-access">{{ $last_access }}</p>
                                </div>
                                <div>
                                <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Card ID terdeteksi</p>
                                <p class="text-sm sm:text-lg font-semibold text-green-500" id="status-device">{{ $status_device }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Bottom Section -->
            <div class="mt-6 sm:mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Existing Information Box -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-full mr-4">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">Keterangan</h3>
                    </div>
                    <div class="mt-4 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                        <p>⚠︎ Data perangkat Wemos D1 Mini dan NodeMCU ESP8266 memiliki jeda 1 menit. Saat pengecekan, tunggu 1 menit untuk melihat perubahan data.</p>
                        <button id="openGuide" class="mt-2 px-4 py-2 bg-yellow-500 bg-opacity-30 text-dark rounded hover:bg-opacity-50"><b> Lihat panduan lengkapnya</b></button>
                    </div>
                </div>

                <!-- Device Control and Status Section -->
                <div class="col-span-1 md:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Device Control Section -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="p-3 sm:p-4 mr-3 sm:mr-4 bg-blue-100 rounded-full">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">Mulai Ulang Perangkat</h3>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Wemos D1 mini -->
                                <button id="restartWemos" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition-all duration-200 shadow-md hover:shadow-xl dark:shadow-gray-900">
                                    <img src="{{ asset('asset/wemos.png') }}" class="w-12 h-12 sm:w-16 sm:h-16 mb-2 sm:mb-3" alt="Wemos D1 mini">
                                    <p class="text-sm sm:text-base text-gray-700 dark:text-gray-200 text-center">Wemos D1 mini</p>
                                </button>
                                
                                <!-- NodeMCU ESP8266 -->
                                <button id="restartESP" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition-all duration-200 shadow-md hover:shadow-xl dark:shadow-gray-900">
                                    <img src="{{ asset('asset/esp.png') }}" class="w-12 h-12 sm:w-16 sm:h-16 mb-2 sm:mb-3" alt="NodeMCU ESP8266">
                                    <p class="text-sm sm:text-base text-gray-700 dark:text-gray-200 text-center">NodeMCU ESP8266</p>
                                </button>
                            </div>
                        </div>

                        <!-- Status Indicators - Hanya tampil di desktop -->
                        <div class="grid grid-rows-2 gap-4 hidden md:grid">
                            <!-- Exhaust Fan Status -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 sm:p-4">
                                <div class="flex items-center">
                                    <div class="p-2 sm:p-3 mr-3 sm:mr-4 bg-green-100 rounded-full w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.071 4.929a10 10 0 10-14.142 14.142 10 10 0 0014.142-14.142z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8M8 12h8"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Exhaust fan</p>
                                        <p class="text-sm sm:text-lg font-semibold text-green-500" id="fan-status">Aktif</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Alarm System Status -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 sm:p-4">
                                <div class="flex items-center">
                                    <div class="p-2 sm:p-3 mr-3 sm:mr-4 bg-red-100 rounded-full w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Alarm System</p>
                                        <p class="text-sm sm:text-lg font-semibold text-gray-500" id="motion-status">Dinonaktifkan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Include notification panel component -->
    @include('components.notification-panel')

    <!-- Floating Action Button -->
    <div class="fixed bottom-0 right-0 z-50 mb-5 mr-5">
        <button class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-500 text-white shadow-lg hover:bg-blue-600" onclick="toggleModal()">
            <svg class="w-6 h-6 animate-spin" style="animation-duration: 3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </button>
    </div>

    <!-- Modal -->
    <div id="controlModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="toggleModal()"></div>
        <div class="fixed bottom-24 right-5 w-72 rounded-lg bg-white p-4 shadow-xl dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Security Controls</h3>
            
            <!-- Security Toggle -->
            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-700 dark:text-gray-200">Security System</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="securityToggle" class="sr-only peer" onchange="toggleSecurity(this)">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                </label>
            </div>
        </div>
    </div>

    <!-- Modal Structure -->
    <div id="guideModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="toggleGuideModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-11/12 max-w-md">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Panduan Lengkap</h3>
                <div class="flex items-start mb-2">
                    <div class="p-2 bg-blue-100 rounded-full mr-2">
                        <svg class="w-5 h-5 text-blue-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 14v1m8-8h1M4 12H3m15.364-6.364l.707.707M6.343 17.657l-.707.707M17.657 17.657l.707-.707M6.343 6.343l-.707-.707"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Jika anda restart wemos d1 mini maka perangkat RFID dan servo akan ikut dimulai ulang / di restart</p>
                </div>
                <div class="flex items-start">
                    <div class="p-2 bg-blue-100 rounded-full mr-2">
                        <svg class="w-5 h-5 text-blue-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 14v1m8-8h1M4 12H3m15.364-6.364l.707.707M6.343 17.657l-.707.707M17.657 17.657l.707-.707M6.343 6.343l-.707-.707"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Jika anda restart NodeMCU Esp8266 maka perangkat kipas, alarm, DHT 11, dan Mpu6050 akan ikut di mulai ulang</p>
                </div>
                <button class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600" onclick="toggleGuideModal()">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden">
        <!-- Background overlay to close the modal -->
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeImageModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 relative max-w-sm w-full">
                <!-- Close button with "X" icon -->
                <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white" onclick="closeImageModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <!-- Image displayed in the modal -->
                <img id="modalImage" src="" alt="Large Image" class="max-w-full h-auto max-h-64">
            </div>
        </div>
    </div>

    <script>
        function openImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageSrc;
            modal.classList.remove('hidden'); // Show the modal
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden'); // Hide the modal
        }
    </script>

    <!-- Firebase SDK v8 -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>

    <script>
        // Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyCr8xNQIsPpIUMGIR9wEGmG7hgMDKf2H5I",
            authDomain: "smartcab-8bb42.firebaseapp.com",
            databaseURL: "https://smartcab-8bb42-default-rtdb.firebaseio.com",
            projectId: "smartcab-8bb42",
            storageBucket: "smartcab-8bb42.firebasestorage.app",
            messagingSenderId: "539751617121",
            appId: "1:539751617121:web:3a899309fdb5e29efa9020",
            measurementId: "G-BQPQLLCJTR"
        };

        // Inisialisasi Firebase
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }
        
        const database = firebase.database();

        // Tambahkan variabel untuk melacak status restart ESP
        let espRestartRequested = false;

        // Listener untuk ESP status
        database.ref('logs/systemESP').on('value', (snapshot) => {
            const status = snapshot.val();
            console.log('ESP status received:', status);
            
            let statusText;
            let statusClass;
            
            if (status === 'Device online') {
                statusText = 'Online';
                statusClass = 'text-sm sm:text-lg font-semibold text-green-500';
            } else if (status === 'Device auto-restarting...' || status === 'Device restarting by command...') {
                statusText = 'Restarting...';
                statusClass = 'text-sm sm:text-lg font-semibold text-yellow-500';
            } else {
                statusText = 'Offline';
                statusClass = 'text-sm sm:text-lg font-semibold text-red-500';
            }
            
            document.querySelectorAll('#esp-status').forEach(el => {
                el.textContent = statusText;
                el.className = statusClass;
            });
        }, (error) => {
            console.error('Error getting ESP status:', error);
        });

        // Tambahkan listener khusus untuk restart ESP
        database.ref('control/restartESP').on('value', (snapshot) => {
            const restartValue = snapshot.val();
            console.log('ESP restart value:', restartValue);
            
            // Jika nilai restart adalah true, segera ubah status menjadi "Restarting..."
            if (restartValue === true || restartValue === "true") {
            document.querySelectorAll('#esp-status').forEach(el => {
                    el.textContent = 'Restarting...';
                    el.className = 'text-sm sm:text-lg font-semibold text-yellow-500';
                });
                
                // Pertahankan status "Restarting..." selama 30 detik
                                setTimeout(() => {
                    // Cek status saat ini sebelum mengubahnya kembali
                    database.ref('logs/systemESP').once('value', (snapshot) => {
                        const currentStatus = snapshot.val();
                        if (currentStatus !== 'Device online') {
                            document.querySelectorAll('#esp-status').forEach(el => {
                                el.textContent = 'Restarting...';
                                el.className = 'text-sm sm:text-lg font-semibold text-yellow-500';
                            });
                        }
                    });
                }, 30000);
            }
        });

        // Fungsi untuk memperbarui status ESP di UI
        function updateESPStatus(status) {
            let statusText;
            let statusClass;
            
            if (espRestartRequested) {
                statusText = 'Restarting...';
                statusClass = 'text-sm sm:text-lg font-semibold text-yellow-500';
            } else if (status === 'Device online') {
                statusText = 'Online';
                statusClass = 'text-sm sm:text-lg font-semibold text-green-500';
            } else if (status === 'Device auto-restarting...' || status === 'Device restarting by command...') {
                statusText = 'Restarting...';
                statusClass = 'text-sm sm:text-lg font-semibold text-yellow-500';
            } else {
                statusText = 'Offline';
                statusClass = 'text-sm sm:text-lg font-semibold text-red-500';
            }
            
            document.querySelectorAll('#esp-status').forEach(el => {
                el.textContent = statusText;
                el.className = statusClass;
            });
        }

        // Fungsi untuk memaksa perubahan pada DOM
        function forceUpdate(id, text, className = null) {
            try {
                // Cari elemen dengan ID
                const element = document.getElementById(id);
                if (!element) {
                    console.error(`Element with id ${id} not found`);
                    return;
                }

                // Buat elemen baru dengan konten yang sama
                const newElement = element.cloneNode(false);
                newElement.innerHTML = text;
                if (className) {
                    newElement.className = className;
                }
                
                // Ganti elemen lama dengan yang baru
                element.parentNode.replaceChild(newElement, element);
                
                console.log(`Element ${id} forcefully updated with: ${text}`);
            } catch (error) {
                console.error(`Error updating element ${id}:`, error);
            }
        }

        // Fungsi untuk memperbarui semua data dari snapshot
        function updateAllFromSnapshot(path, data) {
            console.log(`Updating all from ${path}:`, data);
            
            if (path === 'smartcab') {
                if (data.servo_status) {
                    document.querySelectorAll('#door-status').forEach(el => {
                        el.textContent = data.servo_status;
                        el.className = `text-sm sm:text-lg font-semibold ${
                            data.servo_status === 'Terkunci' ? 'text-green-500' : 'text-red-500'
                        }`;
                    });
                }
                
                if (data.last_access) {
                    document.querySelectorAll('#last-access').forEach(el => {
                        el.textContent = data.last_access;
                    });
                }
                
                if (data.status_device) {
                    document.querySelectorAll('#status-device').forEach(el => {
                        el.textContent = data.status_device;
                    });
                }
            }
            else if (path === 'dht11') {
                if (data.temperature) {
                    document.querySelectorAll('#temp-value').forEach(el => {
                        el.textContent = `${data.temperature}°C`;
                    });
                }
            }
            else if (path === 'logs') {
                if (data.systemWemos) {
                    const isOnline = data.systemWemos === 'Device Online';
                    const statusText = isOnline ? 'Online' : 'Offline';
                    const statusClass = `text-sm sm:text-lg font-semibold ${
                        isOnline ? 'text-green-500' : 'text-red-500'
                    }`;
                    document.querySelectorAll('#wemos-status').forEach(el => {
                        el.textContent = statusText;
                        el.className = statusClass;
                    });
                }
                
                if (data.systemESP) {
                    const isOnline = data.systemESP === 'Device online';
                    const statusText = isOnline ? 'Online' : 'Offline';
                    const statusClass = `text-sm sm:text-lg font-semibold ${
                        isOnline ? 'text-green-500' : 'text-red-500'
                    }`;
                    document.querySelectorAll('#esp-status').forEach(el => {
                        el.textContent = statusText;
                        el.className = statusClass;
                    });
                }
            }
            else if (path === 'security') {
                if (data.status) {
                    document.querySelectorAll('#security-status').forEach(el => {
                        el.textContent = data.status;
                    });
                }
                
                if (data.fan) {
                    const statusText = data.fan === 'ON' ? 'Aktif' : 'Mati';
                    const statusClass = `text-sm sm:text-lg font-semibold ${
                        data.fan === 'ON' ? 'text-green-500' : 'text-red-500'
                    }`;
                    document.querySelectorAll('#fan-status').forEach(el => {
                        el.textContent = statusText;
                        el.className = statusClass;
                    });
                }
                
                if (data.motion) {
                    const statusText = data.motion === 'clear' ? 'Aman' : 'Terdeteksi';
                    const statusClass = `text-sm sm:text-lg font-semibold ${
                        data.motion === 'clear' ? 'text-green-500' : 'text-red-500'
                    }`;
                    document.querySelectorAll('#motion-status').forEach(el => {
                        el.textContent = statusText;
                        el.className = statusClass;
                    });
                }
            }
        }

        // Fungsi untuk setup listeners dengan pendekatan baru
        function setupListeners() {
            console.log('Setting up all listeners with new approach...');
            
            // Hapus semua listener yang ada
            database.ref().off();
            
            // Listener untuk smartcab dengan pendekatan langsung
            database.ref('smartcab').on('value', (snapshot) => {
                const data = snapshot.val();
                console.log('Smartcab data received:', data);
                
                if (data) {
                    // Update door status
                    if (data.servo_status) {
                        document.querySelectorAll('#door-status').forEach(el => {
                            el.textContent = data.servo_status;
                            el.className = `text-sm sm:text-lg font-semibold ${
                                data.servo_status === 'Terkunci' ? 'text-green-500' : 'text-red-500'
                            }`;
                        });
                    }
                    
                    // Update last access
                    if (data.last_access) {
                        document.querySelectorAll('#last-access').forEach(el => {
                            el.textContent = data.last_access;
                        });
                    }
                    
                    // Update card ID
                    if (data.status_device) {
                        document.querySelectorAll('#status-device').forEach(el => {
                            el.textContent = data.status_device;
                        });
                    }
                }
            }, (error) => {
                console.error('Error getting smartcab data:', error);
            });
            
            // Listener untuk temperature
            database.ref('dht11/temperature').on('value', (snapshot) => {
                const temp = snapshot.val();
                console.log('Temperature data received:', temp);
                if (temp) {
                    document.querySelectorAll('#temp-value').forEach(el => {
                        el.textContent = `${temp}°C`;
                    });
                }
            }, (error) => {
                console.error('Error getting temperature data:', error);
            });
            
            // Listener untuk Wemos status
            database.ref('logs/systemWemos').on('value', (snapshot) => {
                const status = snapshot.val();
                console.log('Wemos status received:', status);
                
                let statusText;
                let statusClass;
                
                if (status === 'Device Online') {
                    statusText = 'Online';
                    statusClass = 'text-sm sm:text-lg font-semibold text-green-500';
                } else if (status === 'Device auto-restarting...') {
                    statusText = 'Restarting...';
                    statusClass = 'text-sm sm:text-lg font-semibold text-yellow-500';
                } else {
                    statusText = 'Offline';
                    statusClass = 'text-sm sm:text-lg font-semibold text-red-500';
                }
                
                document.querySelectorAll('#wemos-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = statusClass;
                });
            }, (error) => {
                console.error('Error getting Wemos status:', error);
            });
            
            // Listener untuk ESP status
            database.ref('logs/systemESP').on('value', (snapshot) => {
                const status = snapshot.val();
                console.log('ESP status received:', status);
                
                let statusText;
                let statusClass;
                
                if (status === 'Device online') {
                    statusText = 'Online';
                    statusClass = 'text-sm sm:text-lg font-semibold text-green-500';
                } else if (status === 'Device auto-restarting...' || status === 'Device restarting by command...') {
                    statusText = 'Restarting...';
                    statusClass = 'text-sm sm:text-lg font-semibold text-yellow-500';
                } else {
                    statusText = 'Offline';
                    statusClass = 'text-sm sm:text-lg font-semibold text-red-500';
                }
                
                document.querySelectorAll('#esp-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = statusClass;
                });
            }, (error) => {
                console.error('Error getting ESP status:', error);
            });
            
            // Listener untuk security status
            database.ref('security/status').on('value', (snapshot) => {
                const status = snapshot.val();
                console.log('Security status received:', status);
                
                document.querySelectorAll('#security-status').forEach(el => {
                    el.textContent = status;
                });
            }, (error) => {
                console.error('Error getting security status:', error);
            });
            
            // Listener untuk fan status
            database.ref('security/fan').on('value', (snapshot) => {
                const status = snapshot.val();
                console.log('Fan status received:', status);
                
                const statusText = status === 'ON' ? 'Aktif' : 'Mati';
                
                document.querySelectorAll('#fan-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = `text-sm sm:text-lg font-semibold ${
                        status === 'ON' ? 'text-green-500' : 'text-red-500'
                    }`;
                });
            }, (error) => {
                console.error('Error getting fan status:', error);
            });
            
            // Listener untuk motion status
            database.ref('security/motion').on('value', (snapshot) => {
                const status = snapshot.val();
                console.log('Motion status received:', status);
                
                let statusText, statusClass;
                
                if (status === 'clear') {
                    statusText = 'Aman';
                    statusClass = 'text-sm sm:text-lg font-semibold text-green-500';
                } else if (status === 'detected') {
                    statusText = 'Terdeteksi';
                    statusClass = 'text-sm sm:text-lg font-semibold text-red-500';
                } else if (status === 'disabled') {
                    statusText = 'Dinonaktifkan';
                    statusClass = 'text-sm sm:text-lg font-semibold text-gray-500';
                }
                
                document.querySelectorAll('#motion-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = statusClass;
                });
            }, (error) => {
                console.error('Error getting motion status:', error);
            });
            
            // TAMBAHAN: Listener untuk status sensor DHT11
            database.ref('sensors/dht11').on('value', (snapshot) => {
                const status = snapshot.val();
                console.log('DHT11 status received:', status);
                
                const isConnected = status === 'connected';
                const statusText = isConnected ? 'Connected' : 'Disconnected';
                const statusClass = `text-sm sm:text-lg font-semibold ${isConnected ? 'text-green-500' : 'text-red-500'}`;
                
                // Update status DHT11 di semua elemen yang sesuai
                document.querySelectorAll('.dht-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = `dht-status ${statusClass}`;
                });
            }, (error) => {
                console.error('Error getting DHT11 status:', error);
            });
            
            // TAMBAHAN: Listener untuk status sensor MPU6050
            database.ref('sensors/mpu6050').on('value', (snapshot) => {
                const status = snapshot.val();
                console.log('MPU6050 status received:', status);
                
                const isConnected = status === 'connected';
                const statusText = isConnected ? 'Connected' : 'Disconnected';
                const statusClass = `text-sm sm:text-lg font-semibold ${isConnected ? 'text-green-500' : 'text-red-500'}`;
                
                // Update status MPU6050 di semua elemen yang sesuai
                document.querySelectorAll('.mpu-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = `mpu-status ${statusClass}`;
                });
            }, (error) => {
                console.error('Error getting MPU6050 status:', error);
            });
            
            // TAMBAHAN: Listener untuk status Servo
            database.ref('sensors/servo').on('value', (snapshot) => {
                const status = snapshot.val();
                console.log('Servo status received:', status);
                
                const isConnected = status === 'Connected';
                const statusText = isConnected ? 'Connected' : 'Disconnected';
                const statusClass = `text-sm sm:text-lg font-semibold ${isConnected ? 'text-green-500' : 'text-red-500'}`;
                
                // Update status Servo di semua elemen yang sesuai
                document.querySelectorAll('.servo-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = `servo-status ${statusClass}`;
                });
            }, (error) => {
                console.error('Error getting Servo status:', error);
            });
            
            // TAMBAHAN: Listener untuk status RFID
            database.ref('sensors/rfid').on('value', (snapshot) => {
                const status = snapshot.val();
                console.log('RFID status received:', status);
                
                const isConnected = status === 'Connected';
                const statusText = isConnected ? 'Connected' : 'Disconnected';
                const statusClass = `text-sm sm:text-lg font-semibold ${isConnected ? 'text-green-500' : 'text-red-500'}`;
                
                // Update status RFID di semua elemen yang sesuai
                document.querySelectorAll('.rfid-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = `rfid-status ${statusClass}`;
                });
            }, (error) => {
                console.error('Error getting RFID status:', error);
            });
            
            console.log('All listeners setup complete with new approach');
        }

        // Fungsi untuk toggle modal
        function toggleModal() {
            const modal = document.getElementById('controlModal');
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }

        // Fungsi untuk restart perangkat
        function setupRestartButtons() {
            const restartWemos = document.getElementById('restartWemos');
            if (restartWemos) {
                restartWemos.addEventListener('click', function() {
                    // Cek status Wemos sebelum menampilkan dialog
                    const wemosStatus = document.querySelector('#wemos-status').textContent;
                    if (wemosStatus === 'Offline') {
                        Swal.fire(
                            'Tidak Dapat Melakukan Restart',
                            'Perangkat Wemos D1 Mini sedang offline.',
                            'error'
                        );
                        return;
                    }

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Anda ingin me-restart Wemos D1 Mini?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, restart!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Perbarui status restart di UI terlebih dahulu
                            document.querySelectorAll('#wemos-status').forEach(el => {
                                el.textContent = 'Restarting...';
                                el.className = 'text-sm sm:text-lg font-semibold text-yellow-500';
                            });
                            
                            // Perbarui status di Firebase
                            database.ref('logs/systemWemos').set('Device auto-restarting...')
                                .then(() => {
                                    console.log('Status Wemos diperbarui ke restarting');
                                    
                                    // Kirim perintah restart
                                    return database.ref('control/restartWemos').set(true);
                                })
                                .then(() => {
                                    Swal.fire(
                                        'Berhasil!',
                                        'Perintah restart Wemos D1 Mini berhasil dikirim.',
                                        'success'
                                    );
                                    
                                    // Reset perintah restart setelah 5 detik
                                    setTimeout(() => {
                                        database.ref('control/restartWemos').set(false);
                                    }, 5000);
                                })
                                .catch(error => {
                                    console.error('Error restarting Wemos:', error);
                                    Swal.fire(
                                        'Gagal!',
                                        'Gagal mengirim perintah restart.',
                                        'error'
                                    );
                                    
                                    // Kembalikan status jika gagal
                                    database.ref('logs/systemWemos').once('value', (snapshot) => {
                                        const previousStatus = snapshot.val();
                                        if (previousStatus === 'Device auto-restarting...') {
                                            database.ref('logs/systemWemos').set('Device Online');
                                        }
                                    });
                                });
                        }
                    });
                });
            }
            
            const restartESP = document.getElementById('restartESP');
            if (restartESP) {
                restartESP.addEventListener('click', function() {
                    // Cek status ESP sebelum menampilkan dialog
                    const espStatus = document.querySelector('#esp-status').textContent;
                    if (espStatus === 'Offline') {
                        Swal.fire(
                            'Tidak Dapat Melakukan Restart',
                            'Perangkat NodeMCU ESP8266 sedang offline.',
                            'error'
                        );
                        return;
                    }

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Anda ingin me-restart NodeMCU ESP8266?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, restart!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Perbarui status restart di UI terlebih dahulu
                            document.querySelectorAll('#esp-status').forEach(el => {
                                el.textContent = 'Restarting...';
                                el.className = 'text-sm sm:text-lg font-semibold text-yellow-500';
                            });
                            
                            // Perbarui status di Firebase
                            database.ref('logs/systemESP').set('Device restarting by command...')
                                .then(() => {
                                    console.log('Status ESP diperbarui ke restarting');
                                    
                                    // Kirim perintah restart
                                    return database.ref('control/restartESP').set(true);
                                })
                                .then(() => {
                                    Swal.fire(
                                        'Berhasil!',
                                        'Perintah restart NodeMCU ESP8266 berhasil dikirim.',
                                        'success'
                                    );
                                    
                                    // Reset perintah restart setelah 5 detik
                                    setTimeout(() => {
                                        database.ref('control/restartESP').set(false);
                                    }, 5000);
                                })
                                .catch(error => {
                                    console.error('Error restarting ESP:', error);
                                    Swal.fire(
                                        'Gagal!',
                                        'Gagal mengirim perintah restart.',
                                        'error'
                                    );
                                    
                                    // Kembalikan status jika gagal
                                    database.ref('logs/systemESP').once('value', (snapshot) => {
                                        const previousStatus = snapshot.val();
                                        if (previousStatus === 'Device restarting by command...') {
                                            database.ref('logs/systemESP').set('Device online');
                                        }
                                    });
                                });
                        }
                    });
                });
            }
        }

        // Fungsi untuk setup mobile menu
        function setupMobileMenu() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');

            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            const profileButton = document.querySelector('button img');
            const profileMenu = document.getElementById('profileMenu');

            if (profileButton && profileMenu) {
                profileButton.addEventListener('click', () => {
                    profileMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', (e) => {
                    if (!profileButton.contains(e.target) && !profileMenu.contains(e.target)) {
                        profileMenu.classList.add('hidden');
                    }
                });
            }
        }

        // Tambahkan fungsi untuk memastikan UI diperbarui secara real-time
        function forceRefreshDeviceStatus() {
            // Update ESP8266 status dari Firebase
            database.ref('logs/systemESP').once('value', (snapshot) => {
                const status = snapshot.val();
                console.log('Forcing refresh of ESP status:', status);
                
                let statusText, statusClass;
                
                if (status === 'Device online') {
                    statusText = 'Online';
                    statusClass = 'text-sm sm:text-lg font-semibold text-green-500';
                } else if (status === 'Device auto-restarting...' || status === 'Device restarting by command...') {
                    statusText = 'Restarting...';
                    statusClass = 'text-sm sm:text-lg font-semibold text-yellow-500';
                } else {
                    statusText = 'Offline';
                    statusClass = 'text-sm sm:text-lg font-semibold text-red-500';
                }
                
                document.querySelectorAll('#esp-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = statusClass;
                });
            });
            
            // Update Wemos status dari Firebase
            database.ref('logs/systemWemos').once('value', (snapshot) => {
                const status = snapshot.val();
                console.log('Forcing refresh of Wemos status:', status);
                
                let statusText, statusClass;
                
                if (status === 'Device Online') {
                    statusText = 'Online';
                    statusClass = 'text-sm sm:text-lg font-semibold text-green-500';
                } else if (status === 'Device auto-restarting...') {
                    statusText = 'Restarting...';
                    statusClass = 'text-sm sm:text-lg font-semibold text-yellow-500';
                } else {
                    statusText = 'Offline';
                    statusClass = 'text-sm sm:text-lg font-semibold text-red-500';
                }
                
                document.querySelectorAll('#wemos-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = statusClass;
                });
            });
            
            // PERBAIKAN: Update status sensor dengan path yang benar
            database.ref('logs/dht/status').once('value', (snapshot) => {
                const status = snapshot.val();
                console.log('Forcing refresh of DHT11 status:', status);
                
                const isConnected = status === 'connected';
                const statusText = isConnected ? 'Connected' : 'Disconnected';
                const statusClass = `text-sm sm:text-lg font-semibold ${isConnected ? 'text-green-500' : 'text-red-500'}`;
                
                document.querySelectorAll('.dht-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = `dht-status ${statusClass}`;
                });
            });
            
            database.ref('logs/mpu/status').once('value', (snapshot) => {
                const status = snapshot.val();
                console.log('Forcing refresh of MPU6050 status:', status);
                
                const isConnected = status === 'connected';
                const statusText = isConnected ? 'Connected' : 'Disconnected';
                const statusClass = `text-sm sm:text-lg font-semibold ${isConnected ? 'text-green-500' : 'text-red-500'}`;
                
                document.querySelectorAll('.mpu-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = `mpu-status ${statusClass}`;
                });
            });
            
            database.ref('logs/servo/status').once('value', (snapshot) => {
                const status = snapshot.val();
                console.log('Forcing refresh of Servo status:', status);
                
                const isConnected = status === 'Connected';
                const statusText = isConnected ? 'Connected' : 'Disconnected';
                const statusClass = `text-sm sm:text-lg font-semibold ${isConnected ? 'text-green-500' : 'text-red-500'}`;
                
                document.querySelectorAll('.servo-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = `servo-status ${statusClass}`;
                });
            });
            
            database.ref('logs/RFID/status').once('value', (snapshot) => {
                const status = snapshot.val();
                console.log('Forcing refresh of RFID status:', status);
                
                const isConnected = status === 'Connected';
                const statusText = isConnected ? 'Connected' : 'Disconnected';
                const statusClass = `text-sm sm:text-lg font-semibold ${isConnected ? 'text-green-500' : 'text-red-500'}`;
                
                document.querySelectorAll('.rfid-status').forEach(el => {
                    el.textContent = statusText;
                    el.className = `rfid-status ${statusClass}`;
                });
            });
        }

        // Fungsi untuk memeriksa status online/offline perangkat
        function checkDeviceStatus() {
            console.log('Checking device status based on data changes...');
            
            // Tambahkan pengecekan lebih agresif untuk ESP
            database.ref('device/lastActive').once('value', (snapshot) => {
                const lastActiveESP = snapshot.val();
                
                if (lastActiveESP) {
                    // Ambil nilai data sebelumnya untuk ESP jika tersedia
                    const previousDataESP = localStorage.getItem('previousESPData');
                    const currentDataESP = JSON.stringify(lastActiveESP);
                    
                    console.log('ESP8266 previous data: ' + previousDataESP);
                    console.log('ESP8266 current data: ' + currentDataESP);
                    
                    // Jika data tidak berubah sejak pengecekan terakhir, anggap offline
                    if (previousDataESP && previousDataESP === currentDataESP) {
                        console.log('ESP8266 terdeteksi offline - tidak ada perubahan data');
                        
                        database.ref('logs/systemESP').set('Device offline')
                            .then(() => {
                                console.log('Status ESP diperbarui ke offline');
                            })
                            .catch(error => {
                                console.error('Error updating ESP status:', error);
                            });
                    } else {
                        // Data berubah, perangkat dianggap online
                        console.log('ESP8266 terdeteksi online - data berubah');
                        database.ref('logs/systemESP').set('Device online');
                        
                        // Simpan data saat ini untuk pengecekan berikutnya
                        localStorage.setItem('previousESPData', currentDataESP);
                    }
                } else {
                    // Jika lastActive tidak ada, set status offline
                    console.log('ESP8266 lastActive tidak ditemukan - set status offline');
                    database.ref('logs/systemESP').set('Device offline');
                }
            });
            
            // Tambahkan pengecekan lebih agresif untuk Wemos
            database.ref('device/lastActiveWemos').once('value', (snapshot) => {
                const lastActiveWemos = snapshot.val();
                
                if (lastActiveWemos) {
                    // Ambil nilai data sebelumnya untuk Wemos jika tersedia
                    const previousData = localStorage.getItem('previousWemosData');
                    const currentData = JSON.stringify(lastActiveWemos);
                    
                    console.log('Wemos D1 Mini previous data: ' + previousData);
                    console.log('Wemos D1 Mini current data: ' + currentData);
                    
                    // Jika data tidak berubah sejak pengecekan terakhir, anggap offline
                    if (previousData && previousData === currentData) {
                        console.log('Wemos D1 Mini terdeteksi offline - tidak ada perubahan data');
                        
                        database.ref('logs/systemWemos').set('Device Offline')
                            .then(() => {
                                console.log('Status Wemos diperbarui ke offline');
                            })
                            .catch(error => {
                                console.error('Error updating Wemos status:', error);
                            });
                    } else {
                        // Data berubah, perangkat dianggap online
                        console.log('Wemos D1 Mini terdeteksi online - data berubah');
                        database.ref('logs/systemWemos').set('Device Online');
                        
                        // Simpan data saat ini untuk pengecekan berikutnya
                        localStorage.setItem('previousWemosData', currentData);
                    }
                } else {
                    // Jika lastActiveWemos tidak ada, set status offline
                    console.log('Wemos lastActive tidak ditemukan - set status offline');
                    database.ref('logs/systemWemos').set('Device Offline');
                }
            });
        }

        // Function to toggle security system (updated with lowercase values)
        function toggleSecurity(checkbox) {
            // Get the current state (checked = on, unchecked = off)
            const isEnabled = checkbox.checked;
            const securityStatus = isEnabled ? 'on' : 'off';
            
            console.log(`Toggling security system to: ${securityStatus}`);
            
            // Update Firebase with the new status
            database.ref('security/status').set(securityStatus)
                .then(() => {
                    console.log(`Security status updated to: ${securityStatus}`);
                    
                    // Also update motion to disabled when turned off
                    if (!isEnabled) {
                        return database.ref('security/motion').set('disabled');
                    }
                })
                .then(() => {
                    if (!isEnabled) {
                        console.log('Motion sensor disabled');
                    }
                })
                .catch(error => {
                    console.error('Error updating security status:', error);
                    // Revert the checkbox state if there was an error
                    checkbox.checked = !isEnabled;
                    alert(`Failed to update security status: ${error.message}`);
                });
        }

        // Add this code to initialize the toggle based on current state (inside window.load event)
        function initializeSecurityToggle() {
            const securityToggle = document.getElementById('securityToggle');
            if (securityToggle) {
                // Get the current security status from Firebase
                database.ref('security/status').once('value', (snapshot) => {
                    const status = snapshot.val();
                    console.log('Current security status:', status);
                    
                    // Set the toggle based on the status
                    securityToggle.checked = (status === 'on');
                });
                
                // Also set up a listener to keep the toggle in sync
                database.ref('security/status').on('value', (snapshot) => {
                    const status = snapshot.val();
                    console.log('Security status changed:', status);
                    
                    // Only update if the value doesn't match (to prevent loops)
                    if (securityToggle.checked !== (status === 'on')) {
                        securityToggle.checked = (status === 'on');
                    }
                });
            }
        }

        // Inisialisasi semua fungsi saat window load
        window.addEventListener('load', function() {
            console.log('Window loaded, initializing...');
            
            // Tunggu sedikit untuk memastikan DOM sudah siap
            setTimeout(function() {
                setupListeners();
                setupRestartButtons();
                setupMobileMenu();
                initializeSecurityToggle();
                
                // Jalankan pengecekan status perangkat segera dan lebih sering
                checkDeviceStatus(); // Panggil saat awal
                setInterval(checkDeviceStatus, 61000); // Periksa setiap 10 detik
                
                // Refresh UI lebih sering
                forceRefreshDeviceStatus(); // Panggil saat awal
                setInterval(forceRefreshDeviceStatus, 3000); // Refresh UI setiap 3 detik
                
                // Polling untuk listener
                setInterval(function() {
                    console.log('Refreshing listeners...');
                    setupListeners();
                }, 61000);
                
                console.log('Initialization complete');
            }, 1000);
        });

        // Tambahkan listener untuk debugging
        console.log('Script loaded');
        
        // Tambahkan interval untuk memaksa refresh DOM setiap 5 detik
        setInterval(function() {
            console.log('Forcing DOM refresh...');
            
            // Ambil data terbaru dari Firebase dan perbarui UI
            database.ref('smartcab').once('value', (snapshot) => {
                const data = snapshot.val();
                if (data) {
                    // Update door status
                    if (data.servo_status) {
                        document.querySelectorAll('#door-status').forEach(el => {
                            el.textContent = data.servo_status;
                            el.className = `text-sm sm:text-lg font-semibold ${
                                data.servo_status === 'Terkunci' ? 'text-green-500' : 'text-red-500'
                            }`;
                        });
                    }
                    
                    // Update last access
                    if (data.last_access) {
                        document.querySelectorAll('#last-access').forEach(el => {
                            el.textContent = data.last_access;
                        });
                    }
                    
                    // Update card ID
                    if (data.status_device) {
                        document.querySelectorAll('#status-device').forEach(el => {
                            el.textContent = data.status_device;
                        });
                    }
                }
            });
        }, 5000);

        // Function to toggle the guide modal
        function toggleGuideModal() {
            const guideModal = document.getElementById('guideModal');
            if (guideModal) {
                guideModal.classList.toggle('hidden');
            }
        }

        // Add event listener to the guide button
        document.getElementById('openGuide').addEventListener('click', function(event) {
            event.preventDefault();
            toggleGuideModal();
        });

        function openImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageSrc;
            modal.classList.remove('hidden'); // Show the modal
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden'); // Hide the modal
        }
    </script>
</body>
</html>
