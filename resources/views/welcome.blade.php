<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                        <span class="text-xs lg:text-sm font-medium dark:text-white">Smart Cabin Security & Monitoring</span>
                    </div>
                </div>
                
                <!-- Mobile Right Menu -->
                <div class="flex items-center gap-2 lg:hidden">
                    <a href="{{route('ai.chat')}}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                        AI
                    </a>
                    <!-- Notification Button Mobile -->
                    <button 
                        @click="isNotificationsPanelOpen = true"
                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </button>

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
                    <a href="{{route('ai.chat')}}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                        AI
                    </a>
                    
                    
                    <!-- Notification Button -->
                    <button 
                        @click="isNotificationsPanelOpen = true"
                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </button>

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
                            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">Your Profile</a>
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
                        <a href="#" class="block px-4 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">Your Profile</a>
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
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Temperature -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-red-500 bg-red-100 rounded-full dark:text-red-100 dark:bg-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Temperature</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="temp-value">{{ $temperature }}°C</p>
                        </div>
                    </div>
                </div>

                <!-- Humidity -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-100 dark:bg-blue-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Humidity</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="humidity-value">{{ $humidity }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Motion -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-yellow-500 bg-yellow-100 rounded-full dark:text-yellow-100 dark:bg-yellow-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Motion</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="motion-value">{{ $motion }}</p>
                        </div>
                    </div>
                </div>

                <!-- Security Status -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Security Status</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="status-value">{{ $status }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="mt-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Doughnut Chart -->
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 transition-colors duration-200">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-300 mb-4">Temperature & Humidity Overview</h4>
                        <div class="h-64">
                            <canvas id="sensorChart"></canvas>
                        </div>
                    </div>

                    <!-- Line Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 transition-colors duration-200">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-300 mb-4">Revenue Growth</h4>
                        <div class="h-64">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Notification Panel -->
    <div 
        x-show="isNotificationsPanelOpen" 
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-0 z-50"
    >
        <!-- Backdrop -->
        <div 
            class="fixed inset-0 bg-black bg-opacity-50" 
            @click="isNotificationsPanelOpen = false"
        ></div>
        
        <!-- Panel -->
        <div 
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 w-full max-w-xs bg-white dark:bg-gray-800 overflow-y-auto"
        >
            <div class="flex flex-col h-screen">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b dark:border-primary-darker">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-light">Notifications</h2>
                    <button @click="isNotificationsPanelOpen = false" class="p-2 text-gray-600 rounded-md hover:bg-gray-100 dark:text-light dark:hover:bg-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Notification Items -->
                <div class="flex-1 p-4 space-y-4">
                    <!-- Sample Notification Item -->
                    <div class="flex p-4 bg-white dark:bg-darker rounded-lg shadow">
                        <div class="flex-shrink-0">
                            <span class="p-2 bg-blue-500 rounded-full text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-light">New Order Received</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Order #123 has been placed</p>
                            <span class="text-xs text-gray-400 dark:text-gray-500">2 minutes ago</span>
                        </div>
                    </div>
                    
                    <!-- ... more notification items -->
                </div>
            </div>
        </div>
    </div>

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

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        const database = firebase.database();

        let isUserAction = false;

        // Function to update UI elements
        function updateUIElement(elementId, value, suffix = '') {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = `${value}${suffix}`;
                console.log(`Updated ${elementId} with value: ${value}${suffix}`);
            } else {
                console.error(`Element ${elementId} not found`);
            }
        }

        // Function to update UI with new data
        function updateUI(data) {
            console.log('Updating UI with data:', data);
            
            if (data.dht11) {
                updateUIElement('temp-value', data.dht11.temperature, '°C');
                updateUIElement('humidity-value', data.dht11.humidity, '%');
                
                // Update chart if it exists
                if (typeof sensorChart !== 'undefined') {
                    updateChart(Date.now(), data.dht11.temperature, data.dht11.humidity);
                }
            }
            
            if (data.security) {
                updateUIElement('motion-value', data.security.motion);
                updateUIElement('status-value', data.security.status);
                
                const toggle = document.getElementById('securityToggle');
                if (toggle) {
                    toggle.checked = data.security.status === 'on';
                }
            }
        }

        // Function to fetch and update data
        function fetchAndUpdateData() {
            fetch('https://smartcab-8bb42-default-rtdb.firebaseio.com/.json')
                .then(response => response.json())
                .then(data => {
                    console.log('Data fetched successfully:', data);
                    updateUI(data);
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        }

        // Function to toggle security status
        function toggleSecurity(checkbox) {
            isUserAction = true;
            console.log('Toggle security called with state:', checkbox.checked);
            
            const securityRef = database.ref('security/status');
            const newStatus = checkbox.checked ? 'on' : 'off';
            
            checkbox.disabled = true;
            
            securityRef.set(newStatus)
                .then(() => {
                    console.log('Security status updated successfully to:', newStatus);
                    updateUIElement('status-value', newStatus);
                })
                .catch((error) => {
                    console.error('Error updating security status:', error);
                    checkbox.checked = !checkbox.checked;
                    alert('Failed to update security status. Please try again.');
                })
                .finally(() => {
                    checkbox.disabled = false;
                    setTimeout(() => {
                        isUserAction = false;
                    }, 1000);
                });
        }

        // Listen for security status changes
        database.ref('security/status').on('value', (snapshot) => {
            const status = snapshot.val();
            if (status && !isUserAction) {
                console.log('Security status updated:', status);
                updateUIElement('status-value', status);
                
                const securityToggle = document.getElementById('securityToggle');
                if (securityToggle && !securityToggle.disabled) {
                    securityToggle.checked = status === 'on';
                }
            }
        });

        // Initialize when document is ready
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Document ready, initializing...');
            fetchAndUpdateData();
            setInterval(fetchAndUpdateData, 1000);
            
            // Initialize charts
            initializeCharts();
        });

        // Initialize the chart with empty data
        const ctx = document.getElementById('sensorChart').getContext('2d');
        const sensorChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Temperature',
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        data: [],
                        pointStyle: 'circle',
                        pointRadius: 6,
                        pointHoverRadius: 8
                    },
                    {
                        label: 'Humidity',
                        borderColor: 'rgb(135, 206, 235)',
                        backgroundColor: 'rgba(135, 206, 235, 0.5)',
                        data: [],
                        pointStyle: 'circle',
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                        },
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                        }
                    },
                    x: {
                        grid: {
                            color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                        },
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                        }
                    }
                }
            }
        });

        // Function to update chart data
        function updateChart(timestamp, temperature, humidity) {
            const MAX_DATA_POINTS = 10;
            
            // Add new data
            sensorChart.data.labels.push(new Date(timestamp).toLocaleTimeString());
            sensorChart.data.datasets[0].data.push(temperature);
            sensorChart.data.datasets[1].data.push(humidity);

            // Remove old data if we have more than MAX_DATA_POINTS
            if (sensorChart.data.labels.length > MAX_DATA_POINTS) {
                sensorChart.data.labels.shift();
                sensorChart.data.datasets[0].data.shift();
                sensorChart.data.datasets[1].data.shift();
            }

            sensorChart.update();
        }

        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Dark Mode Handler
        const darkModeToggle = document.getElementById('darkModeToggle');
        const mobileDarkModeToggle = document.getElementById('mobileDarkModeToggle');
        
        function toggleDarkMode() {
            const root = document.documentElement;
            const isDark = root.classList.contains('dark');
            
            if (isDark) {
                root.classList.remove('dark');
                localStorage.setItem('darkMode', 'disabled');
            } else {
                root.classList.add('dark');
                localStorage.setItem('darkMode', 'enabled');
            }
            updateChartsTheme();
        }

        // Check system preference and localStorage
        if (localStorage.getItem('darkMode') === 'enabled' || 
            (localStorage.getItem('darkMode') === null && 
             window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        darkModeToggle.addEventListener('click', toggleDarkMode);
        mobileDarkModeToggle.addEventListener('click', toggleDarkMode);

        // Update Tailwind dark mode classes
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    const isDark = document.documentElement.classList.contains('dark');
                    document.body.className = isDark ? 'dark:bg-gray-900' : 'bg-gray-100';
                    
                    // Update all dark mode elements
                    document.querySelectorAll('[class*="dark:"]').forEach(element => {
                        const darkClasses = Array.from(element.classList)
                            .filter(cls => cls.startsWith('dark:'));
                        
                        darkClasses.forEach(cls => {
                            const lightClass = cls.replace('dark:', '');
                            if (isDark) {
                                element.classList.add(lightClass);
                            } else {
                                element.classList.remove(lightClass);
                            }
                        });
                    });
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });

        // Profile Menu
        const profileButton = document.querySelector('button img');
        const profileMenu = document.getElementById('profileMenu');
        
        profileButton.addEventListener('click', () => {
            profileMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!profileButton.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.add('hidden');
            }
        });

        // Function to update charts theme
        function updateChartsTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#fff' : '#000';
            const gridColor = isDark ? '#374151' : '#e5e7eb';

            // Update both charts
            [sensorChart, lineChart].forEach(chart => {
                if (chart) {
                    chart.options.plugins.legend.labels.color = textColor;
                    if (chart.options.scales) {
                        Object.values(chart.options.scales).forEach(scale => {
                            scale.ticks.color = textColor;
                            scale.grid.color = gridColor;
                        });
                    }
                    chart.update();
                }
            });
        }

        // Charts configuration
        let lineChart;

        // Initialize charts
        function initializeCharts() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#fff' : '#000';
            const gridColor = isDark ? '#374151' : '#e5e7eb';

            // Line Chart
            const lineCtx = document.getElementById('lineChart').getContext('2d');
            lineChart = new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue',
                        data: [65, 59, 80, 81, 56, 55],
                        borderColor: '#3B82F6',
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: textColor
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                color: gridColor
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                color: gridColor
                            }
                        }
                    }
                }
            });
        }

        // Add these functions to your existing script
        function toggleModal() {
            const modal = document.getElementById('controlModal');
            modal.classList.toggle('hidden');
            
            // Update toggle state when modal opens
            if (!modal.classList.contains('hidden')) {
                database.ref('security/status').once('value', (snapshot) => {
                    const status = snapshot.val();
                    document.getElementById('securityToggle').checked = status === 'on';
                });
            }
        }
    </script>
</body>
</html>
