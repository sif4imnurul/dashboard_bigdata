@extends('layouts.app')

@section('content')
<div class="col-md-10 main-content">
    {{-- Search Bar --}}
    <div class="search-container mb-4">
        <div class="search-bar">
            <i class="bi bi-search"></i>
            <input type="text" id="reportSearch" placeholder="Cari kode/nama perusahaan..." autocomplete="off" value="{{ $searchQuery }}">
        </div>
    </div>

    <div class="content-section">
        {{-- Top Section: Title and Filters --}}
        <div class="header-section d-flex justify-content-between align-items-center mb-4">
            <div class="section-title d-flex align-items-center">
                Laporan Keuangan
                <div class="filters d-flex align-items-center ms-3"> {{-- Added ms-3 for spacing --}}
                    <div class="me-2"> {{-- Adjusted spacing --}}
                        <select class="form-select form-select-sm" id="reportYear">
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select class="form-select form-select-sm" id="reportPeriod">
                            @foreach ($availablePeriods as $period)
                                <option value="{{ $period }}" {{ $currentPeriod == $period ? 'selected' : '' }}>{{ strtoupper($period) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Available Data Info --}}
        @if(!empty($availableCollections))
            <div class="alert alert-info">
                <strong>Data Tersedia:</strong>
                @php
                    $suggestions = [];
                    foreach($availableCollections as $collection) {
                        if (preg_match('/processed_reports_(\d{4})_(tw\d|TAHUNAN)/', $collection, $matches)) {
                            $suggestions[] = $matches[1] . ' ' . strtoupper($matches[2]);
                        }
                    }
                @endphp
                {{ implode(', ', $suggestions) }}
            </div>
        @endif

        {{-- Loading indicator --}}
        <div id="loadingIndicator" class="text-center py-4" style="display: none;">
            <div class="spinner-border " role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2">Memuat data...</div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info" role="alert">
                {{ session('info') }}
            </div>
        @endif

        {{-- Reports Grid --}}
        <div class="reports-masonry-container mt-3" id="reportsContainer">
            @include('financial_reports.report_items', ['reports' => $reports])
        </div>

        {{-- Pagination --}}
        <div class="mt-5 d-flex justify-content-center" id="paginationContainer">
            {{ $reports->links('layouts.pagination') }}
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let searchTimeout;
    let availableCollections = @json($availableCollections ?? []);

    function showLoading() {
        $('#loadingIndicator').show();
        $('#reportsContainer').hide();
    }

    function hideLoading() {
        $('#loadingIndicator').hide();
        $('#reportsContainer').show();
    }

    function updateAvailablePeriods(year) {
        const periodSelect = $('#reportPeriod');
        const currentPeriod = periodSelect.val();
        
        const periodsForYear = availableCollections.filter(collection => {
            const match = collection.match(/processed_reports_(\d{4})_(tw\d|TAHUNAN)/);
            return match && match[1] == year;
        }).map(collection => {
            const match = collection.match(/processed_reports_(\d{4})_(tw\d|TAHUNAN)/);
            return match[2];
        });

        const uniquePeriods = [...new Set(periodsForYear)].sort();

        periodSelect.empty();
        
        if (uniquePeriods.length > 0) {
            uniquePeriods.forEach(period => {
                const selected = period === currentPeriod ? 'selected' : '';
                periodSelect.append(`<option value="${period}" ${selected}>${period.toUpperCase()}</option>`);
            });
        } else {
            ['tw1', 'tw2', 'tw3', 'tw4', 'TAHUNAN'].forEach(period => {
                const selected = period === currentPeriod ? 'selected' : '';
                periodSelect.append(`<option value="${period}" ${selected}>${period.toUpperCase()}</option>`);
            });
        }

        if (!uniquePeriods.includes(currentPeriod) && uniquePeriods.length > 0) {
            periodSelect.val(uniquePeriods[0]).trigger('change');
        }
    }

    function fetchReports(page = 1) {
        const year = $('#reportYear').val();
        const period = $('#reportPeriod').val();
        const searchQuery = $('#reportSearch').val().trim();

        console.log('Fetching reports with params:', { year, period, searchQuery, page });
        showLoading();

        $.ajax({
            url: '{{ route("financial_reports.index") }}',
            type: 'GET',
            data: {
                year: year,
                period: period,
                search: searchQuery,
                page: page
            },
            timeout: 30000,
            success: function(response) {
                console.log('Response received:', response);
                
                $('#reportsContainer').html(response.reportsHtml);
                
                if (response.paginationHtml) {
                    $('#paginationContainer').html(response.paginationHtml);
                } else {
                    $('#paginationContainer').empty();
                }
                
                if (response.availableCollections) {
                    availableCollections = response.availableCollections;
                    updateAvailablePeriods($('#reportYear').val());
                }
                
                if (response.debug) {
                    console.log('Debug info:', response.debug);
                }
                
                hideLoading();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', { xhr, status, error });
                console.error('Response Text:', xhr.responseText);
                
                let errorMessage = 'Gagal memuat laporan keuangan.';
                let detailMessage = '';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                    if (errorResponse.available_collections) {
                        const suggestions = errorResponse.available_collections.map(collection => {
                            const match = collection.match(/processed_reports_(\d{4})_(tw\d|TAHUNAN)/);
                            return match ? `${match[1]} ${match[2].toUpperCase()}` : collection;
                        });
                        detailMessage = `<br><small>Data tersedia: ${suggestions.join(', ')}</small>`;
                    }
                } catch (e) {
                    if (xhr.status === 0) {
                        errorMessage = 'Koneksi terputus. Periksa koneksi internet Anda.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error. Periksa log server.';
                    } else if (xhr.status === 404) {
                        errorMessage = 'Data tidak ditemukan untuk kombinasi tahun dan periode ini.';
                    }
                }
                
                $('#reportsContainer').html(`
                    <div class="alert alert-danger col-12 reports-card-wrapper">
                        <strong>Error:</strong> ${errorMessage}${detailMessage}<br>
                        <small>Status: ${xhr.status} - ${error}</small>
                    </div>
                `);
                $('#paginationContainer').empty();
                hideLoading();
            }
        });
    }

    // Year change handler
    $('#reportYear').on('change', function() {
        const selectedYear = $(this).val();
        updateAvailablePeriods(selectedYear);
        fetchReports(1);
    });

    // Period change handler
    $('#reportPeriod').on('change', function() {
        fetchReports(1);
    });

    // Initialize periods for current year on page load
    updateAvailablePeriods($('#reportYear').val());
    // Initial fetch of reports based on current filter values
    fetchReports(1);


    // Search with debounce
    $('#reportSearch').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            fetchReports(1);
        }, 500);
    });

    // Pagination
    $(document).on('click', '#paginationContainer a', function(e) {
        e.preventDefault();
        const pageUrl = $(this).attr('href');
        const urlParams = new URLSearchParams(new URL(pageUrl).search);
        const page = urlParams.get('page');
        fetchReports(page);
    });

    // Auto-refresh every 5 minutes for real-time data
    setInterval(function() {
        console.log('Auto-refreshing data...');
        const currentPage = $('#paginationContainer .page-item.active .page-link').text() || 1;
        fetchReports(currentPage);
    }, 300000); // 5 minutes
});
</script>
@endsection