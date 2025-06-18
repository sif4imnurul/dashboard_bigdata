<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FinancialReportController extends Controller
{
    /**
     * Menampilkan halaman laporan keuangan dengan data dari API.
     */
    public function index(Request $request)
    {
        // Ubah default ke data yang tersedia: 2021 tw1
        $year = $request->input('year', '2021');
        $period = $request->input('period', 'tw1');
        $searchQuery = $request->input('search', '');
        $currentPage = $request->input('page', 1);
        $perPage = 9;

        $financialReports = [];
        $totalReports = 0;
        $availableCollections = [];
        $apiUrl = "http://localhost:5000/api/reports/list/{$year}/{$period}";

        try {
            // Log untuk debugging
            Log::info('Fetching financial reports', [
                'url' => $apiUrl,
                'params' => [
                    'page' => $currentPage,
                    'limit' => $perPage,
                    'search' => $searchQuery,
                ]
            ]);

            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->get($apiUrl, [
                    'page' => $currentPage,
                    'limit' => $perPage,
                    'search' => $searchQuery,
                ]);

            Log::info('API Response Status: ' . $response->status());

            if ($response->successful()) {
                $apiData = $response->json();
                
                if (isset($apiData['data'])) {
                    $financialReports = $apiData['data'];
                    $totalReports = $apiData['total_count'] ?? count($financialReports);
                } else {
                    $financialReports = $apiData;
                    $totalReports = count($financialReports);
                }

                // Ambil available collections jika ada
                if (isset($apiData['available_collections'])) {
                    $availableCollections = $apiData['available_collections'];
                }

                Log::info('Reports fetched successfully', [
                    'count' => count($financialReports),
                    'total' => $totalReports,
                    'available_collections' => $availableCollections
                ]);

            } else {
                // Handle 404 atau error lainnya
                $apiData = $response->json();
                
                // Ambil available collections dari error response
                if (isset($apiData['available_collections'])) {
                    $availableCollections = $apiData['available_collections'];
                }
                
                $errorMessage = $apiData['message'] ?? 'API Error: ' . $response->status();
                
                // Jika 404 dan ada available collections, beri saran
                if ($response->status() == 404 && !empty($availableCollections)) {
                    $suggestions = $this->parseAvailableCollections($availableCollections);
                    $errorMessage .= " Tersedia data untuk: " . implode(', ', $suggestions);
                }
                
                Log::error('API Request Failed', [
                    'status' => $response->status(),
                    'message' => $errorMessage,
                    'available_collections' => $availableCollections
                ]);
                
                session()->flash('error', $errorMessage);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('API Connection Error: ' . $e->getMessage());
            session()->flash('error', 'Tidak dapat terhubung ke server API. Pastikan server berjalan di localhost:5000');
            
        } catch (\Exception $e) {
            Log::error('General Error: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        $paginatedReports = new LengthAwarePaginator(
            $financialReports,
            $totalReports,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'reportsHtml' => view('financial_reports.report_items', ['reports' => $paginatedReports])->render(),
                'paginationHtml' => $paginatedReports->links('layouts.pagination')->toHtml(),
                'availableCollections' => $availableCollections,
                'debug' => [
                    'api_url' => $apiUrl,
                    'total_reports' => $totalReports,
                    'current_page' => $currentPage,
                    'available_collections' => $availableCollections
                ]
            ]);
        }

        return view('financial_reports.index', [
            'reports' => $paginatedReports,
            'currentYear' => $year,
            'currentPeriod' => $period,
            'searchQuery' => $searchQuery,
            'availableYears' => $this->getAvailableYears($availableCollections),
            'availablePeriods' => $this->getAvailablePeriods($availableCollections),
            'availableCollections' => $availableCollections,
        ]);
    }

    /**
     * Get available collections from API
     */
    public function getAvailableCollections()
    {
        try {
            $response = Http::timeout(10)->get('http://localhost:5000/api/reports/collections');
            
            if ($response->successful()) {
                return $response->json()['collections'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to get available collections: ' . $e->getMessage());
        }
        
        return [];
    }

    /**
     * Parse available collections to get year/period combinations
     */
    private function parseAvailableCollections($collections)
    {
        $suggestions = [];
        
        foreach ($collections as $collection) {
            // Expected format: processed_reports_YYYY_twX
            if (preg_match('/processed_reports_(\d{4})_(tw\d)/', $collection, $matches)) {
                $year = $matches[1];
                $period = strtoupper($matches[2]);
                $suggestions[] = "{$year} {$period}";
            }
        }
        
        return $suggestions;
    }

    /**
     * Get available years from collections
     */
    private function getAvailableYears($collections = [])
    {
        $years = [];
        
        foreach ($collections as $collection) {
            if (preg_match('/processed_reports_(\d{4})_tw\d/', $collection, $matches)) {
                $years[] = (int)$matches[1];
            }
        }
        
        // Jika tidak ada data dari collections, gunakan default
        if (empty($years)) {
            $years = range(2020, date('Y'));
        } else {
            $years = array_unique($years);
            sort($years);
        }
        
        return $years;
    }

    /**
     * Get available periods from collections for a specific year
     */
    private function getAvailablePeriods($collections = [], $year = null)
    {
        if ($year) {
            $periods = [];
            
            foreach ($collections as $collection) {
                if (preg_match("/processed_reports_{$year}_(tw\d)/", $collection, $matches)) {
                    $periods[] = $matches[1];
                }
            }
            
            if (!empty($periods)) {
                sort($periods);
                return $periods;
            }
        }
        
        // Default periods
        return ['tw1', 'tw2', 'tw3', 'tw4'];
    }

    /**
     * Get available periods for a specific year via AJAX
     */
    public function getPeriodsForYear(Request $request)
    {
        $year = $request->input('year');
        $collections = $this->getAvailableCollections();
        $periods = $this->getAvailablePeriods($collections, $year);
        
        return response()->json([
            'periods' => $periods,
            'collections' => $collections
        ]);
    }

    /**
     * Test API connection and get available data
     */
    public function testApi()
    {
        try {
            // Test basic connection
            $healthResponse = Http::timeout(10)->get('http://localhost:5000/api/health');
            
            // Get available collections
            $collectionsResponse = Http::timeout(10)->get('http://localhost:5000/api/reports/collections');
            
            // Test dengan data yang tersedia (2021 tw1)
            $testDataResponse = Http::timeout(10)->get('http://localhost:5000/api/reports/list/2021/tw1', [
                'page' => 1,
                'limit' => 5
            ]);
            
            return response()->json([
                'health_check' => [
                    'status' => $healthResponse->successful() ? 'success' : 'error',
                    'response' => $healthResponse->json(),
                    'status_code' => $healthResponse->status()
                ],
                'collections' => [
                    'status' => $collectionsResponse->successful() ? 'success' : 'error',
                    'response' => $collectionsResponse->json(),
                    'status_code' => $collectionsResponse->status()
                ],
                'test_data' => [
                    'status' => $testDataResponse->successful() ? 'success' : 'error',
                    'response' => $testDataResponse->json(),
                    'status_code' => $testDataResponse->status()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}