@extends('layouts.app')

@section('content')
<div class="col-md-10 main-content">
    <div class="search-container">
        <div class="search-bar">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Cari saham & lainnya">
        </div>
    </div>
    
    {{-- Stock Chart Section --}}
    <div class="content-section">
        <div class="row g-3">
            {{-- Chart Area --}}
            <div class="col-md-8">
                <div class="section-title">
                    Grafik Saham
                    <i class="bi bi-three-dots three-dots"></i>
                </div>
                
                <div class="chart-container">
                    {{-- Stock Header --}}
                    <div class="d-flex align-items-center mb-2">
                        <div class="chart-symbol">{{ $stockData['symbol'] ?? 'S&P' }}</div>
                        <div class="chart-name">{{ $stockData['name'] ?? 'S&P 500' }}</div>
                    </div>
                    
                    {{-- Price and Controls --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <div class="chart-price">{{ number_format($stockData['current_price'] ?? 4566.48, 2) }}</div>
                            <div class="chart-change {{ ($stockData['change_percent'] ?? 1.66) >= 0 ? 'positive' : 'negative' }}">
                                {{ ($stockData['change_percent'] ?? 1.66) >= 0 ? '+' : '' }}{{ number_format($stockData['change_percent'] ?? 1.66, 2) }}%
                            </div>
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
                    
                    {{-- Chart Info --}}
                    <div class="chart-info">
                        {{ now()->format('M d, g:i:sA') }} UTC+7 · {{ $stockData['exchange'] ?? 'INDEXSP' }} · Disclaimer
                    </div>
                    
                    {{-- Chart Canvas --}}
                    <div class="chart-canvas-container">
                        <canvas id="stockChart"></canvas>
                        <div id="chartTooltip">
                            <div class="tooltip-date">{{ now()->format('M d, g:i:sA') }}</div>
                            <div class="tooltip-value">{{ number_format($stockData['current_price'] ?? 4566.48, 2) }}</div>
                        </div>
                    </div>
                    
                    {{-- Chart Timeline --}}
                    <div class="chart-timeline">
                        <div>Jul 2023</div>
                        <div>Aug 2023</div>
                        <div>Sep 2023</div>
                        <div>Oct 2023</div>
                        <div>Nov 2023</div>
                    </div>
                </div>
            </div>
            
            {{-- Details Sidebar --}}
            <div class="col-md-4">
                <div class="section-title">
                    Rincian
                    <i class="bi bi-three-dots three-dots"></i>
                </div>
                
                <div class="details-container">
                    {{-- Market Cap Badge and Dropdown --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="market-cap-badge mb-0">
                            <div class="market-cap-icon">
                                <i class="bi bi-bar-chart-fill"></i>
                            </div>
                            <div>
                                <div class="market-cap-label">Market Cap</div>
                                <div class="market-cap-value">{{ $stockData['market_cap'] ?? '$40.3 T' }}</div>
                            </div>
                        </div>

                        <div class="dropdown">
                            <button class="btn range-dropdown-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                24 h
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">24 h</a></li>
                                <li><a class="dropdown-item" href="#">7 d</a></li>
                                <li><a class="dropdown-item" href="#">1 m</a></li>
                                <li><a class="dropdown-item" href="#">1 y</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    {{-- Details Table --}}
                    <table class="details-table">
                        <tr>
                            <td>{{ $stockData['name'] ?? 'S&P 500' }}</td>
                            <td>
                                <div class="d-flex align-items-center justify-content-end">
                                    <div>{{ number_format($stockData['current_price'] ?? 4566.48, 2) }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Previous Close</td>
                            <td>{{ number_format($stockData['previous_close'] ?? 4566.48, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Day Range</td>
                            <td>{{ number_format($stockData['day_range'][0] ?? 4533.94, 2) }}–{{ number_format($stockData['day_range'][1] ?? 4598.53, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Year Range</td>
                            <td>{{ number_format($stockData['year_range'][0] ?? 3233.94, 2) }}–{{ number_format($stockData['year_range'][1] ?? 4598.53, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Market Cap</td>
                            <td>{{ $stockData['market_cap'] ?? '$40.3 T USD' }}</td>
                        </tr>
                        <tr>
                            <td>Volume</td>
                            <td>{{ $stockData['volume'] ?? '2,924,736' }}</td>
                        </tr>
                        <tr>
                            <td>Dividend Yield</td>
                            <td>{{ $stockData['dividend_yield'] ?? '1.43%' }}</td>
                        </tr>
                        <tr>
                            <td>P/E Ratio</td>
                            <td>{{ $stockData['pe_ratio'] ?? '31.08' }}</td>
                        </tr>
                        <tr>
                            <td>Exchange</td>
                            <td>{{ $stockData['exchange'] ?? 'INDEX' }}</td>
                        </tr>
                        <tr>
                            <td>Atribut lainnya</td>
                            <td>-</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Recent News Section --}}
    <div class="content-section">
        <div class="section-title">Recent News: {{ $stockData['name'] ?? 'S&P 500' }}</div>

        <div class="d-flex">
            {{-- News Cards Area --}}
            <div class="flex-grow-1" style="overflow: hidden; padding-top: 5px; padding-bottom: 5px;">
                <div class="row g-3 horizontal-scroll-row">
                    @forelse ($news as $item)
                        <div class="news-card-wrapper">
                            <div class="news-card h-100">
                                <div class="news-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="news-source">{{ $item['source'] ?? 'CNN' }}</span>
                                        <span class="news-time">{{ $item['time'] ?? '1h' }}</span>
                                    </div>
                                    <div class="news-title">{{ $item['title'] }}</div>
                                    <div class="news-content">
                                        {{ $item['excerpt'] ?? 'Tidak ada ringkasan tersedia.' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col">
                            <p>Tidak ada berita yang tersedia saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- View All Button --}}
            <div class="ps-4 d-flex align-items-center">
                <a href="{{ route('news.index') }}" class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Additional styles for chart specific elements */
.chart-change.positive {
    color: #28a745;
    background-color: rgba(40, 167, 69, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
}

.chart-change.negative {
    color: #dc3545;
    background-color: rgba(220, 53, 69, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
}

.news-source {
    font-size: 11px;
    font-weight: 600;
    color: #007bff;
    text-transform: uppercase;
}

.news-time {
    font-size: 11px;
    color: #6c757d;
}

/* Chart tooltip positioning */
#chartTooltip {
    position: absolute;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.2s;
    z-index: 1000;
}

.tooltip-date {
    font-size: 11px;
    opacity: 0.8;
}

.tooltip-value {
    font-weight: bold;
    margin-top: 2px;
}

/* Chart canvas container */
.chart-canvas-container {
    position: relative;
    height: 300px;
    margin: 20px 0;
}

#stockChart {
    width: 100%;
    height: 100%;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .chart-tabs {
        flex-wrap: wrap;
        gap: 4px;
    }
    
    .chart-tab {
        padding: 6px 12px;
        font-size: 12px;
    }
    
    .chart-price {
        font-size: 24px;
    }
    
    .chart-symbol {
        font-size: 16px;
        min-width: 50px;
    }
    
    .chart-name {
        font-size: 14px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart data from controller
    const chartData = {!! json_encode($chartData ?? []) !!};
    
    // Initialize chart
    const canvas = document.getElementById('stockChart');
    const ctx = canvas.getContext('2d');
    const tooltip = document.getElementById('chartTooltip');
    
    let chart;
    
    function initChart(data) {
        const labels = data.map((_, index) => `Point ${index + 1}`);
        
        if (chart) {
            chart.destroy();
        }
        
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ $stockData["name"] ?? "S&P 500" }}',
                    data: data,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#28a745',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false,
                        external: function(context) {
                            // Custom tooltip
                            const tooltipModel = context.tooltip;
                            const chartPosition = Chart.helpers.getRelativePosition(context.chart.canvas, context.chart);
                            
                            if (tooltipModel.opacity === 0) {
                                tooltip.style.opacity = 0;
                                return;
                            }
                            
                            if (tooltipModel.body) {
                                const titleLines = tooltipModel.title || [];
                                const bodyLines = tooltipModel.body.map(b => b.lines);
                                
                                let innerHtml = '<div class="tooltip-date">' + titleLines[0] + '</div>';
                                bodyLines.forEach(body => {
                                    innerHtml += '<div class="tooltip-value">' + body[0] + '</div>';
                                });
                                
                                tooltip.innerHTML = innerHtml;
                            }
                            
                            const position = context.chart.canvas.getBoundingClientRect();
                            
                            tooltip.style.opacity = 1;
                            tooltip.style.left = position.left + window.pageXOffset + tooltipModel.caretX + 'px';
                            tooltip.style.top = position.top + window.pageYOffset + tooltipModel.caretY + 'px';
                        }
                    }
                },
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        display: false
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        });
    }
    
    // Period tab functionality
    document.querySelectorAll('.chart-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            const period = this.getAttribute('data-period');
            
            // Fetch new data based on period
            fetch(`{{ route('grafik.data') }}?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        initChart(data.data);
                    }
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);
                });
        });
    });
    
    // Initial chart render
    initChart(chartData);
    
    // Search functionality (if needed)
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value;
            
            if (query.length > 2) {
                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('grafik.search') }}?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            // Handle search results
                            console.log('Search results:', data.results);
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                        });
                }, 300);
            }
        });
    }
});
</script>
@endpush
@endsection