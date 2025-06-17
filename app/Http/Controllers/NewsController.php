<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
                $newsData = array_slice($allData, 0, 10);
            }
        } catch (\Exception $e) {
            // Logging jika diperlukan
            // Log::error("Gagal mengambil berita dari API: " . $e->getMessage());
        }

        return view('dashboard', ['news' => $newsData]);
    }

    public function showAllNews(Request $request)
    {
        $allNewsData = [];

        try {
            $response = Http::get('http://localhost:5000/api/iqplus/news');
            if ($response->successful()) {
                $allNewsData = $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            // Biarkan data kosong jika API error
        }

        $perPage = 30;
        $currentPage = $request->input('page', 1);
        $collection = new Collection($allNewsData);

        // Apply search filter if search query exists
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = strtolower($request->search);
            $collection = $collection->filter(function ($item) use ($searchTerm) {
                return str_contains(strtolower($item['title']), $searchTerm); 
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

        return view('news.index', ['news' => $paginatedItems]);
    }
}