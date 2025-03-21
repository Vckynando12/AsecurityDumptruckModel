<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function getReports()
    {
        $jsonPath = storage_path('app/reports.json');

        if (!File::exists($jsonPath)) {
            return response()->json([]);
        }

        $jsonData = json_decode(File::get($jsonPath), true);

        if (!$jsonData) {
            return response()->json([]);
        }

        // Urutkan data berdasarkan timestamp (terbaru dulu)
        usort($jsonData, function($a, $b) {
            $timeA = strtotime($a['timestamp']);
            $timeB = strtotime($b['timestamp']);
            return $timeB - $timeA; // Descending order
        });

        foreach ($jsonData as &$report) {
            $carbonTime = Carbon::parse($report['timestamp'])->setTimezone('Asia/Jakarta');
            $report['tanggal'] = $carbonTime->format('d M Y');  // Format tanggal
            $report['waktu'] = $carbonTime->format('H:i:s');     // Format waktu
        }

        return response()->json($jsonData);
    }

    public function getLatestReport()
    {
        $jsonPath = storage_path('app/reports.json');

        if (!File::exists($jsonPath)) {
            return response()->json(null);
        }

        $jsonData = json_decode(File::get($jsonPath), true);

        if (!$jsonData || empty($jsonData)) {
            return response()->json(null);
        }

        // Urutkan data berdasarkan timestamp (terbaru dulu)
        usort($jsonData, function($a, $b) {
            $timeA = strtotime($a['timestamp']);
            $timeB = strtotime($b['timestamp']);
            return $timeB - $timeA; // Descending order
        });

        $latestReport = $jsonData[0];
        $carbonTime = Carbon::parse($latestReport['timestamp'])->setTimezone('Asia/Jakarta');
        $latestReport['tanggal'] = $carbonTime->format('d M Y');
        $latestReport['waktu'] = $carbonTime->format('H:i:s');

        return response()->json($latestReport);
    }

    public function index()
    {
        // Path file JSON (sesuaikan dengan lokasi yang benar)
        $jsonPath = storage_path('app/reports.json');

        // Cek apakah file JSON ada
        if (!File::exists($jsonPath)) {
            return view('reports', ['reports' => []]);
        }

        // Ambil isi file JSON dan decode
        $jsonData = json_decode(File::get($jsonPath), true);

        // Jika tidak ada data, kirim array kosong
        if (!$jsonData) {
            return view('reports', ['reports' => []]);
        }

        // Urutkan data berdasarkan timestamp (terbaru dulu)
        usort($jsonData, function($a, $b) {
            $timeA = strtotime($a['timestamp']);
            $timeB = strtotime($b['timestamp']);
            return $timeB - $timeA; // Descending order
        });

        // Konversi timestamp ke Waktu Indonesia Barat (WIB) dan pisahkan tanggal & waktu
        foreach ($jsonData as &$report) {
            $carbonTime = Carbon::parse($report['timestamp'])->setTimezone('Asia/Jakarta');
            $report['tanggal'] = $carbonTime->format('d M Y');  // Format tanggal
            $report['waktu'] = $carbonTime->format('H:i:s');     // Format waktu
        }

        // Kirim data ke view
        return view('reports', ['reports' => $jsonData]);
    }
}
