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

            // Cek perubahan pada setiap kategori data
            $securityChanged = false;
            $smartcabChanged = false;
            $controlChanged = false;
            $logsChanged = false;
            $dht11Changed = false;
            $deviceChanged = false;
            $hasChanges = false;

            if ($lastEntry === null) {
                // Jika belum ada data, anggap semua kategori berubah (kecuali yang dikecualikan)
                $securityChanged = !empty($securityData);
                $smartcabChanged = !empty($smartcabData);
                $controlChanged = !empty($controlData);
                $logsChanged = !empty($logsData);
                
                // Untuk DHT11 dan Device, kita hanya anggap berubah jika ada data selain yang dikecualikan
                $dht11Changed = !empty($dht11Data) && $this->hasNonTrivialDHT11Data($dht11Data);
                $deviceChanged = !empty($deviceData) && $this->hasNonTrivialDeviceData($deviceData);
                
                $hasChanges = $securityChanged || $smartcabChanged || $controlChanged || 
                              $logsChanged || $dht11Changed || $deviceChanged;
            } else {
                // Cek perubahan pada setiap kategori
                $securityChanged = $this->hasDataChanged($lastEntry['security'] ?? [], $securityData);
                $smartcabChanged = $this->hasDataChanged($lastEntry['smartcab'] ?? [], $smartcabData);
                $controlChanged = $this->hasDataChanged($lastEntry['control'] ?? [], $controlData);
                $logsChanged = $this->hasDataChanged($lastEntry['logs'] ?? [], $logsData);
                
                // Untuk dht11 dan device kita periksa secara khusus, mengabaikan field yang sering berubah
                $dht11Changed = $this->hasNonTrivialDHT11Changes($lastEntry['dht11'] ?? [], $dht11Data);
                $deviceChanged = $this->hasNonTrivialDeviceChanges($lastEntry['device'] ?? [], $deviceData);
                
                $hasChanges = $securityChanged || $smartcabChanged || $controlChanged || 
                              $logsChanged || $dht11Changed || $deviceChanged;
            }

            // Proses setiap perubahan secara terpisah
            $changesMade = false;
            
            // Buat template data lengkap
            $fullData = [
                    'security' => $securityData,
                    'smartcab' => $smartcabData,
                    'control' => $controlData,
                'logs' => $logsData,
                'dht11' => $dht11Data,
                'device' => $deviceData
            ];
            
            // Simpan setiap perubahan secara terpisah dengan data lengkap
            if ($securityChanged && !empty($securityData)) {
                $newEntry = $fullData;
                $newEntry['id'] = Str::uuid()->toString();
                $newEntry['timestamp'] = now()->toIso8601String();
                $newEntry['change_type'] = 'security'; // Tambahkan informasi apa yang berubah
                
                $historyData[] = $newEntry;
                event(new ReportUpdated($newEntry, 'security'));
                $this->info('Perubahan terdeteksi pada security, ID: ' . $newEntry['id']);
                $changesMade = true;
            }
            
            if ($smartcabChanged && !empty($smartcabData)) {
                $newEntry = $fullData;
                $newEntry['id'] = Str::uuid()->toString();
                $newEntry['timestamp'] = now()->toIso8601String();
                $newEntry['change_type'] = 'smartcab';
                
                $historyData[] = $newEntry;
                event(new ReportUpdated($newEntry, 'smartcab'));
                $this->info('Perubahan terdeteksi pada smartcab, ID: ' . $newEntry['id']);
                $changesMade = true;
            }
            
            if ($controlChanged && !empty($controlData)) {
                $newEntry = $fullData;
                $newEntry['id'] = Str::uuid()->toString();
                $newEntry['timestamp'] = now()->toIso8601String();
                $newEntry['change_type'] = 'control';
                
                $historyData[] = $newEntry;
                event(new ReportUpdated($newEntry, 'control'));
                $this->info('Perubahan terdeteksi pada control, ID: ' . $newEntry['id']);
                $changesMade = true;
            }
            
            if ($logsChanged && !empty($logsData)) {
                $newEntry = $fullData;
                $newEntry['id'] = Str::uuid()->toString();
                $newEntry['timestamp'] = now()->toIso8601String();
                $newEntry['change_type'] = 'logs';
                
                $historyData[] = $newEntry;
                event(new ReportUpdated($newEntry, 'logs'));
                $this->info('Perubahan terdeteksi pada logs, ID: ' . $newEntry['id']);
                $changesMade = true;
            }
            
            if ($dht11Changed && !empty($dht11Data)) {
                $newEntry = $fullData;
                $newEntry['id'] = Str::uuid()->toString();
                $newEntry['timestamp'] = now()->toIso8601String();
                $newEntry['change_type'] = 'dht11';
                
                $historyData[] = $newEntry;
                event(new ReportUpdated($newEntry, 'dht11'));
                $this->info('Perubahan signifikan terdeteksi pada dht11, ID: ' . $newEntry['id']);
                $changesMade = true;
            }
            
            if ($deviceChanged && !empty($deviceData)) {
                $newEntry = $fullData;
                $newEntry['id'] = Str::uuid()->toString();
                $newEntry['timestamp'] = now()->toIso8601String();
                $newEntry['change_type'] = 'device';
                
                $historyData[] = $newEntry;
                event(new ReportUpdated($newEntry, 'device'));
                $this->info('Perubahan signifikan terdeteksi pada device, ID: ' . $newEntry['id']);
                $changesMade = true;
            }
            
            // Jika ada perubahan yang disimpan, update file
            if ($changesMade) {
                Storage::put('reports.json', json_encode($historyData, JSON_PRETTY_PRINT));
                $this->info('Semua perubahan berhasil disimpan.');
            } else {
                $this->info('Tidak ada perubahan signifikan pada data, data tidak disimpan');
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

    /**
     * Memeriksa perubahan pada data dht11 selain humidity dan temperature
     */
    private function hasNonTrivialDHT11Changes($oldData, $newData)
    {
        // Buat salinan data untuk perbandingan
        $oldDataCompare = is_array($oldData) ? $oldData : [];
        $newDataCompare = is_array($newData) ? $newData : [];
        
        // Hapus field yang sering berubah
        if (isset($oldDataCompare['humidity'])) {
            unset($oldDataCompare['humidity']);
        }
        if (isset($oldDataCompare['temperature'])) {
            unset($oldDataCompare['temperature']);
        }
        if (isset($newDataCompare['humidity'])) {
            unset($newDataCompare['humidity']);
        }
        if (isset($newDataCompare['temperature'])) {
            unset($newDataCompare['temperature']);
        }
        
        // Bandingkan data yang tersisa
        return json_encode($oldDataCompare) !== json_encode($newDataCompare);
    }
    
    /**
     * Memeriksa apakah DHT11 data memiliki field selain yang dikecualikan
     */
    private function hasNonTrivialDHT11Data($data)
    {
        $dataCopy = is_array($data) ? $data : [];
        
        // Hapus field yang sering berubah
        if (isset($dataCopy['humidity'])) {
            unset($dataCopy['humidity']);
        }
        if (isset($dataCopy['temperature'])) {
            unset($dataCopy['temperature']);
        }
        
        // Periksa apakah masih ada data lain
        return !empty($dataCopy);
    }
    
    /**
     * Memeriksa perubahan pada data device selain lastActive dan lastActiveWemos
     */
    private function hasNonTrivialDeviceChanges($oldData, $newData)
    {
        // Buat salinan data untuk perbandingan
        $oldDataCompare = is_array($oldData) ? $oldData : [];
        $newDataCompare = is_array($newData) ? $newData : [];
        
        // Hapus field yang sering berubah
        if (isset($oldDataCompare['lastActive'])) {
            unset($oldDataCompare['lastActive']);
        }
        if (isset($oldDataCompare['lastActiveWemos'])) {
            unset($oldDataCompare['lastActiveWemos']);
        }
        if (isset($newDataCompare['lastActive'])) {
            unset($newDataCompare['lastActive']);
        }
        if (isset($newDataCompare['lastActiveWemos'])) {
            unset($newDataCompare['lastActiveWemos']);
        }
        
        // Bandingkan data yang tersisa
        return json_encode($oldDataCompare) !== json_encode($newDataCompare);
    }
    
    /**
     * Memeriksa apakah Device data memiliki field selain yang dikecualikan
     */
    private function hasNonTrivialDeviceData($data)
    {
        $dataCopy = is_array($data) ? $data : [];
        
        // Hapus field yang sering berubah
        if (isset($dataCopy['lastActive'])) {
            unset($dataCopy['lastActive']);
        }
        if (isset($dataCopy['lastActiveWemos'])) {
            unset($dataCopy['lastActiveWemos']);
        }
        
        // Periksa apakah masih ada data lain
        return !empty($dataCopy);
    }
}