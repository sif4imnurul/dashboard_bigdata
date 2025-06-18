<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log; // Pastikan ini ada untuk logging
use Carbon\Carbon;

class NewsController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data berita dari API.
     */
    public function index(Request $request)
    {
        $newsData = []; // Siapkan array kosong sebagai nilai default
        $searchStockCode = $request->input('stock_code', '');

        try {
            // Ambil data dari API lokal
            $response = Http::get('http://localhost:5000/api/iqplus/news');

            if ($response->successful()) {
                $allData = $response->json()['data'] ?? [];

                // Filter berdasarkan kode saham jika ada
                if (!empty($searchStockCode)) {
                    $allData = array_filter($allData, function ($item) use ($searchStockCode) {
                        return str_contains(strtolower($item['title']), strtolower($searchStockCode));
                    });
                }

                // Ambil maksimal 10 berita pertama setelah filtering
                $newsData = array_slice($allData, 0, 10);

                // Format dates for display in JavaScript
                foreach ($newsData as &$item) {
                    $item['formatted_date'] = Carbon::createFromFormat('l d/M/Y \a\t H:i', $item['original_date'])->locale('id')->translatedFormat('l, d F Y H:i');
                }

            } else {
                Log::error("Gagal mengambil berita dari API di dashboard: " . $response->status() . " - " . $response->body());
                if ($request->ajax()) {
                    return response()->json(['error' => 'Gagal memuat berita. Status API: ' . $response->status()], $response->status());
                } else {
                    session()->flash('error', 'Gagal memuat berita. Status API: ' . $response->status());
                }
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Koneksi API Berita Error (Dashboard): ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Tidak dapat terhubung ke server API berita. Pastikan server berjalan di localhost:5000'], 500);
            } else {
                session()->flash('error', 'Tidak dapat terhubung ke server API berita. Pastikan server berjalan di localhost:5000');
            }
        } catch (\Exception $e) {
            Log::error("Kesalahan umum saat mengambil berita (Dashboard): " . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            } else {
                session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        // Check if the request is an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'news' => $newsData,
                'searchStockCode' => $searchStockCode
            ]);
        } else {
            // For non-AJAX requests (initial page load)
            return view('dashboard', [
                'news' => $newsData,
                'searchStockCode' => $searchStockCode
            ]);
        }
    }

    /**
     * Menampilkan semua berita dengan fitur pencarian dan paginasi.
     */
    public function showAllNews(Request $request)
    {
        $allNewsData = [];
        // Tangkap parameter 'search' untuk pencarian berita umum (di judul berita)
        $searchQuery = $request->input('search', ''); 
        // Tangkap parameter 'stock_code' untuk pencarian spesifik saham
        $searchStockCode = $request->input('stock_code', ''); 

        try {
            // Panggil API untuk mendapatkan semua berita
            $response = Http::get('http://localhost:5000/api/iqplus/news');
            if ($response->successful()) {
                $allNewsData = $response->json()['data'] ?? [];
            } else {
                Log::error("Gagal mengambil semua berita dari API: " . $response->status() . " - " . $response->body());
                session()->flash('error', 'Gagal memuat semua berita. Status API: ' . $response->status());
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Koneksi API Berita Error (Semua Berita): ' . $e->getMessage());
            session()->flash('error', 'Tidak dapat terhubung ke server API berita. Pastikan server berjalan di localhost:5000');
        } catch (\Exception $e) {
            Log::error("Kesalahan umum saat mengambil semua berita: " . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        $perPage = 30; // Jumlah berita per halaman
        $currentPage = $request->input('page', 1);
        $collection = new Collection($allNewsData);

        // Filter pencarian berita umum (jika ada)
        if (!empty($searchQuery)) {
            $searchTerm = strtolower($searchQuery);
            $collection = $collection->filter(function ($item) use ($searchTerm) {
                return str_contains(strtolower($item['title']), $searchTerm);
            });
        }

        // Filter berdasarkan stock_code (jika ada)
        if (!empty($searchStockCode)) {
            $stockCodeTerm = strtolower($searchStockCode);
            $collection = $collection->filter(function ($item) use ($stockCodeTerm) {
                // Asumsi: Judul berita mengandung kode saham
                return str_contains(strtolower($item['title']), $stockCodeTerm);
            });
        }

        // Ambil item sesuai halaman saat ini
        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        // Buat objek paginator manual
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($collection), $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        // Jika permintaan AJAX, kembalikan dalam format JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'newsHtml' => view('news.news_items', ['news' => $paginatedItems])->render(),
                'paginationHtml' => $paginatedItems->links('layouts.pagination')->toHtml()
            ]);
        }

        return view('news.index', [
            'news' => $paginatedItems,
            'searchQuery' => $searchQuery, // Teruskan juga search query ke view
            'searchStockCode' => $searchStockCode // Teruskan juga stock code ke view
        ]);
    }
}