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
        .badge-sensor { background-color: #dbeafe; }
        .badge-info { background-color: #e0e7ff; }
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
                            $prevReport = isset($reports[$index+1]) ? $reports[$index+1] : null;
                            
                            // Security changes - only show if there's an actual change from previous value
                            if(isset($report['security'])) {
                                // Motion detection
                                if(isset($report['security']['motion'])) {
                                    $currentMotion = $report['security']['motion'];
                                    $prevMotion = ($prevReport && isset($prevReport['security']['motion'])) 
                                        ? $prevReport['security']['motion'] 
                                        : null;
                                        
                                    if($prevMotion !== $currentMotion && $currentMotion != 'none') {
                                        $changes[] = [
                                            'type' => 'Motion: ' . ucfirst($currentMotion), 
                                            'badge' => 'badge-motion'
                                        ];
                                    }
                                }
                                
                                // Security status
                                if(isset($report['security']['status'])) {
                                    $currentStatus = $report['security']['status'];
                                    $prevStatus = ($prevReport && isset($prevReport['security']['status'])) 
                                        ? $prevReport['security']['status'] 
                                        : null;
                                        
                                    if($prevStatus !== $currentStatus && !empty($currentStatus)) {
                                        $changes[] = [
                                            'type' => 'Status: ' . ucfirst($currentStatus), 
                                            'badge' => 'badge-status'
                                        ];
                                    }
                                }
                                
                                // Fan status
                                if(isset($report['security']['fan'])) {
                                    $currentFan = $report['security']['fan'];
                                    $prevFan = ($prevReport && isset($prevReport['security']['fan'])) 
                                        ? $prevReport['security']['fan'] 
                                        : null;
                                        
                                    if($prevFan !== $currentFan) {
                                        $changes[] = [
                                            'type' => 'Fan: ' . ucfirst($currentFan), 
                                            'badge' => 'badge-fan'
                                        ];
                                    }
                                }
                            }
                            
                            // SmartCab changes
                            if(isset($report['smartcab'])) {
                                // Servo status
                                if(isset($report['smartcab']['servo_status'])) {
                                    $currentServo = $report['smartcab']['servo_status'];
                                    $prevServo = ($prevReport && isset($prevReport['smartcab']['servo_status'])) 
                                        ? $prevReport['smartcab']['servo_status'] 
                                        : null;
                                        
                                    if($prevServo !== $currentServo && !empty($currentServo)) {
                                        $changes[] = [
                                            'type' => 'Servo: ' . $currentServo, 
                                            'badge' => 'badge-servo-status'
                                        ];
                                    }
                                }
                                
                                // Last access
                                if(isset($report['smartcab']['last_access'])) {
                                    $currentAccess = $report['smartcab']['last_access'];
                                    $prevAccess = ($prevReport && isset($prevReport['smartcab']['last_access'])) 
                                        ? $prevReport['smartcab']['last_access'] 
                                        : null;
                                        
                                    if($prevAccess !== $currentAccess && !empty($currentAccess)) {
                                        $changes[] = [
                                            'type' => 'Access: ' . $currentAccess, 
                                            'badge' => 'badge-last-access'
                                        ];
                                    }
                                }
                            }
                            
                            // Control changes - these are boolean events, so just show them when true
                            if(isset($report['control'])) {
                                if(isset($report['control']['restartESP']) && $report['control']['restartESP'] === true) {
                                    $changes[] = ['type' => 'ESP Restart', 'badge' => 'badge-restart-esp'];
                                }
                                
                                if(isset($report['control']['restartWemos']) && $report['control']['restartWemos'] === true) {
                                    $changes[] = ['type' => 'Wemos Restart', 'badge' => 'badge-restart-wemos'];
                                }
                            }
                            
                            // Sensor changes - for the first report, show initial values
                            if($index === 0 || !$prevReport) {
                                if(isset($report['dht11'])) {
                                    if(isset($report['dht11']['temperature'])) {
                                        $changes[] = [
                                            'type' => 'Suhu: ' . $report['dht11']['temperature'] . '°C', 
                                            'badge' => 'badge-sensor'
                                        ];
                                    }
                                    
                                    if(isset($report['dht11']['humidity'])) {
                                        $changes[] = [
                                            'type' => 'Kelembaban: ' . $report['dht11']['humidity'] . '%', 
                                            'badge' => 'badge-sensor'
                                        ];
                                    }
                                }
                            } else {
                                // For subsequent reports, only show changes in sensor values
                                if(isset($report['dht11']) && isset($prevReport['dht11'])) {
                                    if(isset($report['dht11']['temperature']) && isset($prevReport['dht11']['temperature'])) {
                                        $diff = abs($report['dht11']['temperature'] - $prevReport['dht11']['temperature']);
                                        if($diff >= 1) { // Only show if temperature changed by at least 1 degree
                                            $changes[] = [
                                                'type' => 'Suhu: ' . $report['dht11']['temperature'] . '°C', 
                                                'badge' => 'badge-sensor'
                                            ];
                                        }
                                    }
                                    
                                    if(isset($report['dht11']['humidity']) && isset($prevReport['dht11']['humidity'])) {
                                        $diff = abs($report['dht11']['humidity'] - $prevReport['dht11']['humidity']);
                                        if($diff >= 5) { // Only show if humidity changed by at least 5%
                                            $changes[] = [
                                                'type' => 'Kelembaban: ' . $report['dht11']['humidity'] . '%', 
                                                'badge' => 'badge-sensor'
                                            ];
                                        }
                                    }
                                }
                            }
                            
                            // For first report (most recent), always show current state
                            if($index === 0) {
                                if(empty($changes)) {
                                    $changes[] = ['type' => 'Status saat ini', 'badge' => 'badge-info'];
                                }
                            }
                        @endphp
                        
                        @if(count($changes) > 0)
                            @foreach($changes as $change)
                                <span class="badge {{ $change['badge'] }}">{{ $change['type'] }}</span>
                            @endforeach
                        @else
                            <span class="text-gray-400">Tidak ada perubahan</span>
                        @endif
                    </td>
                    <td>
                        @if(isset($report['security']))
                            <div>
                                <strong>Gerakan:</strong> 
                                @if(isset($report['security']['motion']))
                                    <span style="{{ $report['security']['motion'] == 'detected' ? 'color: #e11d48; font-weight: bold;' : 'color: #22c55e;' }}">
                                        {{ ucfirst($report['security']['motion'] ?? 'Tidak ada') }}
                                    </span>
                                @else
                                    <span>Tidak ada data</span>
                                @endif
                            </div>
                            <div>
                                <strong>Status:</strong> 
                                @if(isset($report['security']['status']))
                                    <span style="{{ $report['security']['status'] == 'danger' ? 'color: #e11d48; font-weight: bold;' : 'color: #22c55e;' }}">
                                        {{ ucfirst($report['security']['status'] ?? 'Normal') }}
                                    </span>
                                @else
                                    <span>Normal</span>
                                @endif
                            </div>
                            <div>
                                <strong>Fan:</strong> 
                                <span style="{{ isset($report['security']['fan']) && $report['security']['fan'] == 'on' ? 'color: #22c55e;' : 'color: #6b7280;' }}">
                                    {{ ucfirst($report['security']['fan'] ?? 'Off') }}
                                </span>
                            </div>
                        @else
                            <div class="text-center">Tidak ada data keamanan</div>
                        @endif
                    </td>
                    <td>
                        @if(isset($report['smartcab']))
                            <div>
                                <strong>Servo:</strong> 
                                <span style="{{ isset($report['smartcab']['servo_status']) && $report['smartcab']['servo_status'] == 'Terbuka' ? 'color: #0ea5e9;' : 'color: #6b7280;' }}">
                                    {{ $report['smartcab']['servo_status'] ?? 'N/A' }}
                                </span>
                            </div>
                            @if(isset($report['smartcab']['last_access']) && !empty($report['smartcab']['last_access']))
                                <div><strong>Akses Terakhir:</strong> {{ $report['smartcab']['last_access'] }}</div>
                            @endif
                        @endif
                        
                        @if(isset($report['dht11']))
                            <div style="margin-top: 4px; padding-top: 4px; border-top: 1px dotted #e5e7eb;">
                                <strong>Sensor DHT11:</strong>
                            </div>
                            <div>
                                <strong>Suhu:</strong> 
                                <span style="{{ isset($report['dht11']['temperature']) && $report['dht11']['temperature'] > 30 ? 'color: #e11d48;' : 'color: #0ea5e9;' }}">
                                    {{ $report['dht11']['temperature'] ?? 'N/A' }}°C
                                </span>
                            </div>
                            <div>
                                <strong>Kelembaban:</strong> {{ $report['dht11']['humidity'] ?? 'N/A' }}%
                            </div>
                        @endif
                        
                        @if(isset($report['control']) && ($report['control']['restartESP'] || $report['control']['restartWemos']))
                            <div style="margin-top: 4px; padding-top: 4px; border-top: 1px dotted #e5e7eb;">
                                <strong>Restart Status:</strong>
                                @if($report['control']['restartESP'])
                                    <span style="color: #f97316;">ESP restarted</span>
                                @endif
                                @if($report['control']['restartWemos'])
                                    <span style="color: #f59e0b;">Wemos restarted</span>
                                @endif
                            </div>
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
