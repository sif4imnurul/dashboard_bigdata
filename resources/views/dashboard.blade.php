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
.chart-change.positive { color: #16a34a; }
.chart-change.negative { color: #dc2626; }
</style>
@endpush

@section('content')
<div class="col-md-10 main-content">
    {{-- Search Bar untuk mencari saham lain --}}
    <div class="search-container mb-3">
        <form id="stockSearchForm" class="w-100">
            <div class="search-bar">
                <i class="bi bi-search"></i>
                <input type="text" id="stockSearchInput" 
                       placeholder="Cari saham, contoh: GOTO" 
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
                <div class="col-md-8">
                    <div class="section-title">Grafik Saham</div>
                    <div class="chart-container">
                        <div class="d-flex align-items-center mb-2">
                            <div class="chart-symbol">{{ $stockData['stock_code'] ?? 'N/A' }}</div>
                            <div class="chart-name">{{ $stockData['name'] ?? 'Nama Perusahaan' }}</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="chart-price">{{ number_format($stockData['current_price'] ?? 0, 2) }}</div>
                                <div class="chart-change {{ ($stockData['change_percent'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                                    {{ ($stockData['change_percent'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($stockData['change_percent'] ?? 0, 2) }}%
                                </div>
                            </div>
                            <div class="chart-tabs">
                                <div class="chart-tab" data-period="1w">1Mgg</div>
                                <div class="chart-tab" data-period="1m">1Bln</div>
                                <div class="chart-tab" data-period="3m">3Bln</div>
                                <div class="chart-tab" data-period="6m">6Bln</div>
                                <div class="chart-tab active" data-period="1y">1Thn</div>
                                <div class="chart-tab" data-period="all">Semua</div>
                            </div>
                        </div>
                        <div class="chart-canvas-container">
                            <div id="loadingOverlay" style="display: none;"><div class="spinner-border text-primary"></div></div>
                            <canvas id="stockChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="section-title">Rincian</div>
                    <div class="details-container">
                        <table class="details-table">
                            <tr><td>Kode Saham</td><td><strong>{{ $stockData['stock_code'] ?? '-' }}</strong></td></tr>
                            <tr><td>Harga Terkini</td><td>{{ number_format($stockData['current_price'] ?? 0, 2) }}</td></tr>
                            <tr><td>Tertinggi (Hari Ini)</td><td>{{ number_format($stockData['day_high'] ?? 0, 2) }}</td></tr>
                            <tr><td>Terendah (Hari Ini)</td><td>{{ number_format($stockData['day_low'] ?? 0, 2) }}</td></tr>
                            <tr><td>Volume</td><td>{{ number_format($stockData['volume'] ?? 0) }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bagian Berita Saham --}}
        <div class="content-section mt-4">
            <div class="section-title d-flex justify-content-between">
                <span>Berita Terkait: {{ $isDetailView ? $stockData['stock_code'] : 'Terkini' }}</span>
                <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua Berita</a>
            </div>
            <div class="row g-3">
                @forelse ($news as $item)
                    <div class="col-md-4">
                        <div class="news-card h-100">
                            <div class="news-body">
                                <div class="news-title">{{ $item['title'] }}</div>
                                {{-- Menampilkan tanggal yang sudah diformat oleh Controller --}}
                                <div class="news-date">{{ $item['original_date'] ?? '' }}</div>
                                <div class="news-content">{{ Str::limit($item['summary'], 150) }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col"><p>Tidak ada berita yang tersedia.</p></div>
                @endforelse
            </div>
        </div>

    @else
        <div class="alert alert-warning">
            Data untuk saham ini tidak dapat ditampilkan. Silakan coba cari kode saham yang lain.
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js tidak termuat.');
        return;
    }

    const initialChartData = {!! json_encode($chartData ?? []) !!};
    const stockCode = '{{ $stockData["stock_code"] ?? "" }}';

    // Jika tidak ada data saham, jangan jalankan script chart
    if (!stockCode || initialChartData.length === 0) {
        console.warn('Data saham atau data chart tidak tersedia, script chart tidak dijalankan.');
        return;
    }

    const ctx = document.getElementById('stockChart').getContext('2d');
    const loadingOverlay = document.getElementById('loadingOverlay');
    let stockChart;
    let currentPeriod = '1y'; // Periode saat halaman pertama kali dimuat

    // Fungsi untuk memformat label sumbu-X secara dinamis
    function formatChartLabels(labels, period) {
        if (period === '1w' || period === '1m' || period === '3m' || period === '6m') {
            // Untuk periode harian, tampilkan Hari-Bulan (misal: 19-Jun)
            return labels.map(dateStr => new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
        }
        if (period === '1y' || period === 'all') {
            // Untuk periode bulanan, tampilkan Bulan-Tahun (misal: Jun '25)
            return labels.map(dateStr => new Date(dateStr).toLocaleDateString('id-ID', { month: 'short', year: '2-digit' }));
        }
        return labels; // Fallback jika periode tidak dikenal
    }

    function renderChart(apiData, period) {
        // Ambil data 'Close' dari API, ini akan menjadi titik-titik di grafik
        const dataPoints = apiData.map(item => item.Close);
        
        // Ambil data 'Date' untuk label di bawah grafik
        const originalLabels = apiData.map(item => item.Date);
        const formattedLabels = formatChartLabels(originalLabels, period);

        if (stockChart) stockChart.destroy(); // Hancurkan chart lama jika ada
        
        stockChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: formattedLabels,
                datasets: [{
                    label: `Harga Penutupan ${stockCode}`,
                    data: dataPoints,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.1)',
                    borderWidth: 2, pointRadius: 0, tension: 0.1, fill: true,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                    y: { ticks: { callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value) } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: context => 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(context.parsed.y)
                        }
                    }
                }
            }
        });
    }

    function fetchAndUpdateChart(period) {
        currentPeriod = period;
        loadingOverlay.style.display = 'flex';
        
        // Panggil endpoint AJAX yang sudah kita buat di routes/web.php
        fetch(`/ajax/chart-data/${stockCode}?period=${period}`)
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success' && result.data && result.data.length > 0) {
                    renderChart(result.data, period); // Render chart dengan data baru
                } else {
                    console.error('Gagal mengambil data chart baru:', result.message);
                    alert('Gagal memuat data untuk periode ini atau data tidak tersedia.');
                }
            })
            .catch(error => console.error('Error saat fetch data:', error))
            .finally(() => {
                loadingOverlay.style.display = 'none'; // Sembunyikan loading
            });
    }

    // Render chart pertama kali dengan data dari server
    renderChart(initialChartData, currentPeriod);

    // Tambahkan event listener untuk setiap tab periode
    document.querySelectorAll('.chart-tabs .chart-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.chart-tabs .chart-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            fetchAndUpdateChart(this.dataset.period);
        });
    });

    // Logika untuk form pencarian
    const searchForm = document.getElementById('stockSearchForm');
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const newStockCode = document.getElementById('stockSearchInput').value.trim().toUpperCase();
        
        // Redirect ke halaman detail jika ada input, atau ke halaman utama jika kosong
        window.location.href = newStockCode ? `/${newStockCode}` : '/';
    });
});
</script>
@endpush
