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

        // Mengambil data dari controller yang sudah diperbaiki
        $stockData = $this->getStockDetails($stock_code_to_fetch);
        $chartData = $this->getChartDataForView($stock_code_to_fetch);
        $newsData = $this->getNewsData($isDetailView, $stock_code_to_fetch);

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
     * SELALU mengambil dari data harian.
     */
    private function getStockDetails($stock_code)
    {
        // **PERUBAHAN ENDPOINT**: Meminta data spesifik dari koleksi 'daily'.
        // Parameter 'range' diasumsikan untuk membatasi jumlah data yang diambil oleh API.
        $response = Http::timeout(10)->get("{$this->apiBaseUrl}/stock/{$stock_code}/daily", ['range' => '5d']);

        if (!$response->successful() || empty($response->json()['data'])) {
            Log::error("Gagal mengambil data detail untuk {$stock_code} dari endpoint /daily: " . $response->body());
            session()->flash('error', "Data detail untuk saham {$stock_code} tidak ditemukan.");
            return [];
        }

        $timeSeries = $response->json()['data'];
        $latestData = end($timeSeries);
        $previousData = count($timeSeries) > 1 ? $timeSeries[count($timeSeries) - 2] : $latestData;

        // Perhitungan persentase perubahan yang akurat
        $change = ($latestData['Close'] ?? 0) - ($previousData['Close'] ?? 0);
        $changePercent = ($previousData['Close'] ?? 0) > 0 ? ($change / $previousData['Close']) * 100 : 0;

        return [
            'stock_code' => $stock_code,
            'name' => $latestData['company_name'] ?? $stock_code,
            'current_price' => $latestData['Close'] ?? 0,
            'change_percent' => $changePercent,
            'volume' => $latestData['Volume'] ?? 0,
            'day_high' => $latestData['High'] ?? 0,
            'day_low' => $latestData['Low'] ?? 0,
        ];
    }

    /**
     * Helper untuk mengambil data grafik untuk tampilan awal.
     * SELALU mengambil dari data harian.
     */
    private function getChartDataForView($stock_code)
    {
        // **PERUBAHAN ENDPOINT**: Meminta data harian untuk rentang 1 tahun
        $response = Http::timeout(15)->get("{$this->apiBaseUrl}/stock/{$stock_code}/daily", ['range' => '1y']);

        if (!$response->successful()) {
            Log::error("Gagal mengambil data grafik untuk {$stock_code} dari endpoint /daily: " . $response->body());
            return [];
        }
        return $response->json()['data'] ?? [];
    }

    /**
     * Helper untuk mengambil data berita.
     * (Fungsi ini tidak diubah)
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
        foreach ($newsData as &$item) {
            if (!empty($item['original_date'])) {
                try {
                    $item['original_date'] = Carbon::parse($item['original_date'])->locale('id')->translatedFormat('l, d F Y H:i');
                } catch (\Exception $e) {
                    $item['original_date'] = $item['original_date'];
                }
            }
        }
        return $newsData;
    }

    /**
     * Endpoint AJAX untuk mengambil data chart berdasarkan periode.
     * SELALU mengambil dari data harian dengan rentang tertentu.
     */
    public function getChartData(Request $request, $stock_code)
    {
        $period = $request->input('period', '1y'); // '1w', '1m', '1y', dll.
        
        // **PERUBAHAN ENDPOINT**: Meminta data harian dengan rentang yang diminta dari frontend
        $response = Http::timeout(15)->get("{$this->apiBaseUrl}/stock/{$stock_code}/daily", ['range' => $period]);

        return $response->successful()
            ? response()->json($response->json())
            : response()->json(['status' => 'error', 'message' => 'Gagal mengambil data'], 500);
    }
}
