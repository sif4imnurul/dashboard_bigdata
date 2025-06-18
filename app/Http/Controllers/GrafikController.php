<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // <-- Tambahkan ini

class GrafikController extends Controller
{
    /**
     * Alamat dasar dari API Flask Anda.
     */
    private $apiBaseUrl = 'http://localhost:5000/api';

    /**
     * Menampilkan halaman dashboard utama yang dinamis.
     *
     * @param string|null $stock_code Kode saham dari URL, default ke 'BBCA'.
     * @return \Illuminate\View\View
     */
    public function index($stock_code = null)
    {
        // Tentukan apakah ini halaman detail atau dashboard umum
        $isDetailView = $stock_code !== null;
        $stock_code = strtoupper($stock_code ?? 'BBCA'); // Default ke BBCA jika tidak ada kode

        // Data default untuk mencegah error jika API gagal
        $stockData = [];
        $chartData = [];
        $newsData = [];

        // 1. Ambil data untuk Grafik dan Rincian Saham
        $detailResponse = Http::timeout(10)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", ['period' => '1w']);
        if ($detailResponse->successful() && !empty($detailResponse->json()['data'])) {
            $latestData = end($detailResponse->json()['data']);
            $stockData = [
                'stock_code' => $stock_code,
                'name' => $latestData['company_name'] ?? $stock_code,
                'current_price' => $latestData['Close'] ?? 0,
                'change_percent' => ($latestData['Close'] > $latestData['Open']) ? 1.5 : -1.5, // Logika sederhana
                'volume' => $latestData['Volume'] ?? 0,
                'day_high' => $latestData['High'] ?? 0,
                'day_low' => $latestData['Low'] ?? 0,
            ];
        } else {
            Log::error("Gagal mengambil data detail untuk {$stock_code}: " . $detailResponse->body());
        }

        $chartResponse = Http::timeout(15)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", ['period' => '1y']);
        if ($chartResponse->successful()) {
            $chartData = $chartResponse->json()['data'] ?? [];
        } else {
            Log::error("Gagal mengambil data grafik untuk {$stock_code}: " . $chartResponse->body());
            session()->flash('error', "Gagal memuat data grafik untuk {$stock_code}.");
        }

        // 2. Ambil Data Berita (filter berdasarkan kode saham jika ini halaman detail)
        $newsApiUrl = "{$this->apiBaseUrl}/iqplus/news";
        $apiParams = $isDetailView ? ['search' => $stock_code] : [];
        $newsResponse = Http::get($newsApiUrl, $apiParams);
        if ($newsResponse->successful()) {
            $newsData = array_slice($newsResponse->json()['data'] ?? [], 0, 10);
            
            // ===================================================================
            // PERBAIKAN: Menggunakan Carbon::parse untuk membaca format tanggal dari API
            // ===================================================================
            foreach ($newsData as &$item) {
                if (!empty($item['original_date'])) {
                   try {
                        // Gunakan parse() yang lebih cerdas, bukan createFromFormat()
                        $item['original_date'] = Carbon::parse($item['original_date'])->locale('id')->translatedFormat('l, d F Y H:i');
                   } catch (\Exception $e) {
                        // Jika parsing tetap gagal, tampilkan tanggal aslinya saja
                        $item['original_date'] = $item['original_date']; 
                   }
                }
            }
        } else {
            Log::error("Gagal mengambil data berita: " . $newsResponse->body());
        }

        return view('dashboard', [
            'stockData' => $stockData,
            'chartData' => $chartData,
            'news' => $newsData,
            'isDetailView' => $isDetailView,
            'searchStockCode' => $isDetailView ? $stock_code : '',
        ]);
    }

    /**
     * Endpoint AJAX untuk mengambil data chart untuk periode tertentu.
     */
    public function getChartData(Request $request, $stock_code)
    {
        $period = $request->input('period', '1y');
        $stock_code = strtoupper($stock_code);

        $response = Http::timeout(15)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", [
            'period' => $period,
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['status' => 'error', 'message' => 'Gagal mengambil data'], $response->status());
    }
}
