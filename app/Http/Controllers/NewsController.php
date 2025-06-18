<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NewsController extends Controller
{
    /**
     * Alamat dasar dari API Flask Anda.
     */
    private $apiBaseUrl = 'http://localhost:5000/api';

    /**
     * Menampilkan halaman dashboard dengan data berita dari API.
     */
    public function index(Request $request)
    {
        $newsData = []; // Siapkan array kosong sebagai nilai default
        $searchStockCode = $request->input('stock_code', '');

        try {
            // ===================================================================
            // PERBAIKAN: Mengganti 'hhttp://' menjadi 'http://' dan menggunakan localhost
            // ===================================================================
            $response = Http::get("{$this->apiBaseUrl}/iqplus/news");

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

                // Format dates for display
                foreach ($newsData as &$item) {
                    if (!empty($item['original_date'])) {
                       try {
                            $item['formatted_date'] = Carbon::parse($item['original_date'])->locale('id')->translatedFormat('l, d F Y H:i');
                       } catch (\Exception $e) {
                            $item['formatted_date'] = $item['original_date']; // Fallback jika format tidak sesuai
                       }
                    }
                }

            } else {
                Log::error("Gagal mengambil berita dari API di dashboard: " . $response->status() . " - " . $response->body());
                session()->flash('error', 'Gagal memuat berita. Status API: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Kesalahan umum saat mengambil berita (Dashboard): " . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
        $searchQuery = $request->input('search', ''); 
        $searchStockCode = $request->input('stock_code', ''); 

        try {
            // PERBAIKAN: Menggunakan localhost
            $response = Http::get("{$this->apiBaseUrl}/iqplus/news");

            if ($response->successful()) {
                $allNewsData = $response->json()['data'] ?? [];
            } else {
                Log::error("Gagal mengambil semua berita dari API: " . $response->status() . " - " . $response->body());
                session()->flash('error', 'Gagal memuat semua berita. Status API: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Kesalahan umum saat mengambil semua berita: " . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        $perPage = 30;
        $currentPage = $request->input('page', 1);
        $collection = new Collection($allNewsData);

        if (!empty($searchQuery)) {
            $searchTerm = strtolower($searchQuery);
            $collection = $collection->filter(function ($item) use ($searchTerm) {
                return str_contains(strtolower($item['title']), $searchTerm);
            });
        }

        if (!empty($searchStockCode)) {
            $stockCodeTerm = strtolower($searchStockCode);
            $collection = $collection->filter(function ($item) use ($stockCodeTerm) {
                return str_contains(strtolower($item['title']), $stockCodeTerm);
            });
        }

        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($collection), $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'newsHtml' => view('news.news_items', ['news' => $paginatedItems])->render(),
                'paginationHtml' => $paginatedItems->links('layouts.pagination')->toHtml()
            ]);
        }

        return view('news.index', [
            'news' => $paginatedItems,
            'searchQuery' => $searchQuery,
            'searchStockCode' => $searchStockCode
        ]);
    }
}
