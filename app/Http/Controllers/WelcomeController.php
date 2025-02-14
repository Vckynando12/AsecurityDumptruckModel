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

        // Ambil nilai spesifik
        $humidity = $dht11['humidity'] ?? 'N/A';
        $temperature = $dht11['temperature'] ?? 'N/A';
        $motion = $security['motion'] ?? 'N/A';
        $status = $security['status'] ?? 'N/A';

        // Kirim data ke tampilan
        return view('welcome', compact(
            'humidity',
            'temperature',
            'motion',
            'status'
        ));
    }
}
