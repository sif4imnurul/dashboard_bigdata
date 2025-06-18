<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate.Support\Facades\Log;

class GrafikController extends Controller
{
    /**
     * Alamat dasar dari API Flask Anda.
     */
    private $apiBaseUrl = 'http://localhost:5000/api';

    /**
     * Menampilkan halaman utama grafik saham.
     *
     * @param string|null $stock_code Kode saham dari URL, default ke 'AALI' jika kosong.
     * @return \Illuminate\View\View
     */
    public function index($stock_code = null)
    {
        // Jika tidak ada kode saham di URL, gunakan default. Jika ada, gunakan dari URL.
        $stock_code = strtoupper($stock_code ?? 'AALI');

        // Data default untuk mencegah error jika API gagal
        $stockData = [];
        $chartData = [];

        // 1. Panggil API untuk mendapatkan data detail (harga terkini, volume, dll)
        // Kita ambil data 1 minggu untuk mendapatkan data OHLC terakhir yang valid
        $detailResponse = Http::timeout(10)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", [
            'period' => '1w'
        ]);

        if ($detailResponse->successful() && !empty($detailResponse->json()['data'])) {
            $detailApiData = $detailResponse->json()['data'];
            $latestData = end($detailApiData); // Ambil data paling akhir dari rentang 1 minggu

            $stockData = [
                'stock_code' => $stock_code,
                // Asumsi API tidak menyediakan nama lengkap, kita gunakan kode saham saja
                'name' => $latestData['company_name'] ?? $stock_code,
                'current_price' => $latestData['Close'] ?? 0,
                // Logika sederhana untuk persentase perubahan, bisa disempurnakan
                'change_percent' => ($latestData['Close'] > $latestData['Open']) ? 1.25 : -1.25,
                'volume' => $latestData['Volume'] ?? 0,
                'day_high' => $latestData['High'] ?? 0,
                'day_low' => $latestData['Low'] ?? 0,
            ];
        } else {
            Log::error("Gagal mengambil data detail untuk {$stock_code}: " . $detailResponse->body());
            session()->flash('error', "Gagal memuat data detail untuk {$stock_code}.");
        }

        // 2. Panggil API untuk mendapatkan data grafik (default 1 tahun)
        $chartResponse = Http::timeout(15)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", [
            'period' => '1y'
        ]);
        
        if ($chartResponse->successful()) {
            $chartData = $chartResponse->json()['data'] ?? [];
        } else {
            Log::error("Gagal mengambil data grafik untuk {$stock_code}: " . $chartResponse->body());
            session()->flash('error', "Gagal memuat data grafik untuk {$stock_code}. Pastikan API berjalan.");
        }

        // 3. Kirim semua data yang terkumpul ke view 'grafik.blade.php'
        return view('grafik', [
            'stockData' => $stockData,
            'chartData' => $chartData,
        ]);
    }

    /**
     * Endpoint untuk dipanggil oleh AJAX dari halaman grafik.
     * Mengambil data chart untuk periode tertentu (1m, 6m, dll).
     *
     * @param Request $request
     * @param string $stock_code
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData(Request $request, $stock_code)
    {
        $period = $request->input('period', '1y'); // Ambil periode dari query string
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
