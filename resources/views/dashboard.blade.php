@extends('layouts.app')

@push('styles')
<style>
/* Style tambahan untuk loading dan UX */
.chart-container {
    position: relative;
    min-height: 400px; /* Beri tinggi minimal agar tidak collaps saat loading */
}
#loadingOverlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
    backdrop-filter: blur(2px);
    border-radius: 10px;
    transition: opacity 0.3s;
}
.chart-tabs .chart-tab {
    cursor: pointer;
}
.chart-change.positive {
    color: #16a34a;
}
.chart-change.negative {
    color: #dc2626;
}
</style>
@endpush

@section('content')
<div class="col-md-10 main-content">
    {{-- Search Bar untuk mencari saham lain --}}
    <div class="search-container mb-3">
        <form action="#" id="stockSearchForm" class="w-100">
            <div class="search-bar">
                <i class="bi bi-search"></i>
                {{-- Gunakan $searchStockCode untuk mengisi nilai awal --}}
                <input type="text" id="stockSearchInput" 
                       placeholder="Cari saham, contoh: BBCA" 
                       value="{{ $searchStockCode ?? '' }}" 
                       autocomplete="off" class="form-control border-0 bg-transparent">
            </div>
        </form>
    </div>
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Hanya tampilkan jika data saham berhasil dimuat --}}
    @if(!empty($stockData))
        {{-- Bagian Grafik dan Rincian --}}
        <div class="content-section">
            <div class="row g-3">
                {{-- Area Grafik --}}
                <div class="col-md-8">
                    <div class="section-title">
                        Grafik Saham
                    </div>
                    
                    <div class="chart-container">
                        {{-- Header Saham --}}
                        <div class="d-flex align-items-center mb-2">
                            <div class="chart-symbol">{{ $stockData['stock_code'] ?? 'N/A' }}</div>
                            <div class="chart-name">{{ $stockData['name'] ?? 'Nama Perusahaan' }}</div>
                        </div>
                        
                        {{-- Harga dan Kontrol Periode --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="chart-price">{{ number_format($stockData['current_price'] ?? 0, 2) }}</div>
                                <div class="chart-change {{ ($stockData['change_percent'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                                    {{ ($stockData['change_percent'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($stockData['change_percent'] ?? 0, 2) }}%
                                </div>
                            </div>
                            <div class="chart-tabs">
                                {{-- Tab Periode --}}
                                <div class="chart-tab" data-period="1w">1Mgg</div>
                                <div class="chart-tab" data-period="1m">1Bln</div>
                                <div class="chart-tab" data-period="3m">3Bln</div>
                                <div class="chart-tab" data-period="6m">6Bln</div>
                                <div class="chart-tab active" data-period="1y">1Thn</div>
                                <div class="chart-tab" data-period="all">Semua</div>
                            </div>
                        </div>
                        
                        {{-- Chart Canvas --}}
                        <div class="chart-canvas-container">
                            <div id="loadingOverlay" style="display: none;">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                            <canvas id="stockChart"></canvas>
                        </div>
                    </div>
                </div>
                
                {{-- Rincian di Sidebar Kanan --}}
                <div class="col-md-4">
                    <div class="section-title">
                        Rincian
                    </div>
                    
                    <div class="details-container">
                        <table class="details-table">
                            <tr>
                                <td>Kode Saham</td>
                                <td><strong>{{ $stockData['stock_code'] ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Harga Terkini</td>
                                <td>{{ number_format($stockData['current_price'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Tertinggi (Hari Ini)</td>
                                <td>{{ number_format($stockData['day_high'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Terendah (Hari Ini)</td>
                                <td>{{ number_format($stockData['day_low'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Volume</td>
                                <td>{{ number_format($stockData['volume'] ?? 0) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bagian Berita Saham --}}
        <div class="content-section mt-4">
            <div class="section-title">
                Berita Terkait: {{ $stockData['stock_code'] }}
                <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua Berita</a>
            </div>

            <div class="row g-3">
                @forelse ($news as $item)
                    <div class="col-md-4">
                        <div class="news-card h-100">
                            <div class="news-body">
                                <div class="news-title">{{ $item['title'] }}</div>
                                @if(!empty($item['original_date']))
                                <div class="news-date">
                                    {{ $item['original_date'] }}
                                </div>
                                @endif
                                <div class="news-content">
                                    {{ Str::limit($item['summary'], 150) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col">
                        <p>Tidak ada berita yang tersedia untuk kode saham ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

    @else
        <div class="alert alert-warning">
            Data untuk saham ini tidak dapat ditampilkan. Silakan coba cari kode saham yang lain menggunakan kolom pencarian di atas.
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pastikan Chart.js sudah dimuat
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded.');
        return;
    }

    // Ambil data dari PHP Blade ke JavaScript
    const initialChartData = {!! json_encode($chartData ?? []) !!};
    const stockCode = '{{ $stockData["stock_code"] ?? "" }}';

    // Jika tidak ada data saham, jangan jalankan script chart
    if (!stockCode || initialChartData.length === 0) {
        return;
    }

    const ctx = document.getElementById('stockChart').getContext('2d');
    const loadingOverlay = document.getElementById('loadingOverlay');
    let stockChart;

    // Fungsi untuk memformat data dari API untuk Chart.js
    function formatDataForChart(apiData) {
        const labels = apiData.map(item => new Date(item.Date).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' }));
        const dataPoints = apiData.map(item => item.Close);
        return { labels, dataPoints };
    }

    // Fungsi untuk membuat atau mengupdate chart
    function renderChart(apiData) {
        const { labels, dataPoints } = formatDataForChart(apiData);
        
        if (stockChart) {
            stockChart.destroy(); // Hancurkan chart lama jika ada
        }
        
        stockChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: `Harga Penutupan ${stockCode}`,
                    data: dataPoints,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.1)',
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.1,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 8 
                        }
                    },
                    y: {
                        ticks: {
                            callback: function(value, index, values) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // Fungsi untuk mengambil data baru via AJAX saat tab periode diklik
    function fetchAndUpdateChart(period) {
        loadingOverlay.style.display = 'flex'; // Tampilkan loading
        
        // Gunakan route yang sudah kita definisikan
        const url = `/ajax/chart-data/${stockCode}?period=${period}`;
        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                if (result.status === 'success' && result.data) {
                    renderChart(result.data);
                } else {
                    console.error('Failed to fetch new chart data:', result.message);
                }
            })
            .catch(error => console.error('Error fetching data:', error))
            .finally(() => {
                loadingOverlay.style.display = 'none'; // Sembunyikan loading
            });
    }

    // Render chart pertama kali dengan data dari server
    renderChart(initialChartData);

    // Tambahkan event listener untuk setiap tab periode
    const tabs = document.querySelectorAll('.chart-tabs .chart-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const period = this.dataset.period;
            fetchAndUpdateChart(period);
        });
    });
    
    // Logika untuk form pencarian, akan me-redirect ke halaman detail saham
    const searchForm = document.getElementById('stockSearchForm');
    const searchInput = document.getElementById('stockSearchInput');
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const newStockCode = searchInput.value.trim().toUpperCase();
        if (newStockCode) {
            // Redirect ke halaman detail untuk kode saham yang baru
            window.location.href = `/${newStockCode}`;
        } else {
            // Jika kosong, redirect ke halaman utama
            window.location.href = '/';
        }
    });
});
</script>
@endpush
