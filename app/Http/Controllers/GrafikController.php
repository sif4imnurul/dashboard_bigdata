<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GrafikController extends Controller
{
    /**
     * Display the stock chart page
     */
    public function index()
    {
        // Sample data untuk grafik - nanti bisa diganti dengan data real dari API atau database
        $stockData = [
            'symbol' => 'S&P',
            'name' => 'S&P 500',
            'current_price' => 4566.48,
            'change_percent' => 1.66,
            'change_amount' => 74.82,
            'previous_close' => 4566.48,
            'day_range' => [4533.94, 4598.53],
            'year_range' => [3233.94, 4598.53],
            'market_cap' => '$40.3 T USD',
            'volume' => '2,924,736',
            'dividend_yield' => '1.43%',
            'pe_ratio' => '31.08',
            'exchange' => 'INDEX'
        ];

        // Sample chart data points
        $chartData = [
            4200, 4220, 4180, 4250, 4300, 4280, 4320, 4350, 4380, 4400,
            4420, 4450, 4480, 4500, 4520, 4540, 4566, 4580, 4600, 4590
        ];

        // Sample news data
        $news = [
            [
                'title' => 'ROBIN SINJOTO TAMAIAH KEPEMILIKAN SAHAM HOD',
                'source' => 'CNN',
                'time' => '1h',
                'excerpt' => '(SPGas, 1821) - Robin Sinjoto selaku Direktur Utama PT Surya Pasific Investment Tbk (SPGI) berthal mengumumkan pengunduran dari jabatannya efektif per tanggal 31 Januari 2024...'
            ],
            [
                'title' => 'MARKET UPDATE: S&P 500 CONTINUES UPWARD TREND',
                'source' => 'Bloomberg',
                'time' => '2h',
                'excerpt' => 'The S&P 500 index continues its upward momentum as investors remain optimistic about economic recovery and corporate earnings growth...'
            ],
            [
                'title' => 'TECH STOCKS LEAD MARKET GAINS',
                'source' => 'Reuters',
                'time' => '3h',
                'excerpt' => 'Technology stocks are leading the market gains today with major companies reporting strong quarterly results and positive guidance...'
            ]
        ];

        return view('grafik', compact('stockData', 'chartData', 'news'));
    }

    /**
     * Get chart data for specific period
     */
    public function getChartData(Request $request)
    {
        $period = $request->input('period', '1d');
        
        // Generate sample data based on period
        $dataPoints = $this->generateChartData($period);
        
        return response()->json([
            'success' => true,
            'data' => $dataPoints,
            'period' => $period
        ]);
    }

    /**
     * Generate sample chart data based on period
     */
    private function generateChartData($period)
    {
        $basePrice = 4566.48;
        $dataPoints = [];
        
        switch ($period) {
            case '1d':
                // 24 data points for 1 day (hourly)
                for ($i = 0; $i < 24; $i++) {
                    $variation = (rand(-50, 50) / 10);
                    $dataPoints[] = round($basePrice + $variation, 2);
                }
                break;
                
            case '5d':
                // 5 data points for 5 days
                for ($i = 0; $i < 5; $i++) {
                    $variation = (rand(-100, 100) / 5);
                    $dataPoints[] = round($basePrice + $variation, 2);
                }
                break;
                
            case '1m':
                // 30 data points for 1 month
                for ($i = 0; $i < 30; $i++) {
                    $variation = (rand(-200, 200) / 3);
                    $dataPoints[] = round($basePrice + $variation, 2);
                }
                break;
                
            case '6m':
                // 26 data points for 6 months (weekly)
                for ($i = 0; $i < 26; $i++) {
                    $variation = (rand(-500, 500) / 2);
                    $dataPoints[] = round($basePrice + $variation, 2);
                }
                break;
                
            case '1y':
                // 52 data points for 1 year (weekly)
                for ($i = 0; $i < 52; $i++) {
                    $variation = (rand(-800, 800));
                    $dataPoints[] = round($basePrice + $variation, 2);
                }
                break;
                
            case '5y':
                // 60 data points for 5 years (monthly)
                for ($i = 0; $i < 60; $i++) {
                    $variation = (rand(-1500, 1500));
                    $dataPoints[] = round($basePrice + $variation, 2);
                }
                break;
                
            case 'max':
            default:
                // 100 data points for max period
                for ($i = 0; $i < 100; $i++) {
                    $variation = (rand(-2000, 2000));
                    $dataPoints[] = round($basePrice + $variation, 2);
                }
                break;
        }
        
        return $dataPoints;
    }

    /**
     * Search stocks (for future implementation)
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        // Sample search results
        $results = [
            [
                'symbol' => 'AAPL',
                'name' => 'Apple Inc.',
                'price' => 175.84,
                'change' => 2.34
            ],
            [
                'symbol' => 'GOOGL',
                'name' => 'Alphabet Inc.',
                'price' => 2847.63,
                'change' => -15.42
            ],
            [
                'symbol' => 'MSFT',
                'name' => 'Microsoft Corporation',
                'price' => 378.91,
                'change' => 5.67
            ]
        ];
        
        // Filter results based on query
        if ($query) {
            $results = array_filter($results, function($stock) use ($query) {
                return stripos($stock['symbol'], $query) !== false || 
                       stripos($stock['name'], $query) !== false;
            });
        }
        
        return response()->json([
            'success' => true,
            'results' => array_values($results)
        ]);
    }
}