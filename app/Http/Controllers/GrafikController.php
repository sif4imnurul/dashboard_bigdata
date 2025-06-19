<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GrafikController extends Controller
{
    private $apiBaseUrl = 'http://localhost:5000/api';

    /**
     * Menampilkan halaman dashboard utama yang dinamis.
     *
     * @param string|null $stock_code
     * @return \Illuminate\View\View
     */
    public function index($stock_code = null)
    {
        $isDetailView = $stock_code !== null;
        $stock_code_to_fetch = strtoupper($stock_code ?? 'BBCA');

        Log::info("Menampilkan dasbor untuk kode saham: " . $stock_code_to_fetch);

        $stockData = $this->getStockDetails($stock_code_to_fetch);
        
        // Hanya panggil API lain jika data saham ditemukan
        if (!empty($stockData)) {
            $chartData = $this->getChartDataForView($stock_code_to_fetch);
            $newsData = $this->getNewsData($isDetailView, $stock_code_to_fetch);
        } else {
            // Jika data saham tidak ada, set data lain sebagai array kosong
            $chartData = [];
            $newsData = [];
            if ($isDetailView) { // Hanya tampilkan error jika user benar-benar mencari saham
                session()->flash('error', "Data untuk saham {$stock_code_to_fetch} tidak ditemukan.");
            }
        }

        return view('dashboard', [
            'stockData' => $stockData,
            'chartData' => $chartData,
            'news' => $newsData,
            'isDetailView' => $isDetailView,
            'searchStockCode' => $stock_code,
        ]);
    }

    /**
     * Helper untuk mengambil detail saham dan menghitung perubahan.
     * Menggunakan endpoint /timeseries sesuai app.py
     */
    private function getStockDetails($stock_code)
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", ['period' => '5d']);

            if (!$response->successful() || empty($response->json()['data'])) {
                Log::error("Gagal mengambil data detail untuk {$stock_code} dari API: " . $response->body());
                return [];
            }

            $timeSeries = $response->json()['data'];
            $latestData = end($timeSeries);
            $previousData = count($timeSeries) > 1 ? $timeSeries[count($timeSeries) - 2] : $latestData;

            $change = ($latestData['Close'] ?? 0) - ($previousData['Close'] ?? 0);
            $changePercent = ($previousData['Close'] ?? 0) > 0 ? ($change / $previousData['Close']) * 100 : 0;

            $companyName = $latestData['company_name'] ?? $stock_code;

            return [
                'stock_code' => $stock_code,
                'name' => $companyName,
                'current_price' => $latestData['Close'] ?? 0,
                'change_percent' => $changePercent,
                'volume' => $latestData['Volume'] ?? 0,
                'day_high' => $latestData['High'] ?? 0,
                'day_low' => $latestData['Low'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::critical("Koneksi ke API saham gagal: " . $e->getMessage());
            session()->flash('error', 'Tidak dapat terhubung ke server API. Mohon periksa apakah server API sudah berjalan.');
            return [];
        }
    }

    /**
     * Helper untuk mengambil data grafik untuk tampilan awal.
     * Menggunakan endpoint /timeseries sesuai app.py
     */
    private function getChartDataForView($stock_code)
    {
        $response = Http::timeout(15)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", ['period' => '1y']);

        if (!$response->successful()) {
            Log::error("Gagal mengambil data grafik untuk {$stock_code} dari API: " . $response->body());
            return [];
        }
        return $response->json()['data'] ?? [];
    }

    /**
     * Helper untuk mengambil data berita.
     */
    private function getNewsData($isDetailView, $stock_code)
    {
        $apiParams = $isDetailView ? ['search' => $stock_code] : [];
        $response = Http::get("{$this->apiBaseUrl}/iqplus/news", $apiParams);

        if (!$response->successful()) {
            Log::error("Gagal mengambil data berita: " . $response->body());
            return [];
        }

        $newsData = array_slice($response->json()['data'] ?? [], 0, 10);

        // Tambahkan key baru 'formatted_date' untuk tampilan yang aman
        foreach ($newsData as &$item) {
            if (!empty($item['original_date'])) {
                try {
                    $item['formatted_date'] = Carbon::parse($item['original_date'])->locale('id')->translatedFormat('l, d F Y H:i');
                } catch (\Exception $e) {
                    $item['formatted_date'] = $item['original_date']; // Fallback
                    Log::warning("Gagal mem-parsing tanggal berita: " . $item['original_date']);
                }
            } else {
                $item['formatted_date'] = 'Tanggal tidak tersedia';
            }
        }
        return $newsData;
    }

    /**
     * Endpoint AJAX untuk mengambil data chart berdasarkan periode.
     * Menggunakan endpoint /timeseries sesuai app.py
     */
    public function getChartData(Request $request, $stock_code)
    {
        $period = $request->input('period', '1y'); 
        
        $response = Http::timeout(15)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", ['period' => $period]);

        if (!$response->successful()) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil data'], 500);
        }
        
        // Pastikan untuk mengembalikan data dengan struktur yang diharapkan oleh frontend
        return response()->json($response->json());
    }
}
