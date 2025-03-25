<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            margin-bottom: 15px;
        }
        .date-range {
            font-size: 12px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
        .page-number {
            text-align: right;
            font-size: 10px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            margin-right: 3px;
            color: #333;
        }
        .badge-motion { background-color: #fef9c3; }
        .badge-status { background-color: #fee2e2; }
        .badge-fan { background-color: #dcfce7; }
        .badge-servo-status { background-color: #cffafe; }
        .badge-last-access { background-color: #ede9fe; }
        .badge-restart-esp { background-color: #ffedd5; }
        .badge-restart-wemos { background-color: #fef3c7; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $title }}</div>
        <div class="subtitle">Laporan Keamanan dan Monitoring System</div>
        <div class="date-range">Periode: {{ $startDate }} - {{ $endDate }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Tanggal</th>
                <th width="20%">Perubahan</th>
                <th width="32%">Status Keamanan</th>
                <th width="30%">Status Perangkat</th>
            </tr>
        </thead>
        <tbody>
            @if(count($reports) > 0)
                @foreach($reports as $index => $report)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ date('d/m/Y H:i', strtotime($report['timestamp'])) }}</td>
                    <td>
                        @php
                            $changes = [];
                            // Detect changes based on your logic
                            // This is a simplified example
                            if(isset($report['security']['motion'])) {
                                $changes[] = ['type' => 'Motion', 'badge' => 'badge-motion'];
                            }
                            if(isset($report['security']['status'])) {
                                $changes[] = ['type' => 'Status', 'badge' => 'badge-status'];
                            }
                            if(isset($report['security']['fan'])) {
                                $changes[] = ['type' => 'Fan', 'badge' => 'badge-fan'];
                            }
                            if(isset($report['smartcab']['servo_status'])) {
                                $changes[] = ['type' => 'Servo', 'badge' => 'badge-servo-status'];
                            }
                            // Add more change types as needed
                        @endphp
                        
                        @foreach($changes as $change)
                            <span class="badge {{ $change['badge'] }}">{{ $change['type'] }}</span>
                        @endforeach
                    </td>
                    <td>
                        @if(isset($report['security']))
                            <div>Gerakan: {{ ucfirst($report['security']['motion'] ?? 'N/A') }}</div>
                            <div>Status: {{ ucfirst($report['security']['status'] ?? 'N/A') }}</div>
                            <div>Fan: {{ $report['security']['fan'] ?? 'N/A' }}</div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if(isset($report['smartcab']))
                            <div>Servo: {{ $report['smartcab']['servo_status'] ?? 'N/A' }}</div>
                            <div>Last Access: {{ $report['smartcab']['last_access'] ?? 'N/A' }}</div>
                        @endif
                        @if(isset($report['dht11']))
                            <div>Suhu: {{ $report['dht11']['temperature'] ?? 'N/A' }}°C</div>
                            <div>Kelembaban: {{ $report['dht11']['humidity'] ?? 'N/A' }}%</div>
                        @endif
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" style="text-align: center">Tidak ada data laporan untuk periode yang dipilih</td>
                </tr>
            @endif
        </tbody>
    </table>
    
    <div class="footer">
        <p>Total laporan: {{ $totalReports }}</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
    
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Arial");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
