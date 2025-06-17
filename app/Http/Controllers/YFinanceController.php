<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class YFinanceController extends Controller
{
    /**
     * Membaca dan mem-parsing data emiten dari file CSV.
     *
     * @return Collection
     */
    private function getEmitenData()
    {
        $path = public_path('Daftar_Saham.csv');
        if (!file_exists($path)) {
            return collect(); // Kembalikan koleksi kosong jika file tidak ada
        }

        $file = fopen($path, 'r');
        $data = [];
        
        // Lewati baris header
        $header = fgetcsv($file); 

        while (($row = fgetcsv($file)) !== false) {
            // Pastikan baris memiliki 2 kolom untuk menghindari error
            if (count($row) == 2 && !empty($row[0]) && !empty($row[1])) {
                $data[] = [
                    'Kode' => trim($row[0]),
                    'Nama Perusahaan' => trim($row[1])
                ];
            }
        }

        fclose($file);
        return new Collection($data);
    }

    /**
     * Menampilkan halaman daftar emiten dengan paginasi dan pencarian.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $allEmiten = $this->getEmitenData();
        $searchQuery = $request->input('search', '');

        // Filter data jika ada query pencarian
        if (!empty($searchQuery)) {
            $allEmiten = $allEmiten->filter(function ($item) use ($searchQuery) {
                // Cari berdasarkan Kode atau Nama Perusahaan (case-insensitive)
                return stripos($item['Kode'], $searchQuery) !== false ||
                       stripos($item['Nama Perusahaan'], $searchQuery) !== false;
            });
        }

        // Paginasi manual untuk koleksi data
        $perPage = 25; // Jumlah item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $allEmiten->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $paginatedItems = new LengthAwarePaginator(
            $currentPageItems,
            count($allEmiten),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Jika ini adalah request AJAX, kirim kembali hanya partial HTML
        if ($request->ajax()) {
            $tableHtml = view('yfinance.table_data', ['emiten' => $paginatedItems])->render();
            $paginationHtml = $paginatedItems->links('layouts.pagination')->toHtml();

            return response()->json(['tableHtml' => $tableHtml, 'paginationHtml' => $paginationHtml]);
        }
        
        // Jika request biasa, tampilkan view lengkap
        return view('yfinance.index', ['emiten' => $paginatedItems]);
    }
}