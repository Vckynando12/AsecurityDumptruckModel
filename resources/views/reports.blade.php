<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Laporan Keamanan dan Monitoring</h2>

    @if(count($reports) > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Gerakan</th>
                    <th>Status Keamanan</th>
                    <th>Akses Terakhir</th>
                    <th>Status Servo</th>
                    <th>Status Perangkat</th>
                    <th>Kelembaban</th>
                    <th>Suhu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $key => $report)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $report['tanggal'] }}</td> <!-- Menampilkan tanggal -->
                        <td>{{ $report['waktu'] }}</td>   <!-- Menampilkan waktu -->
                        <td>{{ ucfirst($report['security']['motion']) }}</td>
                        <td>{{ ucfirst($report['security']['status']) }}</td>
                        <td>{{ $report['smartcab']['last_access'] }}</td>
                        <td>{{ ucfirst($report['smartcab']['servo_status']) }}</td>
                        <td>{{ ucfirst($report['smartcab']['status_device']) }}</td>
                        <td>{{ $report['dht11']['humidity'] }}%</td>
                        <td>{{ $report['dht11']['temperature'] }}°C</td>
                    </tr>
                @endforeach
            </tbody>                     
        </table>
    @else
        <div class="alert alert-warning text-center">
            Tidak ada data laporan.
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function loadReports() {
        $.ajax({
            url: "/get-reports",
            type: "GET",
            success: function (data) {
                let html = "";
                data.forEach((report, index) => {
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${report.tanggal}</td>
                            <td>${report.waktu}</td>
                            <td>${report.security.motion.charAt(0).toUpperCase() + report.security.motion.slice(1)}</td>
                            <td>${report.security.status.charAt(0).toUpperCase() + report.security.status.slice(1)}</td>
                            <td>${report.smartcab.last_access}</td>
                            <td>${report.smartcab.servo_status.charAt(0).toUpperCase() + report.smartcab.servo_status.slice(1)}</td>
                            <td>${report.smartcab.status_device.charAt(0).toUpperCase() + report.smartcab.status_device.slice(1)}</td>
                            <td>${report.dht11.humidity}%</td>
                            <td>${report.dht11.temperature}°C</td>
                        </tr>
                    `;
                });
                $("#reportsBody").html(html);
            }
        });
    }

    // Muat data pertama kali
    loadReports();

    // Perbarui setiap 5 detik
    setInterval(loadReports, 5000);
</script>