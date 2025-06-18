<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Pastikan ini di-import

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
        $stock_code_to_fetch = strtoupper($stock_code ?? 'BBCA'); // Default ke BBCA jika tidak ada kode

        Log::info("Menampilkan dasbor untuk kode saham: " . $stock_code_to_fetch);

        $stockData = $this->getStockDetails($stock_code_to_fetch);
        $chartData = $this->getChartDataForView($stock_code_to_fetch);
        $newsData = $this->getNewsData($isDetailView, $stock_code_to_fetch);

        // Mengirim semua data yang dibutuhkan ke view 'dashboard.blade.php'
        return view('dashboard', [
            'stockData' => $stockData,
            'chartData' => $chartData,
            'news' => $newsData,
            'isDetailView' => $isDetailView,
            'searchStockCode' => $stock_code, // Gunakan stock_code asli dari URL untuk nilai form
        ]);
    }

    /**
     * Helper function untuk mengambil detail saham.
     */
    private function getStockDetails($stock_code)
    {
        $response = Http::timeout(10)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", ['period' => '1w']);

        if (!$response->successful() || empty($response->json()['data'])) {
            Log::error("Gagal mengambil data detail untuk {$stock_code}: " . $response->body());
            session()->flash('error', "Data detail untuk saham {$stock_code} tidak ditemukan.");
            return [];
        }

        $latestData = end($response->json()['data']);
        return [
            'stock_code' => $stock_code,
            'name' => $latestData['company_name'] ?? $stock_code,
            'current_price' => $latestData['Close'] ?? 0,
            'change_percent' => ($latestData['Close'] > $latestData['Open']) ? 1.5 : -1.5,
            'volume' => $latestData['Volume'] ?? 0,
            'day_high' => $latestData['High'] ?? 0,
            'day_low' => $latestData['Low'] ?? 0,
        ];
    }

    /**
     * Helper function untuk mengambil data grafik.
     */
    private function getChartDataForView($stock_code)
    {
        $response = Http::timeout(15)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", ['period' => '1y']);

        if (!$response->successful()) {
            Log::error("Gagal mengambil data grafik untuk {$stock_code}: " . $response->body());
            return [];
        }
        return $response->json()['data'] ?? [];
    }

    /**
     * Helper function untuk mengambil data berita.
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
                    // Perbaikan: Menggunakan parse() untuk format tanggal standar dari API
                    $item['original_date'] = Carbon::parse($item['original_date'])->locale('id')->translatedFormat('l, d F Y H:i');
                } catch (\Exception $e) {
                    // Jika parsing gagal, tampilkan tanggal aslinya saja
                    $item['original_date'] = $item['original_date'];
                }
            }
        }
        return $newsData;
    }

    /**
     * Endpoint untuk dipanggil oleh AJAX dari halaman dasbor.
     * Mengambil data chart untuk periode tertentu.
     */
    public function getChartData(Request $request, $stock_code)
    {
        $period = $request->input('period', '1y');
        $response = Http::timeout(15)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", ['period' => $period]);

        return $response->successful()
            ? response()->json($response->json())
            : response()->json(['status' => 'error', 'message' => 'Gagal mengambil data'], 500);
    }
}

/**
     * Endpoint AJAX untuk mengambil semua data yang dibutuhkan oleh dashboard.
     * Dipanggil saat pengguna melakukan pencarian saham baru.
     */
    public function getDashboardDataAjax($stock_code)
    {
        $stock_code_to_fetch = strtoupper($stock_code);
        $isDetailView = true; // Pencarian AJAX selalu untuk halaman detail

        $stockData = $this->getStockDetails($stock_code_to_fetch);
        $chartData = $this->getChartDataForView($stock_code_to_fetch);
        $newsData = $this->getNewsData($isDetailView, $stock_code_to_fetch);

        // Jika data saham tidak ditemukan setelah pencarian, kembalikan error
        if (empty($stockData)) {
            return response()->json([
                'status' => 'error',
                'message' => "Data untuk kode saham '{$stock_code_to_fetch}' tidak dapat ditemukan."
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'stockData' => $stockData,
            'chartData' => $chartData,
            'news' => $newsData,
            'isDetailView' => $isDetailView,
            'searchStockCode' => $stock_code_to_fetch
        ]);
    }