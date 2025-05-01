<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Card Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
    <style>
        .highlight {
            animation: highlight-row 2s ease-in-out;
        }
        @keyframes highlight-row {
            0% { background-color: #fff; }
            50% { background-color: #d1ecf1; }
            100% { background-color: #fff; }
        }
    </style>
</head>
<body class="bg-gray-50">
    

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">RFID Card Management</h2>
            <a href="{{route('welcome')}}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Last Tapped Card Panel -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="mb-4 border-b border-gray-200 pb-3">
                <h5 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Last Tapped Card
                </h5>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Card ID:</span>
                        <span id="last-card-id" class="font-semibold text-blue-600">{{ $lastCardId ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Status:</span>
                        <span id="last-access" class="font-semibold {{ $lastAccess == 'Terdaftar' ? 'text-green-600' : 'text-red-600' }}">{{ $lastAccess ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Combined Add New Card and Registered Cards Panel -->
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="mb-4 border-b border-gray-200 pb-3">
                    <h5 class="text-lg font-medium text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Card Management
                    </h5>
                </div>

                <!-- Add New Card Form -->
                <div class="mb-6">
                    <button type="button" id="startRegistration" 
                            class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2.5 inline-flex items-center whitespace-nowrap">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Card
                    </button>
                </div>

                <!-- Registered Cards Table -->
                <div class="mt-6">
                    <h6 class="text-md font-medium text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Registered Cards
                    </h6>
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">No.</th>
                                    <th scope="col" class="px-6 py-3">Card ID</th>
                                    <th scope="col" class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($cards))
                                    @foreach($cards as $key => $cardId)
                                        <tr class="bg-white border-b hover:bg-gray-50 {{ $cardId == $lastCardId ? 'highlight' : '' }}">
                                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                            <td class="px-6 py-4">{{ $cardId }}</td>
                                            <td class="px-6 py-4">
                                                <a href="{{ route('cards.delete', $cardId) }}" 
                                                   onclick="return confirm('Are you sure you want to delete this card?')"
                                                   class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-xs px-3 py-1.5 inline-flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Delete
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="bg-white border-b">
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">No cards registered</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- System Status Panel -->
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="mb-4 border-b border-gray-200 pb-3">
                    <h5 class="text-lg font-medium text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        System Status
                    </h5>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Total Cards:
                        </span>
                        <span class="font-semibold">{{ $total ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            System Status:
                        </span>
                        <span id="system-status" class="font-semibold text-gray-800">Loading...</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            RFID Status:
                        </span>
                        <span id="rfid-status" class="font-semibold text-gray-800">Loading...</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Servo Status:
                        </span>
                            <span id="servo-status" class="font-semibold text-gray-800">Loading...</span>
                    </div>
                    <div class="mt-5">
                        <button onclick="if(confirm('Are you sure you want to delete ALL cards?')) window.location.href='{{ route('cards.delete.all') }}'" 
                            class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete All Cards
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Firebase configuration - make sure to use your project's actual config
        const firebaseConfig = {
            apiKey: "AIzaSyAw9f5XkTkMg-gPE8tnLYBhTibnFyLCHG0",
            authDomain: "smartcab-8bb42.firebaseapp.com",
            databaseURL: "https://smartcab-8bb42-default-rtdb.firebaseio.com",
            projectId: "smartcab-8bb42",
            storageBucket: "smartcab-8bb42.appspot.com",
            messagingSenderId: "724750966822",
            appId: "1:724750966822:web:6d8af1a18e8d8b5cbc0279"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        const database = firebase.database();

        document.addEventListener('DOMContentLoaded', function() {
            // Listen for real-time updates to device status
            database.ref('logs').on('value', (snapshot) => {
                const data = snapshot.val();
                if (data) {
                    // System status update
                    if (data.systemWemos) {
                        const systemStatus = document.getElementById('system-status');
                        systemStatus.textContent = data.systemWemos;
                        
                        if (data.systemWemos === 'Device Online') {
                            systemStatus.classList.add('text-green-600');
                            systemStatus.classList.remove('text-red-600', 'text-gray-800');
                        } else {
                            systemStatus.classList.add('text-red-600');
                            systemStatus.classList.remove('text-green-600', 'text-gray-800');
                        }
                    }
                    
                    // RFID status update
                    if (data.RFID && data.RFID.status) {
                        const rfidStatus = document.getElementById('rfid-status');
                        rfidStatus.textContent = data.RFID.status;
                        
                        if (data.RFID.status === 'Connected') {
                            rfidStatus.classList.add('text-green-600');
                            rfidStatus.classList.remove('text-red-600', 'text-gray-800');
                        } else {
                            rfidStatus.classList.add('text-red-600');
                            rfidStatus.classList.remove('text-green-600', 'text-gray-800');
                        }
                    }
                    
                    // Servo status update
                    if (data.servo && data.servo.status) {
                        const servoStatus = document.getElementById('servo-status');
                        servoStatus.textContent = data.servo.status;
                        
                        if (data.servo.status === 'Connected') {
                            servoStatus.classList.add('text-green-600');
                            servoStatus.classList.remove('text-blue-600', 'text-gray-800');
                        } else {
                            servoStatus.classList.add('text-red-600');
                            servoStatus.classList.remove('text-green-600', 'text-gray-800');
                        }
                    }
                }
            });
            
            // Listen for real-time updates to card data
            database.ref('smartcab').on('value', (snapshot) => {
                const data = snapshot.val();
                if (data) {
                    // Update last tapped card
                    if (data.status_device) {
                        const lastCardId = document.getElementById('last-card-id');
                        lastCardId.textContent = data.status_device;
                        
                        // Highlight the corresponding row
                        highlightCardRow(data.status_device);
                    }
                    
                    // Update access status
                    if (data.last_access) {
                        const lastAccess = document.getElementById('last-access');
                        lastAccess.textContent = data.last_access;
                        
                        if (data.last_access === 'Terdaftar') {
                            lastAccess.classList.add('text-green-600');
                            lastAccess.classList.remove('text-red-600');
                        } else {
                            lastAccess.classList.add('text-red-600');
                            lastAccess.classList.remove('text-green-600');
                        }
                    }
                }
            });
            
            // Listen for real-time updates to registered cards count
            database.ref('registered_cards/total').on('value', (snapshot) => {
                const totalCards = snapshot.val();
                const totalElement = document.querySelector('.flex.justify-between.items-center.p-3.bg-gray-50.rounded-lg:first-child .font-semibold');
                if (totalElement) {
                    totalElement.textContent = totalCards || 0;
                }
            });
            
            // Listen for changes to the card list
            database.ref('registered_cards/list').on('value', (snapshot) => {
                const cards = snapshot.val();
                // This would require refreshing the table, but complex to do without a page reload
                // Instead, show a notification that new data is available
                if (cards && Object.keys(cards).length > 0) {
                    const tableBody = document.querySelector('tbody');
                    const currentCardCount = tableBody.querySelectorAll('tr:not(.empty-row)').length;
                    
                    if (Object.keys(cards).length !== currentCardCount) {
                        showUpdateNotification();
                    }
                }
            });
            
            function showUpdateNotification() {
                // Create notification if it doesn't exist
                if (!document.getElementById('update-notification')) {
                    const notification = document.createElement('div');
                    notification.id = 'update-notification';
                    notification.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2';
                    notification.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Card list updated!</span>
                        <button class="ml-2 text-white hover:text-gray-200" onclick="location.reload()">Refresh</button>
                    `;
                    document.body.appendChild(notification);
                    
                    // Auto-remove after 10 seconds
                    setTimeout(() => {
                        notification.remove();
                    }, 10000);
                }
            }
            
            // Function to highlight the card row if found in the table
            function highlightCardRow(cardId) {
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    // Remove any existing highlights
                    row.classList.remove('highlight');
                    
                    const cardCell = row.querySelector('td:nth-child(2)');
                    if (cardCell && cardCell.textContent.trim() === cardId) {
                        row.classList.add('highlight');
                    }
                });
            }

            const startRegistrationBtn = document.getElementById('startRegistration');
            let lastScannedCard = null;
            let registrationInProgress = false;

            // Fungsi untuk menampilkan popup "Tempelkan Kartu"
            function showScanPrompt() {
                Swal.fire({
                    title: 'Tempelkan Kartu',
                    html: '<div class="text-center">' +
                          '<div class="mb-4">' +
                          '<svg class="mx-auto animate-pulse w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">' +
                          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>' +
                          '</svg>' +
                          '</div>' +
                          '<p class="text-gray-500">Silakan tempelkan kartu RFID pada reader</p>' +
                          '</div>',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCancelButton: true,
                    cancelButtonText: 'Batal',
                    cancelButtonColor: '#d33',
                });
            }

            // Fungsi untuk konfirmasi pendaftaran kartu
            function confirmCardRegistration(cardId) {
                Swal.fire({
                    title: 'Konfirmasi Pendaftaran',
                    html: `<div class="text-center">` +
                          `<p class="mb-2">ID Kartu terdeteksi:</p>` +
                          `<p class="text-lg font-semibold text-blue-600">${cardId}</p>` +
                          `<p class="mt-4">Apakah Anda ingin mendaftarkan kartu ini?</p>` +
                          `</div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Daftarkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        registerCard(cardId);
                    }
                });
            }

            // Fungsi untuk mendaftarkan kartu
            async function registerCard(cardId) {
                try {
                    const response = await fetch('{{ route('cards.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ card_id: cardId })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Kartu berhasil didaftarkan',
                            icon: 'success',
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            // Reload halaman untuk memperbarui daftar kartu
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan saat mendaftarkan kartu');
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonColor: '#3085d6'
                    });
                }
            }

            // Listener untuk tombol Add Card
            startRegistrationBtn.addEventListener('click', function() {
                showScanPrompt();
                registrationInProgress = true;
            });

            // Listener untuk data Firebase
            database.ref('smartcab').on('value', (snapshot) => {
                const data = snapshot.val();
                if (data && data.status_device && registrationInProgress) {
                    const cardId = data.status_device;
                    
                    // Hanya tampilkan konfirmasi jika kartu berbeda dari sebelumnya
                    if (cardId !== lastScannedCard) {
                        lastScannedCard = cardId;
                        Swal.close(); // Tutup popup "Tempelkan Kartu"
                        confirmCardRegistration(cardId);
                        registrationInProgress = false;
                    }
                }
            });
        });
    </script>
</body>
</html> 