<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use App\Events\ReportUpdated;

class FetchFirebaseData extends Command
{
    protected $signature = 'firebase:fetch';
    protected $description = 'Ambil data dari Firebase jika ada perubahan';

    public function handle()
    {
        try {
            // Inisialisasi Firebase
            $firebase = (new Factory)
                ->withServiceAccount(storage_path('app/smartcab-8bb42-firebase-adminsdk-fbsvc-de33a8e45b.json'))
                ->withDatabaseUri(env('FIREBASE_DATABASE_URL'))
                ->createDatabase();

            // Ambil data terbaru dari Firebase
            $securityData = $firebase->getReference('security')->getValue() ?? [];
            $smartcabData = $firebase->getReference('smartcab')->getValue() ?? [];
            $dht11Data = $firebase->getReference('dht11')->getValue() ?? [];
            $controlData = $firebase->getReference('control')->getValue() ?? [];
            $deviceData = $firebase->getReference('device')->getValue() ?? [];
            $logsData = $firebase->getReference('logs')->getValue() ?? [];

            // Baca history data yang sudah ada
            $historyData = [];
            if (Storage::exists('reports.json')) {
                $historyData = json_decode(Storage::get('reports.json'), true) ?: [];
                if (!is_array($historyData)) {
                    $historyData = [];
                }
            }

            // Ambil data terakhir jika ada
            $lastEntry = !empty($historyData) ? end($historyData) : null;

            // Cek apakah ada perubahan pada security, smartcab, control, atau logs
            $hasChanges = false;
            if ($lastEntry === null) {
                $hasChanges = true;
            } else {
                $securityChanged = $this->hasDataChanged($lastEntry['security'] ?? [], $securityData);
                $smartcabChanged = $this->hasDataChanged($lastEntry['smartcab'] ?? [], $smartcabData);
                $controlChanged = $this->hasDataChanged($lastEntry['control'] ?? [], $controlData);
                $logsChanged = $this->hasDataChanged($lastEntry['logs'] ?? [], $logsData);
                
                $hasChanges = $securityChanged || $smartcabChanged || $controlChanged || $logsChanged;
            }

            // Hanya simpan jika ada perubahan
            if ($hasChanges) {
                $newData = [
                    'id' => Str::uuid()->toString(), // Generate ID unik
                    'timestamp' => now()->toIso8601String(),
                    'security' => $securityData,
                    'smartcab' => $smartcabData,
                    'control' => $controlData,
                    'logs' => $logsData
                ];

                if (!empty($dht11Data)) {
                    $newData['dht11'] = $dht11Data;
                }
                
                if (!empty($deviceData)) {
                    $newData['device'] = $deviceData;
                }

                $historyData[] = $newData;
                Storage::put('reports.json', json_encode($historyData, JSON_PRETTY_PRINT));
                $this->info('Data baru tersimpan dengan ID: ' . $newData['id']);

                // Broadcast event untuk realtime update
                event(new ReportUpdated($newData));

                if (isset($securityChanged) && $securityChanged) {
                    $this->info('Perubahan terdeteksi pada security');
                }
                if (isset($smartcabChanged) && $smartcabChanged) {
                    $this->info('Perubahan terdeteksi pada smartcab');
                }
                if (isset($controlChanged) && $controlChanged) {
                    $this->info('Perubahan terdeteksi pada control');
                }
                if (isset($logsChanged) && $logsChanged) {
                    $this->info('Perubahan terdeteksi pada logs');
                }
            } else {
                $this->info('Tidak ada perubahan pada data, data tidak disimpan');
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    private function hasDataChanged($oldData, $newData)
    {
        $oldJson = json_encode($oldData);
        $newJson = json_encode($newData);

        return $oldJson !== $newJson;
    }
}