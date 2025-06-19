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
     * Menggunakan endpoint /timeseries sesuai app.py
     */
    private function getStockDetails($stock_code)
    {
        // **FIX**: Memanggil endpoint yang benar dengan parameter yang benar
        $response = Http::timeout(10)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", ['period' => '5d']);

        if (!$response->successful() || empty($response->json()['data'])) {
            Log::error("Gagal mengambil data detail untuk {$stock_code} dari endpoint /timeseries: " . $response->body());
            session()->flash('error', "Data detail untuk saham {$stock_code} tidak ditemukan.");
            return [];
        }

        $timeSeries = $response->json()['data'];
        $latestData = end($timeSeries);
        $previousData = count($timeSeries) > 1 ? $timeSeries[count($timeSeries) - 2] : $latestData;

        // Perhitungan persentase perubahan yang akurat
        $change = ($latestData['Close'] ?? 0) - ($previousData['Close'] ?? 0);
        $changePercent = ($previousData['Close'] ?? 0) > 0 ? ($change / $previousData['Close']) * 100 : 0;

        // Mendapatkan nama perusahaan dari data, jika ada
        $companyName = 'Nama Perusahaan Tidak Tersedia';
        if (!empty($timeSeries)) {
             // Coba cari field company_name di data terakhir
            if (isset($latestData['company_name'])) {
                $companyName = $latestData['company_name'];
            } else {
                // Jika tidak ada, coba ambil dari item pertama (fallback)
                $firstData = $timeSeries[0];
                if (isset($firstData['company_name'])) {
                    $companyName = $firstData['company_name'];
                } else {
                    $companyName = $stock_code; // Default ke kode saham jika tidak ada sama sekali
                }
            }
        }


        return [
            'stock_code' => $stock_code,
            'name' => $companyName,
            'current_price' => $latestData['Close'] ?? 0,
            'change_percent' => $changePercent,
            'volume' => $latestData['Volume'] ?? 0,
            'day_high' => $latestData['High'] ?? 0,
            'day_low' => $latestData['Low'] ?? 0,
        ];
    }

    /**
     * Helper untuk mengambil data grafik untuk tampilan awal.
     * Menggunakan endpoint /timeseries sesuai app.py
     */
    private function getChartDataForView($stock_code)
    {
        // **FIX**: Memanggil endpoint yang benar dengan parameter yang benar
        $response = Http::timeout(15)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", ['period' => '1y']);

        if (!$response->successful()) {
            Log::error("Gagal mengambil data grafik untuk {$stock_code} dari endpoint /timeseries: " . $response->body());
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
     * Menggunakan endpoint /timeseries sesuai app.py
     */
    public function getChartData(Request $request, $stock_code)
    {
        // **FIX**: Menggunakan 'period' sebagai parameter, bukan 'range'
        $period = $request->input('period', '1y'); 
        
        $response = Http::timeout(15)->get("{$this->apiBaseUrl}/stock/{$stock_code}/timeseries", ['period' => $period]);

        return $response->successful()
            ? response()->json($response->json())
            : response()->json(['status' => 'error', 'message' => 'Gagal mengambil data'], 500);
    }
}

/**
     * Helper untuk mengambil data berita.
     */
    private function getNewsData($isDetailView, $stock_code)
    {
        // Parameter pencarian berdasarkan kode saham jika ini adalah halaman detail
        $apiParams = $isDetailView ? ['search' => $stock_code] : [];
        $response = Http::get("{$this->newsApiBaseUrl}/iqplus/news", $apiParams);

        if (!$response->successful()) {
            Log::error("Gagal mengambil data berita: " . $response->body());
            return [];
        }

        // Ambil 10 berita teratas untuk ditampilkan di dasbor
        $newsData = array_slice($response->json()['data'] ?? [], 0, 10);

        // Tambahkan key baru 'formatted_date' untuk tampilan, tanpa mengubah data asli
        foreach ($newsData as &$item) {
            if (!empty($item['original_date'])) {
                try {
                    // Gunakan Carbon::parse() yang lebih fleksibel terhadap berbagai format tanggal
                    $item['formatted_date'] = Carbon::parse($item['original_date'])->locale('id')->translatedFormat('l, d F Y H:i');
                } catch (\Exception $e) {
                    // Jika gagal parsing, gunakan tanggal asli sebagai fallback
                    $item['formatted_date'] = $item['original_date'];
                    Log::warning("Gagal mem-parsing tanggal berita: " . $item['original_date']);
                }
            } else {
                 $item['formatted_date'] = 'Tanggal tidak tersedia';
            }
        }
        return $newsData;
    }