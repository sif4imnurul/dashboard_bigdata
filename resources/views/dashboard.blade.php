@extends('layouts.app')

@section('content')
<div class="col-md-10 main-content">
    <div class="search-container">
        <div class="search-bar">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Cari saham & lainnya">
        </div>
    </div>
    
    <div class="content-section">
        <div class="section-title">Berita Saham</div>
        
        <div class="row g-3 horizontal-scroll-row"> 
            @forelse ($news as $item)
                <div class="news-card-wrapper"> 
                    <div class="news-card">
                        <div class="news-title">{{ $item['title'] }}</div>
                        <div class="news-date">{{ $item['original_date'] }}</div>
                        <div class="news-content">
                            {{ $item['summary'] }}
                        </div>
                    </div>
                </div>
            @empty
                <p>Tidak ada berita yang tersedia saat ini.</p>
            @endforelse
        </div>
    </div>
    
    <div class="content-section">
        <div class="row g-3">
            <div class="col-md-8">
                <div class="section-title">
                    Grafik Saham
                    <i class="bi bi-three-dots three-dots"></i>
                </div>
                
                <div class="chart-container">
                    <div class="d-flex align-items-center mb-2">
                        <div class="chart-symbol">S&P</div>
                        <div class="chart-name">S&P 500</div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <div class="chart-price">4,566.48</div>
                            <div class="chart-change">+1.66%</div>
                        </div>
                        <div class="chart-tabs">
                            <div class="chart-tab" data-period="1d">1d</div>
                            <div class="chart-tab" data-period="5d">5d</div>
                            <div class="chart-tab" data-period="1m">1m</div>
                            <div class="chart-tab" data-period="6m">6m</div>
                            <div class="chart-tab" data-period="1y">1y</div>
                            <div class="chart-tab" data-period="5y">5y</div>
                            <div class="chart-tab active" data-period="max">Max</div>
                        </div>
                    </div>
                    
                    <div class="chart-info">
                        Oct 25, 5:26:38PM UTC-4 · INDEXSP · Disclaimer
                    </div>
                    
                    <div class="chart-canvas-container">
                        <canvas id="stockChart"></canvas>
                        <div id="chartTooltip">
                            <div class="tooltip-date">Oct 25, 5:26:38PM</div>
                            <div class="tooltip-value">4,487.90</div>
                        </div>
                    </div>
                    
                    <div class="chart-timeline">
                        <div>Jul 2023</div>
                        <div>Aug 2023</div>
                        <div>Sep 2023</div>
                        <div>Oct 2023</div>
                        <div>Nov 2023</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="section-title">
                    Rincian
                    <i class="bi bi-three-dots three-dots"></i>
                </div>
                
                <div class="details-container">
                    <div class="market-cap-badge">
                        <div class="market-cap-icon"></div>
                        <div>
                            <div class="market-cap-label">Market Cap</div>
                            <div class="market-cap-value">$40.3 T</div>
                        </div>
                    </div>
                    
                    <table class="details-table">
                         <tr>
                            <td>S&P 500</td>
                            <td>
                                <div class="d-flex align-items-center justify-content-end">
                                    <div>4,566.48</div>
                                    <div class="time-badge ms-2">
                                        <i class="bi bi-clock"></i>
                                        24 h
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Previous Close</td>
                            <td>4,566.48</td>
                        </tr>
                        <tr>
                            <td>Day Range</td>
                            <td>4,533.94–4,598.53</td>
                        </tr>
                        <tr>
                            <td>Year Range</td>
                            <td>3,233.94–4,598.53</td>
                        </tr>
                        <tr>
                            <td>Market Cap</td>
                            <td>$40.3 T USD</td>
                        </tr>
                        <tr>
                            <td>Volume</td>
                            <td>2,924,736</td>
                        </tr>
                        <tr>
                            <td>Dividend Yield</td>
                            <td>1.43%</td>
                        </tr>
                        <tr>
                            <td>P/E Ratio</td>
                            <td>31.08</td>
                        </tr>
                        <tr>
                            <td>Previous Close</td>
                            <td>INDEX</td>
                        </tr>
                        <tr>
                            <td>Atribut lainnya</td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection