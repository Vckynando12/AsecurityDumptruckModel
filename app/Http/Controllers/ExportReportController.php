<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExportReportController extends Controller
{
    public function exportPdf(Request $request)
    {
        // Validasi request
        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        // Ambil parameter
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        
        Log::info('Export PDF with date range', [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
        
        // Tambahkan 1 hari ke endDate untuk query "sampai dengan" yang benar
        // Ensure the end date is included by adding one day
        $endDateForQuery = Carbon::parse($endDate)->addDay()->format('Y-m-d');
        
        Log::info('Actual date range for query', [
            'startDate' => $startDate,
            'endDateForQuery' => $endDateForQuery
        ]);

        // Ambil data report dari file JSON
        $jsonPath = storage_path('app/reports.json');
        $reports = [];

        if (File::exists($jsonPath)) {
            Log::info('JSON file exists: ' . $jsonPath);
            $jsonContent = File::get($jsonPath);
            $jsonData = json_decode($jsonContent, true);
            
            if ($jsonData) {
                Log::info('JSON data loaded successfully, count: ' . count($jsonData));
                
                // Debug: Show a few timestamps from the data
                $sampleTimestamps = array_slice(array_column($jsonData, 'timestamp'), 0, 5);
                Log::info('Sample timestamps', ['samples' => $sampleTimestamps]);
                
                // Debug: Check for reports on the specific date (24th)
                $date24th = substr($endDate, 0, 8) . '24'; // Assuming format YYYY-MM-DD
                $reportsOn24th = array_filter($jsonData, function($report) use ($date24th) {
                    return isset($report['timestamp']) && substr($report['timestamp'], 0, 10) === $date24th;
                });
                Log::info('Reports on the 24th', [
                    'date24th' => $date24th,
                    'count' => count($reportsOn24th),
                    'samples' => array_slice(array_column($reportsOn24th, 'timestamp'), 0, 3)
                ]);
                
                // Filter berdasarkan range tanggal
                $filteredReports = array_filter($jsonData, function($report) use ($startDate, $endDateForQuery, $endDate) {
                    if (!isset($report['timestamp'])) {
                        Log::warning('Report missing timestamp', ['report' => json_encode($report)]);
                        return false;
                    }
                    
                    $reportTimestamp = $report['timestamp'];
                    $reportDate = substr($reportTimestamp, 0, 10); // Ambil bagian YYYY-MM-DD saja
                    
                    // Log date comparison for debugging
                    Log::info('Date comparison', [
                        'reportTimestamp' => $reportTimestamp,
                        'reportDate' => $reportDate,
                        'startDate' => $startDate,
                        'endDateForQuery' => $endDateForQuery,
                        'isAfterStart' => $reportDate >= $startDate,
                        'isBeforeEnd' => $reportDate <= $endDate // Compare with original endDate for inclusive
                    ]);
                    
                    // Use inclusive comparison for the end date (≤ instead of <)
                    // With endDateForQuery being endDate + 1 day, proper check is: reportDate < endDateForQuery
                    $isInRange = $reportDate >= $startDate && $reportDate < $endDateForQuery;
                    
                    // Explicitly check for the end date to make sure it's included
                    if (substr($endDate, 0, 10) === $reportDate) {
                        Log::info('Found report on end date', ['timestamp' => $reportTimestamp]);
                        return true;
                    }
                    
                    return $isInRange;
                });
                
                Log::info('Filtered reports count: ' . count($filteredReports));
                
                // Urutkan data berdasarkan timestamp (terbaru dulu)
                usort($filteredReports, function($a, $b) {
                    $timeA = strtotime($a['timestamp']);
                    $timeB = strtotime($b['timestamp']);
                    return $timeB - $timeA; // Descending order
                });
                
                $reports = array_values($filteredReports); // Reset array keys
                Log::info('Final reports count: ' . count($reports));
            } else {
                Log::error('Failed to decode JSON data', [
                    'fileSize' => strlen($jsonContent),
                    'jsonError' => json_last_error_msg()
                ]);
            }
        } else {
            Log::error('JSON file not found: ' . $jsonPath);
        }

        // Preparation data untuk PDF
        $data = [
            'title' => 'Laporan Keamanan dan Monitoring',
            'date' => date('d/m/Y'),
            'startDate' => Carbon::parse($startDate)->format('d/m/Y'),
            'endDate' => Carbon::parse($endDate)->format('d/m/Y'),
            'reports' => $reports,
            'totalReports' => count($reports),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('export.reports-pdf', $data);
        
        // Set paper dan orientasi
        $pdf->setPaper('a4', 'landscape');

        // Download PDF dengan nama file yang berisi range tanggal
        return $pdf->download('laporan_' . $startDate . '_sampai_' . $endDate . '.pdf');
    }
}