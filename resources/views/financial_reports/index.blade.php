@extends('layouts.app')

@section('content')
<div class="col-md-10 main-content">
    {{-- Search Bar --}}
    <div class="search-container mb-4">
        <div class="search-bar">
            <i class="bi bi-search"></i>
            <input type="text" id="reportSearch" placeholder="Cari kode/nama perusahaan..." autocomplete="off" value="{{ $searchQuery ?? '' }}">
        </div>
    </div>

    <div class="content-section">
        {{-- Top Section: Title and Filters --}}
        <div class="header-section d-flex justify-content-between align-items-center mb-4">
            <div class="section-title d-flex align-items-center">
                Laporan Keuangan
                <div class="filters d-flex align-items-center ms-3">
                    <div class="me-2">
                        <select class="form-select form-select-sm" id="reportYear">
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" {{ ($currentYear ?? '') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="me-2">
                        <select class="form-select form-select-sm" id="reportPeriod">
                            @foreach ($availablePeriods as $period)
                                <option value="{{ $period }}" {{ ($currentPeriod ?? '') == $period ? 'selected' : '' }}>{{ strtoupper($period) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button class="btn btn-green btn-sm" id="applyFilterBtn">Terapkan</button>
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
        $('#paginationContainer').hide();
    }

    function hideLoading() {
        $('#loadingIndicator').hide();
        $('#reportsContainer').show();
        $('#paginationContainer').show();
    }

    function updateAvailablePeriods(year) {
        const periodSelect = $('#reportPeriod');
        periodSelect.empty();
        const periodsForYear = availableCollections
            .map(c => c.match(/processed_reports_(\d{4})_(tw\d|TAHUNAN)/))
            .filter(m => m && m[1] == year)
            .map(m => m[2]);
        
        const uniquePeriods = [...new Set(periodsForYear)].sort();

        if (uniquePeriods.length > 0) {
            uniquePeriods.forEach(period => {
                periodSelect.append(`<option value="${period}">${period.toUpperCase()}</option>`);
            });
        } else {
            ['tw1', 'tw2', 'tw3', 'tw4', 'TAHUNAN'].forEach(period => {
                periodSelect.append(`<option value="${period}">${period.toUpperCase()}</option>`);
            });
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
            data: { year, period, search: searchQuery, page },
            timeout: 30000,
            success: function(response) {
                $('#reportsContainer').html(response.reportsHtml);
                $('#paginationContainer').html(response.paginationHtml || '');
                hideLoading();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', { xhr, status, error });
                $('#reportsContainer').html('<div class="alert alert-danger col-12">Gagal memuat laporan keuangan.</div>');
                $('#paginationContainer').empty();
                hideLoading();
            }
        });
    }

    // Dropdown Tahun HANYA mengubah opsi Periode.
    $('#reportYear').on('change', function() {
        updateAvailablePeriods($(this).val());
    });

    // Tombol "Terapkan" menjadi pemicu utama.
    $('#applyFilterBtn').on('click', function() {
        fetchReports(1);
    });
    
    // Search dengan debounce
    $('#reportSearch').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchReports(1), 500);
    });

    // Pagination
    $(document).on('click', '#paginationContainer a', function(e) {
        e.preventDefault();
        const page = new URL($(this).attr('href')).searchParams.get('page');
        fetchReports(page);
    });

    // Panggil saat halaman pertama kali dimuat
    updateAvailablePeriods($('#reportYear').val());
    fetchReports(1); 
});
</script>
@endsection