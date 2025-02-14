<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Kreait\Firebase\Contract\Database;

class SensorDataController extends Controller
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getData()
    {
        $dht11 = $this->database->getReference('dht11')->getValue() ?? [];
        $security = $this->database->getReference('security')->getValue() ?? [];

        return response()->json([
            'temperature' => $dht11['temperature'] ?? 'N/A',
            'humidity' => $dht11['humidity'] ?? 'N/A',
            'motion' => $security['motion'] ?? 'N/A',
            'status' => $security['status'] ?? 'N/A',
        ]);
    }
} 