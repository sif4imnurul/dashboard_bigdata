<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // <-- Import Http Client

class NewsController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data berita dari API.
     */
    public function index()
    {
        $newsData = []; // Siapkan array kosong sebagai default

        try {
            // Lakukan panggilan GET ke API Anda
            $response = Http::get('http://localhost:5000/api/iqplus/news');

            if ($response->successful()) {
                $allData = $response->json()['data'] ?? [];
                // Ambil maksimal 5 item berita
                $newsData = array_slice($allData, 0, 5);
            }
        } catch (\Exception $e) {
            // Logging jika diperlukan
            // Log::error("Gagal mengambil berita dari API: " . $e->getMessage());
        }

        return view('dashboard', ['news' => $newsData]);
    }
}