<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .badge {
            margin-right: 5px;
        }
        #loading-indicator {
            display: none;
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
        .highlight {
            animation: highlight-row 2s ease-in-out;
        }
        @keyframes highlight-row {
            0% { background-color: #fff; }
            50% { background-color: #d1ecf1; }
            100% { background-color: #fff; }
        }
        /* Styling untuk multiple select */
        select[multiple] {
            max-height: 200px;
            overflow-y: auto;
        }
        
        select[multiple] optgroup {
            font-weight: 600;
            color: #374151;
            padding: 0.25rem 0;
        }
        
        select[multiple] option {
            padding: 0.25rem 0.5rem;
            margin: 0.125rem 0;
        }
        
        select[multiple] option:checked {
            background-color: #2563eb;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50">

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Laporan Keamanan dan Monitoring</h2>
        <a href="{{route('welcome')}}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Dashboard Overview - Single Comprehensive Chart -->
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-medium text-gray-900">Ringkasan Status Sistem</h5>
            <span class="text-sm text-gray-500" id="system-chart-count"></span>
            </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div id="systemOverviewChart" class="w-full h-64 md:h-80"></div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <h6 class="text-base font-medium text-gray-900 mb-3">Penjelasan Status</h6>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                        <span><strong>Fan:</strong> Perubahan pada status kipas</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                        <span><strong>Status:</strong> Perubahan status keamanan (aman/bahaya)</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>
                        <span><strong>Motion:</strong> Perubahan status gerakan</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-cyan-500 mr-2"></span>
                        <span><strong>Servo Status:</strong> Perubahan status servo (terbuka/terkunci)</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-violet-400 mr-2"></span>
                        <span><strong>Last Access:</strong> Perubahan pada akses terakhir</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-orange-500 mr-2"></span>
                        <span><strong>Restart ESP:</strong> Perangkat ESP direstart</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-amber-500 mr-2"></span>
                        <span><strong>Restart Wemos:</strong> Perangkat Wemos direstart</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-indigo-500 mr-2"></span>
                        <span><strong>RFID:</strong> Perubahan pada status RFID</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-lime-500 mr-2"></span>
                        <span><strong>DHT:</strong> Perubahan pada sensor DHT</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-teal-500 mr-2"></span>
                        <span><strong>MPU:</strong> Perubahan pada sensor MPU</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-sky-500 mr-2"></span>
                        <span><strong>Servo Log:</strong> Perubahan pada log servo</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-rose-500 mr-2"></span>
                        <span><strong>System ESP:</strong> Perubahan status sistem ESP</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-pink-500 mr-2"></span>
                        <span><strong>System Wemos:</strong> Perubahan status sistem Wemos</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-gray-500 mr-2"></span>
                        <span><strong>Device Status:</strong> Perubahan status perangkat lainnya</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filter Panel Flowbite Style -->
    <div class="p-4 mb-6 border border-gray-200 rounded-lg bg-white shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-medium text-gray-900">Filter Data</h5>
            <button id="toggleFilterBtn" class="text-sm px-3 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-2 focus:ring-gray-300 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filter
            </button>
        </div>
        
        <div id="filterPanel" class="hidden">
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tanggal -->
                <div>
                    <label for="filterDate" class="block mb-2 text-sm font-medium text-gray-700">Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </div>
                        <input type="date" id="filterDate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5">
                    </div>
                </div>
                
                <!-- Waktu / Jam dengan Time Range Picker Flowbite -->
                <div>
                    <label for="filterTimeToggle" class="block mb-2 text-sm font-medium text-gray-700">Waktu</label>
                    
                    <div class="flex items-center">
                        <!-- Toggle -->
                        <label class="relative inline-flex items-center cursor-pointer mr-3">
                            <input type="checkbox" id="filterTimeToggle" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ms-2 text-sm font-medium text-gray-700" id="timeToggleStatus">Semua Waktu</span>
                        </label>
                    </div>
                    
                    <!-- Time range picker (shown when toggle is active) -->
                    <div id="timeRangePicker" class="mt-2 hidden">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="startTime" class="block mb-1 text-xs text-gray-500">Dari</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm3.982 13.982a1 1 0 0 1-1.414 0l-3.274-3.274A1.012 1.012 0 0 1 9 10V6a1 1 0 0 1 2 0v3.586l2.982 2.982a1 1 0 0 1 0 1.414Z"/>
                                        </svg>
                                    </div>
                                    <input type="time" id="startTime" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5">
                                </div>
                            </div>
                            <div>
                                <label for="endTime" class="block mb-1 text-xs text-gray-500">Sampai</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm3.982 13.982a1 1 0 0 1-1.414 0l-3.274-3.274A1.012 1.012 0 0 1 9 10V6a1 1 0 0 1 2 0v3.586l2.982 2.982a1 1 0 0 1 0 1.414Z"/>
                                        </svg>
                                    </div>
                                    <input type="time" id="endTime" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tambahkan ini di dalam form filter, setelah Time Range Picker -->
                <div class="md:col-span-2">
                    <label for="categories" class="block mb-2 text-sm font-medium text-gray-700">Filter Kategori</label>
                    <select id="categories" data-te-select-init multiple 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <optgroup label="Keamanan">
                            <option value="motion">Motion</option>
                            <option value="status">Status Keamanan</option>
                            <option value="fan">Fan</option>
                        </optgroup>
                        <optgroup label="Perangkat">
                            <option value="servo-status">Servo Status</option>
                            <option value="last-access">Last Access</option>
                            <option value="device">Device Status</option>
                        </optgroup>
                        <optgroup label="Kontrol">
                            <option value="restart-esp">Restart ESP</option>
                            <option value="restart-wemos">Restart Wemos</option>
                        </optgroup>
                        <optgroup label="Sensor">
                            <option value="rfid">RFID</option>
                            <option value="dht">DHT</option>
                            <option value="mpu">MPU</option>
                        </optgroup>
                        <optgroup label="Log">
                            <option value="servo-log">Servo Log</option>
                            <option value="system-esp">System ESP</option>
                            <option value="system-wemos">System Wemos</option>
                        </optgroup>
                    </select>
                </div>
                
                <div class="md:col-span-2 flex justify-end space-x-2">
                    <button type="button" id="applyFilter" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        Terapkan Filter
                    </button>
                    <button type="button" id="resetFilter" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200">
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="loading-indicator" class="text-sm px-4 py-2 rounded-md bg-blue-100 text-blue-800">
        <svg class="inline w-4 h-4 me-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Memperbarui data...
    </div>
    
    <div id="reports-container">
        <!-- Data akan diisi oleh AJAX -->
    </div>
    
    <!-- Pagination -->
    <div id="pagination-container" class="flex items-center justify-between mt-4">
        <div>
            <span class="text-sm text-gray-700">
                Menampilkan <span id="pagination-range">0-0</span> dari <span id="pagination-total">0</span> data
            </span>
        </div>
        <div class="inline-flex mt-2 xs:mt-0">
            <button id="prev-page" class="flex items-center justify-center px-3 h-8 text-sm font-medium text-white bg-gray-800 rounded-s hover:bg-gray-900 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-3.5 h-3.5 me-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
                </svg>
                Sebelumnya
            </button>
            <button id="next-page" class="flex items-center justify-center px-3 h-8 text-sm font-medium text-white bg-gray-800 border-0 border-s border-gray-700 rounded-e hover:bg-gray-900 disabled:opacity-50 disabled:cursor-not-allowed">
                Selanjutnya
                <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Modal Detail Data -->
<div id="detailModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-xl font-medium text-gray-900" id="detailModalLabel">
                    Detail Laporan
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="detailModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4" id="modalBodyContent">
                <!-- Modal content akan diisi oleh JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Template untuk tabel reports -->
<template id="reports-template">
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table id="reports-table" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">#</th>
                    <th scope="col" class="px-6 py-3">Tanggal</th>
                    <th scope="col" class="px-6 py-3">Perubahan</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Info Selengkapnya</th>
                </tr>
            </thead>
            <tbody id="reports-body">
                <!-- Rows will be inserted here by JavaScript -->
            </tbody>
        </table>
    </div>
    <div id="no-data-alert" style="display: none;" class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 mt-4 text-center">
        Tidak ada data laporan.
    </div>
</template>

<!-- Template untuk baris report -->
<template id="report-row-template">
    <tr class="bg-white border-b hover:bg-gray-50">
        <td class="px-6 py-4 report-index"></td>
        <td class="px-6 py-4 report-date"></td>
        <td class="px-6 py-4 report-changes"></td>
        <td class="px-6 py-4 report-status"></td>
        <td class="px-6 py-4 report-actions">
            <button class="view-detail text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5">
                Lihat Detail
            </button>
        </td>
    </tr>
</template>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Global variables
        let allReports = [];
        let currentPage = 1;
        const pageSize = 10; // Jumlah item per halaman
        let filteredReports = []; // Menyimpan hasil filter
        let isFilterActive = false; // Flag untuk menandai apakah filter aktif
        let selectedCategories = [];
        
        // Variabel untuk menyimpan instance chart
        let systemOverviewChart = null;
        
        // Toggle filter panel
        document.getElementById('toggleFilterBtn').addEventListener('click', function() {
            const filterPanel = document.getElementById('filterPanel');
            filterPanel.classList.toggle('hidden');
        });
        
        // Event listener for time filter toggle
        const timeToggle = document.getElementById('filterTimeToggle');
        const timeRangePicker = document.getElementById('timeRangePicker');
        const timeToggleStatus = document.getElementById('timeToggleStatus');
        
        timeToggle.addEventListener('change', function() {
            if (this.checked) {
                timeRangePicker.classList.remove('hidden');
                timeToggleStatus.textContent = 'Rentang Waktu';
                
                // Set default times if empty
                if (!document.getElementById('startTime').value) {
                    document.getElementById('startTime').value = '00:00';
                }
                if (!document.getElementById('endTime').value) {
                    document.getElementById('endTime').value = '23:59';
                }
            } else {
                timeRangePicker.classList.add('hidden');
                timeToggleStatus.textContent = 'Semua Waktu';
            }
        });
        
        // Inisialisasi - ambil data pertama kali
        fetchReports();
        
        // Gunakan interval polling yang lebih singkat dan tersembunyi
        setInterval(function() {
            fetchReportsQuietly();
        }, 2000);
        
        // Event listener untuk filter dan pagination
        document.getElementById('applyFilter').addEventListener('click', applyFilters);
        document.getElementById('resetFilter').addEventListener('click', resetFilters);
        document.getElementById('prev-page').addEventListener('click', goToPrevPage);
        document.getElementById('next-page').addEventListener('click', goToNextPage);
        
        // Fungsi untuk halaman sebelumnya
        function goToPrevPage() {
            if (currentPage > 1) {
                currentPage--;
                renderPaginatedReports(false);
                
                // Scroll ke atas tabel
                document.getElementById('reports-table').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
        
        // Fungsi untuk halaman selanjutnya
        function goToNextPage() {
            const totalPages = Math.ceil((filteredReports.length || allReports.length) / pageSize);
            if (currentPage < totalPages) {
                currentPage++;
                renderPaginatedReports(false);
                
                // Scroll ke atas tabel
                document.getElementById('reports-table').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
        
        // Fungsi untuk menerapkan filter
        function applyFilters() {
            const filterDate = document.getElementById('filterDate').value;
            const timeFilterEnabled = document.getElementById('filterTimeToggle').checked;
            const categories = Array.from(document.getElementById('categories').selectedOptions).map(opt => opt.value);
            
            // Set flag filter aktif
            isFilterActive = filterDate || timeFilterEnabled || categories.length > 0;
            selectedCategories = categories;
            
            let filteredData = allReports;
            
            // Filter berdasarkan tanggal dan waktu
            if (filterDate || timeFilterEnabled) {
                filteredData = filteredData.filter(report => {
                    const reportDate = new Date(report.timestamp);
                    
                    // Filter tanggal
                    if (filterDate) {
                        const dateStr = reportDate.toISOString().split('T')[0];
                        if (dateStr !== filterDate) return false;
                    }
                    
                    // Filter waktu
                    if (timeFilterEnabled) {
                        const startTime = document.getElementById('startTime').value;
                        const endTime = document.getElementById('endTime').value;
                        const [startHour, startMinute] = startTime.split(':').map(Number);
                        const [endHour, endMinute] = endTime.split(':').map(Number);
                        
                        const startTotalMinutes = startHour * 60 + startMinute;
                        const endTotalMinutes = endHour * 60 + endMinute;
                        
                        const hour = reportDate.getHours();
                        const minute = reportDate.getMinutes();
                        const totalMinutes = hour * 60 + minute;
                        
                        if (startTotalMinutes <= endTotalMinutes) {
                            if (totalMinutes < startTotalMinutes || totalMinutes > endTotalMinutes) return false;
                        } else {
                            if (totalMinutes < startTotalMinutes && totalMinutes > endTotalMinutes) return false;
                        }
                    }
                    
                    return true;
                });
            }
            
            // Filter berdasarkan kategori
            if (categories.length > 0) {
                filteredData = filteredData.filter(report => {
                    // Deteksi perubahan untuk report ini
                    const changes = detectChanges(report, allReports, allReports.indexOf(report));
                    // Cek apakah ada perubahan yang masuk dalam kategori yang dipilih
                    return changes.some(change => categories.includes(change.badge));
                });
            }
            
            // Update data terfilter
            filteredReports = filteredData;
            
            // Reset ke halaman pertama
            currentPage = 1;
            
            // Update chart
            renderSystemOverviewChart(filteredReports);
            
            // Render data
            renderPaginatedReports(true);
            
            // Tampilkan notifikasi hasil filter
            const filterCount = filteredReports.length;
            const totalCount = allReports.length;
            
            let filterMessage = `Ditemukan ${filterCount} dari ${totalCount} data`;
            if (categories.length > 0) {
                filterMessage += ` dengan kategori: ${categories.join(', ')}`;
            }
            
            showFilterNotification(filterCount === 0 ? 'Tidak ada data yang cocok dengan filter' : filterMessage);
            
            // Update status filter pada chart
            updateChartFilterStatus();
        }
        
        // Fungsi untuk menampilkan notifikasi hasil filter
        function showFilterNotification(message) {
            // Cek apakah notifikasi sudah ada
            let notification = document.getElementById('filter-notification');
            
            // Jika belum ada, buat elemen baru
            if (!notification) {
                notification = document.createElement('div');
                notification.id = 'filter-notification';
                notification.className = 'p-2 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 mt-2';
                document.getElementById('filterPanel').appendChild(notification);
            }
            
            // Isi pesan notifikasi
            notification.textContent = message;
            
            // Hapus notifikasi setelah 5 detik
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
        
        // Fungsi untuk mereset filter
        function resetFilters() {
            document.getElementById('filterForm').reset();
            document.getElementById('filterTimeToggle').checked = false;
            document.getElementById('timeRangePicker').classList.add('hidden');
            document.getElementById('timeToggleStatus').textContent = 'Semua Waktu';
            document.getElementById('categories').selectedIndex = -1; // Reset multiple select
            selectedCategories = [];
            
            // Reset flag filter
            isFilterActive = false;
            
            // Reset ke semua data dan halaman pertama
            filteredReports = allReports;
            currentPage = 1;
            
            // Update chart dengan semua data
            renderSystemOverviewChart(allReports);
            
            // Update chart filter status
            document.getElementById('system-chart-count').textContent = `Total: ${allReports.length} laporan`;
            
            // Render reports
            renderPaginatedReports();
        }
        
        // Fungsi untuk mengambil data report terbaru secara diam-diam
        function fetchReportsQuietly() {
            fetch('/api/reports', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Cek apakah ada data baru
                const hasNewData = checkForNewData(allReports, data);
                
                // Perbarui data global
                allReports = data;
                
                // Jika filter aktif, terapkan filter tanpa mereset halaman
                if (isFilterActive) {
                    // Simpan halaman saat ini
                    const savedPage = currentPage;
                    
                    // Terapkan filter ulang dengan data baru
                    const filterDate = document.getElementById('filterDate').value;
                    const timeFilterEnabled = document.getElementById('filterTimeToggle').checked;
                    let startTime = null;
                    let endTime = null;
                    
                    if (timeFilterEnabled) {
                        startTime = document.getElementById('startTime').value;
                        endTime = document.getElementById('endTime').value;
                        
                        // Convert to minutes for easier comparison
                        const [startHour, startMinute] = startTime.split(':').map(Number);
                        const [endHour, endMinute] = endTime.split(':').map(Number);
                        
                        const startTotalMinutes = startHour * 60 + startMinute;
                        const endTotalMinutes = endHour * 60 + endMinute;
                        
                        filteredReports = allReports.filter(report => {
                            const reportDate = new Date(report.timestamp);
                            
                            // Filter berdasarkan tanggal
                            if (filterDate) {
                                const dateStr = reportDate.toISOString().split('T')[0];
                                if (dateStr !== filterDate) return false;
                            }
                            
                            // Filter berdasarkan rentang waktu
                            if (timeFilterEnabled) {
                                const hour = reportDate.getHours();
                                const minute = reportDate.getMinutes();
                                const totalMinutes = hour * 60 + minute;
                                
                                if (startTotalMinutes <= endTotalMinutes) {
                                    if (totalMinutes < startTotalMinutes || totalMinutes > endTotalMinutes) return false;
                                } else {
                                    if (totalMinutes < startTotalMinutes && totalMinutes > endTotalMinutes) return false;
                                }
                            }
                            
                            return true;
                        });
                    } else {
                        filteredReports = allReports.filter(report => {
                            const reportDate = new Date(report.timestamp);
                            
                            if (filterDate) {
                                const dateStr = reportDate.toISOString().split('T')[0];
                                if (dateStr !== filterDate) return false;
                            }
                            
                            return true;
                        });
                    }
                    
                    // Perbarui chart
                    if (hasNewData) {
                        renderSystemOverviewChart(filteredReports);
                    }
                    
                    // Kembalikan halaman ke posisi sebelumnya
                    currentPage = savedPage;
                    
                    // Periksa apakah halaman saat ini masih valid
                    const totalPages = Math.ceil(filteredReports.length / pageSize);
                    if (currentPage > totalPages && totalPages > 0) {
                        currentPage = totalPages;
                    }
                    
                    // Render ulang dengan flag false untuk tidak mereset halaman
                    renderPaginatedReports(false);
                    
                    // Update filter badge
                    updateChartFilterStatus();
                } else {
                    // Jika tidak ada filter, gunakan semua data
                    filteredReports = allReports;
                    
                    // Update chart dengan semua data
                    if (hasNewData) {
                        renderSystemOverviewChart(allReports);
                        updateReportsIfChanged(false);
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching reports:', error);
            });
        }
        
        // Fungsi untuk memeriksa apakah ada data baru
        function checkForNewData(oldData, newData) {
            if (!oldData || !newData) return false;
            
            // Cek apakah jumlah data berubah
            if (oldData.length !== newData.length) return true;
            
            // Cek apakah ada ID baru
            const oldIds = new Set(oldData.map(item => item.id));
            return newData.some(item => !oldIds.has(item.id));
        }
        
        // Fungsi untuk membandingkan data baru dengan yang sudah ada,
        // dan hanya memperbarui jika ada perubahan
        function updateReportsIfChanged(resetPage = true) {
            // Perbarui data terfilter
            if (!isFilterActive) {
                filteredReports = allReports;
            }
            
            // Hitung total halaman berdasarkan data terfilter
            const totalPages = Math.ceil(filteredReports.length / pageSize);
            
            // Jika halaman saat ini lebih besar dari total halaman, reset ke halaman terakhir
            if (currentPage > totalPages && totalPages > 0 && resetPage) {
                currentPage = totalPages;
            }
            
            // Render ulang halaman dengan data yang sudah difilter
            renderPaginatedReports(resetPage);
        }
        
        // Fungsi untuk mengambil data report terbaru dengan indikator loading
        function fetchReports() {
            document.getElementById('loading-indicator').style.display = 'block';
            
            fetch('/api/reports', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                allReports = data; // Simpan semua data untuk filtering
                filteredReports = data; // Inisialisasi data yang difilter dengan semua data
                
                // Render dashboard chart
                renderSystemOverviewChart(data);
                
                renderPaginatedReports();
                document.getElementById('loading-indicator').style.display = 'none';
            })
            .catch(error => {
                console.error('Error fetching reports:', error);
                document.getElementById('loading-indicator').style.display = 'none';
            });
        }
        
        // Render reports dengan pagination
        function renderPaginatedReports(resetPage = true) {
            // Hitung data untuk halaman saat ini
            const dataToShow = filteredReports.length > 0 ? filteredReports : allReports;
            const totalPages = Math.ceil(dataToShow.length / pageSize);
            
            // Pastikan currentPage tidak melebihi totalPages
            if ((currentPage > totalPages && totalPages > 0) && resetPage) {
                currentPage = totalPages;
            }
            
            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = Math.min(startIndex + pageSize, dataToShow.length);
            const paginatedData = dataToShow.slice(startIndex, endIndex);
            
            // Perbarui informasi pagination
            document.getElementById('pagination-range').textContent = 
                dataToShow.length > 0 ? `${startIndex + 1}-${endIndex}` : '0-0';
            document.getElementById('pagination-total').textContent = dataToShow.length;
            
            // Aktifkan/nonaktifkan tombol pagination
            document.getElementById('prev-page').disabled = currentPage === 1;
            document.getElementById('next-page').disabled = currentPage >= totalPages;
            
            // Render data yang telah dipaginasi
            renderReports(paginatedData);
        }
        
        // Render reports table dengan data yang diterima
        function renderReports(reports) {
            const container = document.getElementById('reports-container');
            
            // Clone template tabel jika belum ada
            if (!document.getElementById('reports-table')) {
                const template = document.getElementById('reports-template');
                container.innerHTML = '';
                container.appendChild(template.content.cloneNode(true));
            }
            
            const tableBody = document.getElementById('reports-body');
            const noDataAlert = document.getElementById('no-data-alert');
            
            // Tampilkan pesan jika tidak ada data
            if (!reports || reports.length === 0) {
                tableBody.innerHTML = '';
                noDataAlert.style.display = 'block';
                document.getElementById('pagination-container').style.display = 'none';
                return;
            }
            
            noDataAlert.style.display = 'none';
            document.getElementById('pagination-container').style.display = 'flex';
            
            // Bersihkan tabel
            tableBody.innerHTML = '';
            
            // Tambahkan baris untuk setiap report dengan indeks mulai dari (currentPage-1) * pageSize + 1
            reports.forEach((report, index) => {
                const template = document.getElementById('report-row-template');
                const row = template.content.cloneNode(true).querySelector('tr');
                
                // Set attributes dan data report
                row.dataset.reportId = report.id;
                row.dataset.reportData = JSON.stringify(report);
                
                // Isi konten baris, indeks dimulai dari indeks halaman saat ini
                row.querySelector('.report-index').textContent = (currentPage - 1) * pageSize + index + 1;
                row.querySelector('.report-date').textContent = formatDate(report.timestamp);
                
                // Isi badges untuk perubahan
                const changesCell = row.querySelector('.report-changes');
                changesCell.innerHTML = '';
                
                // Deteksi perubahan
                const changes = detectChanges(report, allReports, allReports.indexOf(report));
                changes.forEach(change => {
                    const badge = document.createElement('span');
                    
                    // Flowbite badge styles dengan warna khusus untuk setiap jenis
                    const badgeClasses = {
                        'primary': 'bg-blue-100 text-blue-800',
                        'motion': 'bg-yellow-100 text-yellow-800',
                        'status': 'bg-red-100 text-red-800', 
                        'fan': 'bg-green-100 text-green-800',
                        'last-access': 'bg-violet-100 text-violet-800',
                        'servo-status': 'bg-cyan-100 text-cyan-800',
                        'restart-esp': 'bg-orange-100 text-orange-800',
                        'restart-wemos': 'bg-amber-100 text-amber-800',
                        'rfid': 'bg-indigo-100 text-indigo-800',
                        'dht': 'bg-lime-100 text-lime-800',
                        'mpu': 'bg-teal-100 text-teal-800',
                        'servo-log': 'bg-sky-100 text-sky-800',
                        'system-esp': 'bg-rose-100 text-rose-800',
                        'system-wemos': 'bg-pink-100 text-pink-800',
                        'device': 'bg-gray-100 text-gray-800',
                        'light': 'bg-gray-100 text-gray-400'
                    };
                    
                    badge.className = `px-2 py-0.5 rounded text-xs font-medium me-2 ${badgeClasses[change.badge] || badgeClasses['light']}`;
                    badge.textContent = change.type;
                    changesCell.appendChild(badge);
                });
                
                // Isi status
                const statusCell = row.querySelector('.report-status');
                statusCell.innerHTML = '';
                
                if (report.security && report.security.motion) {
                    const motionText = document.createElement('div');
                    motionText.className = 'mb-1';
                    motionText.innerHTML = `<strong>Gerakan:</strong> ${firstUpper(report.security.motion)}`;
                    statusCell.appendChild(motionText);
                }
                
                if (report.security && report.security.status) {
                    const securityStatus = document.createElement('div');
                    securityStatus.className = 'mb-1';
                    securityStatus.innerHTML = `<strong>Keamanan:</strong> ${firstUpper(report.security.status)}`;
                    statusCell.appendChild(securityStatus);
                }
                
                if (report.smartcab && report.smartcab.servo_status) {
                    const servoStatus = document.createElement('div');
                    servoStatus.className = 'mb-1';
                    servoStatus.innerHTML = `<strong>Servo:</strong> ${report.smartcab.servo_status}`;
                    statusCell.appendChild(servoStatus);
                }
                
                // Set action untuk detail
                const viewBtn = row.querySelector('.view-detail');
                viewBtn.onclick = function() {
                    showDetail(report);
                };
                
                tableBody.appendChild(row);
            });
        }
        
        // Fungsi untuk mendeteksi perubahan pada report
        function detectChanges(report, reports, index) {
            const changes = [];
            
            if (index === 0 || !reports[index + 1]) {
                changes.push({ type: 'Data awal', badge: 'primary' });
                return changes;
            }
            
            const prevReport = reports[index + 1]; // Baris sebelumnya (karena data sorted terbaru dulu)
            
            // Security changes
            if (report.security && prevReport.security) {
                if (report.security.motion !== prevReport.security.motion) {
                    changes.push({ type: 'Motion', badge: 'motion' });
                }
                
                if (report.security.status !== prevReport.security.status) {
                    changes.push({ type: 'Status', badge: 'status' });
                }
                
                if (report.security.fan !== prevReport.security.fan) {
                    changes.push({ type: 'Fan', badge: 'fan' });
                }
            }
            
            // Smartcab changes
            if (report.smartcab && prevReport.smartcab) {
                if (report.smartcab.last_access !== prevReport.smartcab.last_access) {
                    changes.push({ type: 'Last Access', badge: 'last-access' });
                }
                
                if (report.smartcab.servo_status !== prevReport.smartcab.servo_status) {
                    changes.push({ type: 'Servo Status', badge: 'servo-status' });
                }
            }
            
            // Control changes
            if (report.control && prevReport.control) {
                if (report.control.restartESP !== prevReport.control.restartESP) {
                    changes.push({ type: 'Restart ESP', badge: 'restart-esp' });
                }
                
                if (report.control.restartWemos !== prevReport.control.restartWemos) {
                    changes.push({ type: 'Restart Wemos', badge: 'restart-wemos' });
                }
            }
            
            // Logs changes
            if (report.logs && prevReport.logs) {
                // RFID logs
                if (report.logs.RFID && prevReport.logs.RFID && 
                    JSON.stringify(report.logs.RFID) !== JSON.stringify(prevReport.logs.RFID)) {
                    changes.push({ type: 'RFID', badge: 'rfid' });
                }
                
                // DHT logs
                if (report.logs.dht && prevReport.logs.dht && 
                    JSON.stringify(report.logs.dht) !== JSON.stringify(prevReport.logs.dht)) {
                    changes.push({ type: 'DHT', badge: 'dht' });
                }
                
                // MPU logs
                if (report.logs.mpu && prevReport.logs.mpu && 
                    JSON.stringify(report.logs.mpu) !== JSON.stringify(prevReport.logs.mpu)) {
                    changes.push({ type: 'MPU', badge: 'mpu' });
                }
                
                // Servo logs
                if (report.logs.servo && prevReport.logs.servo && 
                    JSON.stringify(report.logs.servo) !== JSON.stringify(prevReport.logs.servo)) {
                    changes.push({ type: 'Servo Log', badge: 'servo-log' });
                }
                
                // System ESP logs
                if (report.logs.systemESP !== prevReport.logs.systemESP) {
                    changes.push({ type: 'System ESP', badge: 'system-esp' });
                }
                
                // System Wemos logs
                if (report.logs.systemWemos !== prevReport.logs.systemWemos) {
                    changes.push({ type: 'System Wemos', badge: 'system-wemos' });
                }
            }
            
            // Device changes - general fallback if needed
            if (report.device && prevReport.device && 
                JSON.stringify(report.device) !== JSON.stringify(prevReport.device)) {
                changes.push({ type: 'Device Status', badge: 'device' });
            }
            
            if (changes.length === 0) {
                changes.push({ type: 'Tidak ada perubahan', badge: 'light' });
            }
            
            return changes;
        }
        
        // Utility function to format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).replace(',', '');
        }
        
        // First letter uppercase
        function firstUpper(string) {
            if (!string) return '';
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        
        // Fungsi untuk menampilkan detail dalam modal
        function showDetail(report) {
            let formattedDate = new Date(report.timestamp).toLocaleString('id-ID');
            
            // Security section
            let securityHtml = '';
            if (report.security) {
                securityHtml = `
                    <div class="mb-4">
                        <h5 class="text-lg font-medium text-gray-900 mb-2">Keamanan:</h5>
                        <ul class="space-y-2">
                            <li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Gerakan:</strong> ${report.security.motion || 'N/A'}</li>
                            <li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Status:</strong> ${report.security.status || 'N/A'}</li>
                            <li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Fan:</strong> ${report.security.fan || 'N/A'}</li>
                    </ul>
                    </div>
                `;
            }
            
            // Smartcab section
            let smartcabHtml = '';
            if (report.smartcab) {
                smartcabHtml = `
                    <div class="mb-4">
                        <h5 class="text-lg font-medium text-gray-900 mb-2">Smartcab:</h5>
                        <ul class="space-y-2">
                            <li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Akses Terakhir:</strong> ${report.smartcab.last_access || 'N/A'}</li>
                            <li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Status Servo:</strong> ${report.smartcab.servo_status || 'N/A'}</li>
                            <li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Status Perangkat:</strong> ${report.smartcab.status_device || 'N/A'}</li>
                    </ul>
                    </div>
                `;
            }
            
            // DHT11 section
            let dht11Html = '';
            if (report.dht11) {
                dht11Html = `
                    <div class="mb-4">
                        <h5 class="text-lg font-medium text-gray-900 mb-2">Sensor DHT11:</h5>
                        <ul class="space-y-2">
                            <li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Kelembaban:</strong> ${report.dht11.humidity || 'N/A'}%</li>
                            <li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Suhu:</strong> ${report.dht11.temperature || 'N/A'}°C</li>
                    </ul>
                    </div>
                `;
            }
            
            // Control section
            let controlHtml = '';
            if (report.control) {
                controlHtml = `
                    <div class="mb-4">
                        <h5 class="text-lg font-medium text-gray-900 mb-2">Kontrol:</h5>
                        <ul class="space-y-2">
                            <li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Restart ESP:</strong> ${report.control.restartESP ? 'Ya' : 'Tidak'}</li>
                            <li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Restart Wemos:</strong> ${report.control.restartWemos ? 'Ya' : 'Tidak'}</li>
                        </ul>
                    </div>
                `;
            }
            
            // Device section
            let deviceHtml = '';
            if (report.device) {
                deviceHtml = `
                    <div class="mb-4">
                        <h5 class="text-lg font-medium text-gray-900 mb-2">Status Perangkat:</h5>
                        <ul class="space-y-2">
                            <li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Last Active:</strong> ${report.device.lastActive || 'N/A'}</li>
                            <li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Last Active Wemos:</strong> ${report.device.lastActiveWemos || 'N/A'}</li>
                        </ul>
                    </div>
                `;
            }
            
            // Logs section
            let logsHtml = '';
            if (report.logs) {
                logsHtml = `
                    <div class="mb-4">
                        <h5 class="text-lg font-medium text-gray-900 mb-2">Logs Sistem:</h5>
                        <ul class="space-y-2">
                `;
                
                // RFID
                if (report.logs.RFID) {
                    logsHtml += `<li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">RFID:</strong> ${report.logs.RFID.status || 'N/A'}</li>`;
                }
                
                // DHT
                if (report.logs.dht) {
                    logsHtml += `
                        <li class="p-3 bg-gray-50 rounded-lg">
                            <strong class="text-gray-700">DHT:</strong> ${report.logs.dht.status || 'N/A'}<br>
                            <span class="text-xs text-gray-500">${report.logs.dht.message || ''}</span>
                        </li>
                    `;
                }
                
                // MPU
                if (report.logs.mpu) {
                    logsHtml += `
                        <li class="p-3 bg-gray-50 rounded-lg">
                            <strong class="text-gray-700">MPU:</strong> ${report.logs.mpu.status || 'N/A'}<br>
                            <span class="text-xs text-gray-500">${report.logs.mpu.message || ''}</span>
                        </li>
                    `;
                }
                
                // Servo
                if (report.logs.servo) {
                    logsHtml += `<li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">Servo:</strong> ${report.logs.servo.status || 'N/A'}</li>`;
                }
                
                // System status
                if (report.logs.systemESP) {
                    logsHtml += `<li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">System ESP:</strong> ${report.logs.systemESP || 'N/A'}</li>`;
                }
                
                if (report.logs.systemWemos) {
                    logsHtml += `<li class="p-3 bg-gray-50 rounded-lg"><strong class="text-gray-700">System Wemos:</strong> ${report.logs.systemWemos || 'N/A'}</li>`;
                }
                
                logsHtml += `</ul></div>`;
            }
            
            // Combine all HTML sections
            let detailHtml = `
                <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50">
                    <div class="font-medium">ID: ${report.id}</div>
                    <div>Tanggal Waktu: ${formattedDate}</div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        ${securityHtml}
                        ${smartcabHtml}
                        ${dht11Html}
                    </div>
                    <div>
                        ${controlHtml}
                        ${deviceHtml}
                        ${logsHtml}
                    </div>
                </div>
            `;
            
            document.getElementById('modalBodyContent').innerHTML = detailHtml;
            
            // Gunakan Flowbite modal sebagai gantinya
            const modalElement = document.getElementById('detailModal');
            const modalOptions = {
                placement: 'center',
                backdrop: 'dynamic',
                backdropClasses: 'bg-gray-900/50 fixed inset-0 z-40',
                closable: true
            };
            
            // Buka modal menggunakan Flowbite
            const modal = new Modal(modalElement, modalOptions);
            modal.show();
        }
        
        // Fungsi untuk analisis keseluruhan sistem
        function analyzeSystemOverview(reports) {
            // Siapkan variabel untuk menyimpan hitungan status
            const statusCounts = {
                'motion': 0,          // Motion status
                'status': 0,          // Security status
                'fan': 0,             // Fan status
                'servoStatus': 0,     // Servo status
                'lastAccess': 0,      // Last access
                'restartEsp': 0,      // Restart ESP
                'restartWemos': 0,    // Restart Wemos
                'rfid': 0,            // RFID
                'dht': 0,             // DHT sensor
                'mpu': 0,             // MPU sensor
                'servoLog': 0,        // Servo log
                'systemEsp': 0,       // System ESP
                'systemWemos': 0,     // System Wemos
                'deviceStatus': 0     // Device status
            };
            
            // Total untuk persentase
            let totalCounts = 0;
            
            // Fungsi untuk menghitung perubahan antar report
            function countChanges(report, index) {
                if (index === 0 || index >= reports.length - 1) return [];
                
                const changes = [];
                const prevReport = reports[index + 1]; // Data baris sebelumnya (karena data sorted terbaru dulu)
                
                // Security changes
                if (report.security && prevReport.security) {
                    if (report.security.motion !== prevReport.security.motion) {
                        changes.push('motion');
                    }
                    
                    if (report.security.status !== prevReport.security.status) {
                        changes.push('status');
                    }
                    
                    if (report.security.fan !== prevReport.security.fan) {
                        changes.push('fan');
                    }
                }
                
                // Smartcab changes
                if (report.smartcab && prevReport.smartcab) {
                    if (report.smartcab.last_access !== prevReport.smartcab.last_access) {
                        changes.push('lastAccess');
                    }
                    
                    if (report.smartcab.servo_status !== prevReport.smartcab.servo_status) {
                        changes.push('servoStatus');
                    }
                }
                
                // Control changes
                if (report.control && prevReport.control) {
                    if (report.control.restartESP !== prevReport.control.restartESP) {
                        changes.push('restartEsp');
                    }
                    
                    if (report.control.restartWemos !== prevReport.control.restartWemos) {
                        changes.push('restartWemos');
                    }
                }
                
                // Logs changes
                if (report.logs && prevReport.logs) {
                    // RFID logs
                    if (report.logs.RFID && prevReport.logs.RFID && 
                        JSON.stringify(report.logs.RFID) !== JSON.stringify(prevReport.logs.RFID)) {
                        changes.push('rfid');
                    }
                    
                    // DHT logs
                    if (report.logs.dht && prevReport.logs.dht && 
                        JSON.stringify(report.logs.dht) !== JSON.stringify(prevReport.logs.dht)) {
                        changes.push('dht');
                    }
                    
                    // MPU logs
                    if (report.logs.mpu && prevReport.logs.mpu && 
                        JSON.stringify(report.logs.mpu) !== JSON.stringify(prevReport.logs.mpu)) {
                        changes.push('mpu');
                    }
                    
                    // Servo logs
                    if (report.logs.servo && prevReport.logs.servo && 
                        JSON.stringify(report.logs.servo) !== JSON.stringify(prevReport.logs.servo)) {
                        changes.push('servoLog');
                    }
                    
                    // System ESP logs
                    if (report.logs.systemESP !== prevReport.logs.systemESP) {
                        changes.push('systemEsp');
                    }
                    
                    // System Wemos logs
                    if (report.logs.systemWemos !== prevReport.logs.systemWemos) {
                        changes.push('systemWemos');
                    }
                }
                
                // Device changes - general fallback if needed
                if (report.device && prevReport.device && 
                    JSON.stringify(report.device) !== JSON.stringify(prevReport.device)) {
                    changes.push('deviceStatus');
                }
                
                return changes;
            }
            
            // Periksa setiap laporan untuk mengisi kategori
            reports.forEach((report, index) => {
                const changes = countChanges(report, index);
                
                changes.forEach(change => {
                    statusCounts[change]++;
                    totalCounts++;
                });
                
                // Jika tidak ada perubahan, tambahkan satu ke total untuk report ini
                if (changes.length === 0 && index > 0 && index < reports.length - 1) {
                    totalCounts++;
                }
            });
            
            // Update counter
            document.getElementById('system-chart-count').textContent = 
                `Total: ${reports.length} laporan dengan ${totalCounts} perubahan status`;
            
            // Kembalikan array untuk chart dengan hanya nilai yang bukan nol
            const resultData = [
                statusCounts['fan'],
                statusCounts['status'],
                statusCounts['motion'],
                statusCounts['servoStatus'],
                statusCounts['lastAccess'],
                statusCounts['restartEsp'],
                statusCounts['restartWemos'],
                statusCounts['rfid'],
                statusCounts['dht'],
                statusCounts['mpu'],
                statusCounts['servoLog'],
                statusCounts['systemEsp'],
                statusCounts['systemWemos'],
                statusCounts['deviceStatus']
            ];
            
            // Label yang sesuai dengan resultData
            const resultLabels = [
                'Fan',
                'Status Keamanan',
                'Motion',
                'Servo Status',
                'Last Access',
                'Restart ESP',
                'Restart Wemos',
                'RFID',
                'DHT',
                'MPU',
                'Servo Log',
                'System ESP',
                'System Wemos',
                'Device Status'
            ];
            
            // Warna yang sesuai dengan resultData (sesuai dengan badge colors)
            const resultColors = [
                '#22c55e', // Fan (Hijau)
                '#ef4444', // Status (Merah)
                '#eab308', // Motion (Kuning)
                '#06b6d4', // Servo Status (Cyan)
                '#8b5cf6', // Last Access (Ungu Muda)
                '#f97316', // Restart ESP (Oranye)
                '#f59e0b', // Restart Wemos (Amber)
                '#6366f1', // RFID (Indigo)
                '#84cc16', // DHT (Lime)
                '#14b8a6', // MPU (Teal)
                '#0ea5e9', // Servo Log (Sky)
                '#e11d48', // System ESP (Rose)
                '#ec4899', // System Wemos (Pink)
                '#6b7280'  // Device Status (Gray)
            ];
            
            // Filter untuk menghilangkan kategori dengan nilai nol
            const filteredData = [];
            const filteredLabels = [];
            const filteredColors = [];
            
            for (let i = 0; i < resultData.length; i++) {
                if (resultData[i] > 0) {
                    filteredData.push(resultData[i]);
                    filteredLabels.push(resultLabels[i]);
                    filteredColors.push(resultColors[i]);
                }
            }
            
            return {
                series: filteredData,
                labels: filteredLabels,
                colors: filteredColors
            };
        }
        
        // Render donut chart untuk overview sistem
        function renderSystemOverviewChart(data) {
            const chartInfo = analyzeSystemOverview(data);
            
            const options = {
                series: chartInfo.series,
                chart: {
                    type: 'donut',
                    height: 350
                },
                labels: chartInfo.labels,
                colors: chartInfo.colors,
                legend: {
                    position: 'bottom',
                    fontSize: '14px',
                    offsetY: 10,
                    itemMargin: {
                        horizontal: 5,
                        vertical: 3
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '18px',
                                    fontFamily: 'Helvetica, Arial, sans-serif',
                                    fontWeight: 600,
                                    offsetY: -10
                                },
                                value: {
                                    show: true,
                                    fontSize: '16px',
                                    fontFamily: 'Helvetica, Arial, sans-serif',
                                    fontWeight: 400,
                                    offsetY: 5
                                },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'Total Status',
                                    fontSize: '18px',
                                    fontFamily: 'Helvetica, Arial, sans-serif',
                                    fontWeight: 600,
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 280
                        },
                        legend: {
                            position: 'bottom',
                            offsetY: 0
                        }
                    }
                }],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + "%";
                    }
                }
            };
            
            // Jika chart sudah ada, update data saja
            if (systemOverviewChart) {
                systemOverviewChart.updateOptions({
                    labels: chartInfo.labels,
                    colors: chartInfo.colors
                });
                systemOverviewChart.updateSeries(chartInfo.series);
            } else {
                // Buat chart baru
                systemOverviewChart = new ApexCharts(document.querySelector("#systemOverviewChart"), options);
                systemOverviewChart.render();
            }
        }
        
        // Tambahkan ke fungsi updateChartFilterStatus
        function updateChartFilterStatus() {
            const chartCountElement = document.getElementById('system-chart-count');
            const chartFilterBadge = document.getElementById('chart-filter-badge');
            const chartFilterText = document.getElementById('chart-filter-text');
            const filterDate = document.getElementById('filterDate').value;
            const timeFilterEnabled = document.getElementById('filterTimeToggle').checked;
            
            let filterInfo = [];
            
            if (filterDate) {
                const formattedDate = new Date(filterDate).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                filterInfo.push(`Tanggal: ${formattedDate}`);
            }
            
            if (timeFilterEnabled) {
                const startTime = document.getElementById('startTime').value;
                const endTime = document.getElementById('endTime').value;
                filterInfo.push(`Waktu: ${startTime} - ${endTime}`);
            }
            
            if (selectedCategories.length > 0) {
                filterInfo.push(`Kategori: ${selectedCategories.join(', ')}`);
            }
            
            // Update text pada chart count
            if (filterInfo.length > 0) {
                chartCountElement.innerHTML = `
                    <span class="font-medium">Data Terfilter:</span> ${filteredReports.length} dari ${allReports.length}
                `;
                
                // Tampilkan badge filter
                chartFilterBadge.classList.remove('hidden');
                chartFilterText.textContent = filterInfo.join(' | ');
            } else {
                chartCountElement.textContent = `Total: ${allReports.length} laporan dengan ${analyzeSystemOverview(allReports).series.reduce((a, b) => a + b, 0)} status`;
                
                // Sembunyikan badge filter
                chartFilterBadge.classList.add('hidden');
            }
        }
    });
</script>

</body>
</html>
