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
    {{-- Search Bar --}}
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
    @if(!empty($stockData) && !empty($stockData['stock_code']))
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
        <div class="alert alert-warning mt-3">
            Data untuk saham ini tidak dapat ditampilkan. Silakan coba cari kode saham yang lain.
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pastikan Chart.js sudah dimuat
    if (typeof Chart === 'undefined') {
        console.error('Chart.js tidak termuat.');
        return;
    }

    // Variabel dan elemen DOM
    const stockCode = '{{ $stockData["stock_code"] ?? "" }}';
    const chartCanvas = document.getElementById('stockChart');
    
    // Jangan jalankan script jika tidak ada canvas atau kode saham
    if (!chartCanvas || !stockCode) {
        console.warn('Data saham atau elemen canvas tidak tersedia, script chart tidak dijalankan.');
        return;
    }

    const ctx = chartCanvas.getContext('2d');
    const loadingOverlay = document.getElementById('loadingOverlay');
    let stockChart;
    let currentPeriod = '1y'; 
    // Variabel untuk menyimpan data yang sedang aktif di chart, diinisialisasi dengan data dari PHP
    let activeChartData = {!! json_encode($chartData ?? []) !!};

    // Fungsi untuk memformat label tanggal di sumbu-X
    function formatChartLabels(labels, period) {
        if (period === '1w' || period === '1m') {
            return labels.map(dateStr => new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
        }
        if (period === '3m' || period === '6m' || period === '1y' || period === 'all') {
            return labels.map(dateStr => new Date(dateStr).toLocaleDateString('id-ID', { month: 'short', year: '2-digit' }));
        }
        return labels;
    }

    // Fungsi utama untuk merender atau mengupdate chart
    function renderChart(apiData, period) {
        // Simpan data terbaru ke variabel global agar bisa diakses oleh tooltip
        activeChartData = apiData;

        const dataPoints = apiData.map(item => item.Close);
        const originalLabels = apiData.map(item => item.Date);
        const formattedLabels = formatChartLabels(originalLabels, period);

        // Hancurkan chart lama jika ada sebelum membuat yang baru
        if (stockChart) {
            stockChart.destroy();
        }
        
        stockChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: formattedLabels,
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
                        ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 }
                    },
                    y: {
                        ticks: {
                            callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                        }
                    }
                },
                // Konfigurasi tooltip kustom
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        intersect: false,
                        mode: 'index',
                        callbacks: {
                            title: function(context) {
                                const dataIndex = context[0].dataIndex;
                                // Ambil data lengkap dari variabel activeChartData
                                const dataPoint = activeChartData[dataIndex];
                                if (!dataPoint) return '';
                                // Format tanggal sebagai judul tooltip
                                return new Date(dataPoint.Date).toLocaleDateString('id-ID', {
                                    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
                                });
                            },
                            label: function(context) {
                                const dataPoint = activeChartData[context.dataIndex];
                                if (!dataPoint) return '';
                                const closePrice = dataPoint.Close || 0;
                                // Format harga penutupan sebagai label utama
                                return ` Tutup: Rp ${new Intl.NumberFormat('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(closePrice)}`;
                            },
                            afterLabel: function(context) {
                                const dataPoint = activeChartData[context.dataIndex];
                                if (!dataPoint) return '';
                                // Ambil data lain dari dataPoint
                                const open = dataPoint.Open || 0;
                                const high = dataPoint.High || 0;
                                const low = dataPoint.Low || 0;
                                const volume = dataPoint.Volume || 0;
                                
                                // Format dan kembalikan sebagai array untuk baris-baris baru di tooltip
                                let tooltipRows = [];
                                tooltipRows.push(`  Buka:    Rp ${new Intl.NumberFormat('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(open)}`);
                                tooltipRows.push(`  Tinggi:  Rp ${new Intl.NumberFormat('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(high)}`);
                                tooltipRows.push(`  Rendah:  Rp ${new Intl.NumberFormat('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(low)}`);
                                tooltipRows.push(`  Volume:  ${new Intl.NumberFormat('id-ID').format(volume)}`);
                                
                                return tooltipRows;
                            }
                        }
                    }
                }
            }
        });
    }

    // Fungsi untuk mengambil data baru via AJAX dan mengupdate chart
    function fetchAndUpdateChart(period) {
        currentPeriod = period;
        loadingOverlay.style.display = 'flex'; // Tampilkan loading
        
        fetch(`/ajax/chart-data/${stockCode}?period=${period}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.status === 'success' && result.data && result.data.length > 0) {
                    renderChart(result.data, period); // Render chart dengan data baru
                } else {
                    console.error('Gagal memuat data chart baru:', result.message);
                    alert('Gagal memuat data untuk periode ini atau data tidak tersedia.');
                }
            })
            .catch(error => {
                console.error('Error saat fetch data:', error);
                alert('Terjadi kesalahan saat mengambil data grafik.');
            })
            .finally(() => {
                loadingOverlay.style.display = 'none'; // Sembunyikan loading
            });
    }

    // Render chart pertama kali saat halaman dimuat jika ada data
    if (activeChartData && activeChartData.length > 0) {
        renderChart(activeChartData, currentPeriod);
    }

    // Tambahkan event listener untuk setiap tab periode
    document.querySelectorAll('.chart-tabs .chart-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Hapus kelas 'active' dari semua tab, lalu tambahkan ke tab yang diklik
            document.querySelectorAll('.chart-tabs .chart-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            // Panggil fungsi untuk update chart
            fetchAndUpdateChart(this.dataset.period);
        });
    });

    // Tambahkan event listener untuk form pencarian
    const searchForm = document.getElementById('stockSearchForm');
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const newStockCode = document.getElementById('stockSearchInput').value.trim().toUpperCase();
        // Redirect ke URL yang sesuai, atau ke halaman utama jika input kosong
        window.location.href = newStockCode ? `/${newStockCode}` : '/';
    });
});
</script>
@endpush