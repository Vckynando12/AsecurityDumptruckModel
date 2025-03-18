<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class WelcomeController extends Controller
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function index()
    {
        // Ambil data dari Firebase
        $dht11 = $this->database->getReference('dht11')->getValue() ?? [];
        $security = $this->database->getReference('security')->getValue() ?? [];
        $smartcab = $this->database->getReference('smartcab')->getValue() ?? [];
        $logs = $this->database->getReference('logs')->getValue() ?? [];
        $device = $this->database->getReference('device')->getValue() ?? [];
        $control = $this->database->getReference('control')->getValue() ?? [];

        // Ambil nilai spesifik
        $humidity = $dht11['humidity'] ?? 'N/A';
        $temperature = $dht11['temperature'] ?? 'N/A';
        $motion = $security['motion'] ?? 'N/A';
        $status = $security['status'] ?? 'N/A';
        $fan = $security['fan'] ?? 'N/A';
        $last_access = $smartcab['last_access'] ?? 'N/A';
        $status_device = $smartcab['status_device'] ?? 'N/A';
        $servo_status = $smartcab['servo_status'] ?? 'N/A';
        
        // Status perangkat
        $lastActiveESP = $device['lastActive'] ?? 'N/A';
        $lastActiveWemos = $device['lastActiveWemos'] ?? 'N/A';
        
        // Status logs
        $dhtStatus = $logs['dht']['status'] ?? 'N/A';
        $dhtMessage = $logs['dht']['message'] ?? 'N/A';
        $mpuStatus = $logs['mpu']['status'] ?? 'N/A';
        $mpuMessage = $logs['mpu']['message'] ?? 'N/A';
        $rfidStatus = $logs['RFID']['status'] ?? 'N/A';
        $servoStatus = $logs['servo']['status'] ?? 'N/A';
        $systemESP = $logs['systemESP'] ?? 'N/A';
        $systemWemos = $logs['systemWemos'] ?? 'N/A';
        
        // Control
        $restartESP = $control['restartESP'] ?? false;
        $restartWemos = $control['restartWemos'] ?? false;

        // Kirim data ke tampilan
        return view('welcome', compact(
            'humidity',
            'temperature',
            'motion',
            'status',
            'fan',
            'last_access',
            'status_device',
            'servo_status',
            'lastActiveESP',
            'lastActiveWemos',
            'dhtStatus',
            'dhtMessage',
            'mpuStatus',
            'mpuMessage',
            'rfidStatus',
            'servoStatus',
            'systemESP',
            'systemWemos',
            'restartESP',
            'restartWemos'
        ));
    }
}
