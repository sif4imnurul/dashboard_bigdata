@extends('layouts.app')

@push('styles')
<style>
/* Style tambahan untuk loading dan UX */
.chart-container {
    position: relative;
    min-height: 400px;
}
.page-loading-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(255, 255, 255, 0.8); display: none;
    justify-content: center; align-items: center; z-index: 9999;
}
.chart-tabs .chart-tab { cursor: pointer; }
.chart-change.positive { color: #16a34a; }
.chart-change.negative { color: #dc2626; }
</style>
@endpush

@section('content')
{{-- Overlay untuk Loading Seluruh Halaman --}}
<div class="page-loading-overlay" id="pageLoadingOverlay">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
</div>

<div class="col-md-10 main-content">
    <div class="search-container mb-3">
        <form id="stockSearchForm" class="w-100">
            <div class="search-bar">
                <i class="bi bi-search"></i>
                <input type="text" id="stockSearchInput" 
                       placeholder="Cari saham, contoh: GOTO" 
                       value="{{ $stockData['stock_code'] ?? '' }}" 
                       autocomplete="off" class="form-control border-0 bg-transparent">
            </div>
        </form>
    </div>
    
    <div id="mainDashboardContent">
        {{-- Konten ini akan di-replace oleh AJAX --}}
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(!empty($stockData))
            @include('partials.dashboard_content')
        @else
            <div class="alert alert-warning">
                Data saham tidak dapat ditampilkan. Silakan coba cari kode saham yang valid.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==========================================================
    // BAGIAN INTI UNTUK PENCARIAN AJAX DAN UPDATE HALAMAN
    // ==========================================================

    const searchForm = document.getElementById('stockSearchForm');
    const searchInput = document.getElementById('stockSearchInput');
    const mainContentContainer = document.getElementById('mainDashboardContent');
    const pageLoader = document.getElementById('pageLoadingOverlay');

    // Fungsi utama untuk melakukan pencarian via AJAX
    function performAjaxSearch(stockCode) {
        if (!stockCode) {
            window.location.href = '/'; // Jika input kosong, kembali ke home
            return;
        }

        pageLoader.style.display = 'flex'; // Tampilkan loading

        // Panggil endpoint AJAX baru yang sudah kita buat
        fetch(`/ajax/dashboard/${stockCode}`)
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message || 'Saham tidak ditemukan.') });
                }
                return response.text(); // Ambil respons sebagai teks (HTML)
            })
            .then(html => {
                // Update URL di browser tanpa reload halaman
                history.pushState(null, '', `/${stockCode}`);
                
                // Ganti seluruh konten dashboard dengan hasil render dari server
                // Ini akan dibuat di langkah selanjutnya
                // Untuk sekarang, kita akan update manual (lebih kompleks tapi bisa)
                // Sebaiknya, kita modifikasi controller untuk me-render partial view
                // Untuk sementara, kita reload saja dengan data baru
                 window.location.reload(); // Solusi sementara paling mudah
            })
            .catch(error => {
                console.error('Search error:', error);
                alert(`Terjadi kesalahan: ${error.message}`);
            })
            .finally(() => {
                pageLoader.style.display = 'none'; // Sembunyikan loading
            });
    }
    
    // Fungsi untuk me-render ulang seluruh halaman (solusi sementara yang lebih simpel)
    function searchAndRedirect(stockCode) {
        if (stockCode) {
            window.location.href = `/${stockCode}`;
        } else {
            window.location.href = '/';
        }
    }

    // Event listener untuk form pencarian
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const newStockCode = searchInput.value.trim().toUpperCase();
        searchAndRedirect(newStockCode);
    });

    // ==========================================================
    // BAGIAN UNTUK MENGURUS CHART (TIDAK BERUBAH)
    // ==========================================================
    function initializeChart() {
        if (typeof Chart === 'undefined') return;

        const initialChartData = {!! json_encode($chartData ?? []) !!};
        const stockCode = '{{ $stockData["stock_code"] ?? "" }}';

        if (!stockCode || initialChartData.length === 0) return;

        const ctx = document.getElementById('stockChart')?.getContext('2d');
        if (!ctx) return;

        const loadingOverlay = document.getElementById('loadingOverlay');
        let stockChart;
        let currentPeriod = '1y';

        function formatChartLabels(labels, period) {
            if (period === '1w' || period === '1m' || period === '3m' || period === '6m') {
                return labels.map(dateStr => new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
            }
            if (period === '1y' || period === 'all') {
                return labels.map(dateStr => new Date(dateStr).toLocaleDateString('id-ID', { month: 'short', year: '2-digit' }));
            }
            return labels;
        }

        function renderChart(apiData, period) {
            const dataPoints = apiData.map(item => item.Close);
            const originalLabels = apiData.map(item => item.Date);
            const formattedLabels = formatChartLabels(originalLabels, period);

            if (stockChart) stockChart.destroy();
            
            stockChart = new Chart(ctx, {
                type: 'line', data: { labels: formattedLabels, datasets: [{
                    label: `Harga Penutupan ${stockCode}`, data: dataPoints,
                    borderColor: '#16a34a', backgroundColor: 'rgba(22, 163, 74, 0.1)',
                    borderWidth: 2, pointRadius: 0, tension: 0.1, fill: true,
                }]},
                options: { responsive: true, maintainAspectRatio: false,
                    scales: {
                        x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                        y: { ticks: { callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value) } }
                    },
                    plugins: { legend: { display: false }, tooltip: { callbacks: {
                        label: context => 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(context.parsed.y)
                    }}}
                }
            });
        }

        function fetchAndUpdateChart(period) {
            currentPeriod = period;
            loadingOverlay.style.display = 'flex';
            fetch(`/ajax/chart-data/${stockCode}?period=${period}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success' && result.data && result.data.length > 0) {
                        renderChart(result.data, period);
                    } else {
                        alert('Gagal memuat data untuk periode ini atau data tidak tersedia.');
                    }
                })
                .catch(error => console.error('Error saat fetch data:', error))
                .finally(() => { loadingOverlay.style.display = 'none'; });
        }

        renderChart(initialChartData, currentPeriod);

        document.querySelectorAll('.chart-tabs .chart-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.chart-tabs .chart-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                fetchAndUpdateChart(this.dataset.period);
            });
        });
    }

    // Inisialisasi chart jika konten utama ada
    if(mainContentContainer.querySelector('#stockChart')) {
        initializeChart();
    }
});
</script>
@endpush

{{-- Buat file partial baru untuk konten --}}
{{-- File: resources/views/partials/dashboard_content.blade.php --}}
@if(!empty($stockData))
    <div class="content-section">
        <div class="row g-3">
            <div class="col-md-8">
                <div class="section-title">Grafik Saham</div>
                <div class="chart-container">
                    {{-- ... Konten grafik HTML ... --}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="section-title">Rincian</div>
                <div class="details-container">
                     {{-- ... Konten rincian HTML ... --}}
                </div>
            </div>
        </div>
    </div>
    <div class="content-section mt-4">
        {{-- ... Konten berita HTML ... --}}
    </div>
@else
    <div class="alert alert-warning">
        Data untuk saham ini tidak dapat ditampilkan.
    </div>
@endif
