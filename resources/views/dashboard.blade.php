@extends('layouts.app')

@section('content')
<div class="col-md-10 main-content">
    <div class="search-container">
        <div class="search-bar">
            <i class="bi bi-search"></i>
            {{-- Form pencarian saham dengan real-time search --}}
            <div class="w-100 d-flex position-relative">
                <input type="text" 
                       id="stockSearchInput"
                       name="stock_code" 
                       placeholder="Cari saham, contoh: BBRI" 
                       value="{{ $searchStockCode ?? '' }}" 
                       class="form-control border-0 bg-transparent"
                       autocomplete="off">
                <div id="searchSpinner" class="position-absolute end-0 top-50 translate-middle-y me-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Alert untuk menampilkan error --}}
    <div id="searchAlert" class="alert alert-danger mt-3" role="alert" style="display: none;"></div>

    <div class="content-section">
        <div class="section-title">Berita Saham</div>

        <div class="d-flex">
            {{-- Area untuk kartu berita yang bisa di-scroll --}}
            <div class="flex-grow-1" style="overflow: hidden; padding-top: 5px; padding-bottom: 5px;">
                <div id="newsContainer" class="row g-3 horizontal-scroll-row">
                    @forelse ($news as $item)
                        <div class="news-card-wrapper">
                            {{-- Removed the <a> tag here --}}
                            <div class="news-card h-100">
                                <div class="news-body">
                                    <div class="news-title">{{ $item['title'] }}</div>
                                    <div class="news-date">{{ \Carbon\Carbon::createFromFormat('l d/M/Y \a\t H:i', $item['original_date'])->locale('id')->translatedFormat('l, d F Y H:i') }}</div>
                                    <div class="news-content">
                                        {{ $item['summary'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col">
                            <p>Tidak ada berita yang tersedia untuk kode saham ini atau saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Area untuk tombol "Lihat Semua" --}}
            <div class="ps-4 d-flex align-items-center">
                <a href="{{ route('news.index', ['stock_code' => $searchStockCode]) }}" 
                   id="viewAllBtn"
                   class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" 
                   style="width: 45px; height: 45px;">
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="market-cap-badge mb-0">
                            <div class="market-cap-icon">
                                <i class="bi bi-bar-chart-fill"></i>
                            </div>
                            <div>
                                <div class="market-cap-label">Market Cap</div>
                                <div class="market-cap-value">$40.3 T</div>
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
                                <li><a class="dropdown-item" href="#">1 y</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <table class="details-table">
                        <tr>
                            <td>S&P 500</td>
                            <td>
                                <div class="d-flex align-items-center justify-content-end">
                                    <div>4,566.48</div>
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

<script>
$(document).ready(function() {
    let searchTimeout;
    const searchInput = $('#stockSearchInput');
    const newsContainer = $('#newsContainer');
    const searchSpinner = $('#searchSpinner');
    const searchAlert = $('#searchAlert');
    const viewAllBtn = $('#viewAllBtn');
    
    // Debounce function untuk menghindari terlalu banyak request
    function debounce(func, wait) {
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(searchTimeout);
                func(...args);
            };
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(later, wait);
        };
    }
    
    // Function untuk menampilkan loading
    function showLoading() {
        searchSpinner.show();
    }
    
    // Function untuk menyembunyikan loading
    function hideLoading() {
        searchSpinner.hide();
    }
    
    // Function untuk menampilkan error
    function showError(message) {
        searchAlert.text(message).show();
        setTimeout(() => {
            searchAlert.hide();
        }, 5000);
    }
    
    // Function untuk melakukan pencarian
    function performSearch(stockCode) {
        showLoading();
        hideError();
        
        $.ajax({
            url: window.location.pathname,
            method: 'GET',
            data: {
                stock_code: stockCode
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                updateNewsDisplay(response.news);
                updateViewAllButton(response.searchStockCode);
                hideLoading();
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
                let errorMessage = 'Gagal melakukan pencarian';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                showError(errorMessage);
                hideLoading();
            }
        });
    }
    
    // Function untuk menyembunyikan error
    function hideError() {
        searchAlert.hide();
    }
    
    // Function untuk update tampilan berita
    function updateNewsDisplay(newsData) {
        newsContainer.empty();
        
        if (newsData.length === 0) {
            newsContainer.html(`
                <div class="col">
                    <p>Tidak ada berita yang tersedia untuk kode saham ini atau saat ini.</p>
                </div>
            `);
            return;
        }
        
        newsData.forEach(function(item) {
            const newsCard = `
                <div class="news-card-wrapper">
                    {{-- Removed the <a> tag here --}}
                    <div class="news-card h-100">
                        <div class="news-body">
                            <div class="news-title">${item.title}</div>
                            <div class="news-date">${item.formatted_date}</div>
                            <div class="news-content">
                                ${item.summary}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            newsContainer.append(newsCard);
        });
    }
    
    // Function untuk update tombol "Lihat Semua"
    function updateViewAllButton(stockCode) {
        const newsRoute = "{{ route('news.index') }}";
        let newUrl = newsRoute;
        
        if (stockCode && stockCode.trim() !== '') {
            newUrl += `?stock_code=${encodeURIComponent(stockCode)}`;
        }
        
        viewAllBtn.attr('href', newUrl);
    }
    
    // Debounced search function
    const debouncedSearch = debounce(function(stockCode) {
        performSearch(stockCode);
    }, 500); // 500ms delay
    
    // Event listener untuk input pencarian
    searchInput.on('input', function() {
        const stockCode = $(this).val().trim();
        debouncedSearch(stockCode);
    });
    
    // Event listener untuk Enter key
    searchInput.on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const stockCode = $(this).val().trim();
            clearTimeout(searchTimeout); // Clear debounce
            performSearch(stockCode);
        }
    });
    
    // Clear search ketika input dikosongkan
    searchInput.on('keyup', function(e) {
        if (e.which === 8 || e.which === 46) { // Backspace or Delete
            const stockCode = $(this).val().trim();
            if (stockCode === '') {
                debouncedSearch('');
            }
        }
    });
});
</script>
@endsection