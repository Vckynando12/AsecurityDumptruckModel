<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* CSS untuk memastikan tabel responsif */
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Laporan Keamanan dan Monitoring</h2>

    @php
        use Illuminate\Pagination\LengthAwarePaginator;
        
        // Konversi array ke koleksi
        $reportsCollection = collect($reports);

        // Pagination manual
        $currentPage = request()->get('page', 1);
        $perPage = 10; // Jumlah item per halaman
        $paginatedReports = new LengthAwarePaginator(
            $reportsCollection->forPage($currentPage, $perPage), 
            $reportsCollection->count(), 
            $perPage, 
            $currentPage, 
            ['path' => request()->url()]
        );
    @endphp

    @if($paginatedReports->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Perangkat</th>
                        <th>Status</th>
                        <th>Info Selengkapnya</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paginatedReports as $key => $report)
                        <tr>
                            <td>{{ $paginatedReports->firstItem() + $key }}</td>
                            <td>
                                <!-- Format tanggal sesuai dengan pop-up -->
                                <script>
                                    document.write(new Date("{{ $report['timestamp'] }}").toLocaleString());
                                </script>
                            </td>
                            <td>
                                @if($loop->first)
                                    Gerakan
                                @elseif($report['security']['motion'] !== $reports[$key - 1]['security']['motion'])
                                    Gerakan
                                @elseif($report['security']['status'] !== $reports[$key - 1]['security']['status'])
                                    Status Keamanan
                                @elseif($report['smartcab']['last_access'] !== $reports[$key - 1]['smartcab']['last_access'])
                                    Akses Terakhir
                                @elseif($report['smartcab']['servo_status'] !== $reports[$key - 1]['smartcab']['servo_status'])
                                    Status Servo
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($loop->first)
                                    {{ ucfirst($report['security']['motion']) }}
                                @elseif($report['security']['motion'] !== $reports[$key - 1]['security']['motion'])
                                    {{ ucfirst($report['security']['motion']) }}
                                @elseif($report['security']['status'] !== $reports[$key - 1]['security']['status'])
                                    {{ ucfirst($report['security']['status']) }}
                                @elseif($report['smartcab']['last_access'] !== $reports[$key - 1]['smartcab']['last_access'])
                                    {{ ucfirst($report['smartcab']['last_access']) }}
                                @elseif($report['smartcab']['servo_status'] !== $reports[$key - 1]['smartcab']['servo_status'])
                                    {{ ucfirst($report['smartcab']['servo_status']) }}
                                @else
                                    Tidak ada perubahan
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="showDetail({{ json_encode($report) }})">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>                     
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="d-flex justify-content-center">
            {{ $paginatedReports->links('pagination::bootstrap-4') }}
        </div>
    @else
        <div class="alert alert-warning text-center">
            Tidak ada data laporan.
        </div>
    @endif
</div>

<!-- Modal Detail Data -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyContent">
                <!-- Data akan dimasukkan melalui JavaScript -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showDetail(report) {
        // Format tanggal sesuai dengan locale browser
        let formattedDate = new Date(report.timestamp).toLocaleString();

        let detailHtml = `
            <strong>Tanggal waktu:</strong> ${formattedDate}<br>
            <strong>Gerakan:</strong> ${report.security.motion}<br>
            <strong>Status Keamanan:</strong> ${report.security.status}<br>
            <strong>Akses Terakhir:</strong> ${report.smartcab.last_access}<br>
            <strong>Status Servo:</strong> ${report.smartcab.servo_status}<br>
            <strong>Status Perangkat:</strong> ${report.smartcab.status_device}<br>
            <strong>Kelembaban:</strong> ${report.dht11.humidity}%<br>
            <strong>Suhu:</strong> ${report.dht11.temperature}°C
        `;

        document.getElementById('modalBodyContent').innerHTML = detailHtml;
        let detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    }
</script>

</body>
</html>