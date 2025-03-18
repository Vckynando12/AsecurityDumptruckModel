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
        class="fixed inset-y-0 left-0 w-full max-w-xs bg-white dark:bg-gray-800 overflow-y-auto"
    >
        <div class="flex flex-col h-screen">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-light">Notifications</h2>
                <button @click="isNotificationsPanelOpen = false" class="p-2 text-gray-600 rounded-md hover:bg-gray-100 dark:text-light dark:hover:bg-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Notification Items Container -->
            <div id="notificationContainer" class="flex-1 p-4 space-y-4 overflow-y-auto">
                <!-- Notifications will be dynamically inserted here -->
            </div>
        </div>
    </div>
</div> 