<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIChatController extends Controller
{
    private $apiUrl = 'https://api.sideid.tech/v1/ai/gpt-4o-mini';
    private $memory = [];

    public function index()
    {
        return view('ai-chat');
    }

    public function send(Request $request)
    {
        $message = $request->input('message');
        
        // Tambahkan pesan baru ke memory
        $this->memory[] = ['role' => 'user', 'content' => $message];
        
        // Batasi memory untuk menghindari penggunaan memori berlebihan
        if (count($this->memory) > 10) {
            array_shift($this->memory);
        }

        try {
            $response = Http::post($this->apiUrl, [
                'messages' => array_merge(
                    [['role' => 'system', 'content' => 'Nama Anda adalah V AI']],
                    $this->memory
                )
            ]);

            $aiResponse = $response->json()['data']['content'];
            
            // Tambahkan respon AI ke memory
            $this->memory[] = ['role' => 'assistant', 'content' => $aiResponse];

            return response()->json([
                'status' => 'success',
                'message' => $aiResponse,
                'chatId' => $response->json()['data']['chatId'],
                'model' => $response->json()['data']['model']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses permintaan'
            ], 500);
        }
    }
} 