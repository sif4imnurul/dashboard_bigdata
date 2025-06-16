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

            // Cek jika request berhasil (status code 2xx)
            if ($response->successful()) {
                // Ambil bagian 'data' dari JSON response
                $newsData = $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            // Jika API gagal dihubungi (misal: server API mati),
            // biarkan $newsData tetap kosong agar halaman tidak error.
            // Anda bisa juga menambahkan logging di sini:
            // Log::error("Gagal mengambil berita dari API: " . $e->getMessage());
        }
        
        // Kirim data berita ke view 'dashboard'
        return view('dashboard', ['news' => $newsData]);
    }
}