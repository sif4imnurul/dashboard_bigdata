@forelse ($reports as $report)
    <div class="reports-card-wrapper">
        <div class="report-card">
            <div class="card-body">
                <h5 class="card-title mb-1">
                    {{ $report['company_code'] ?? 'N/A' }}
                </h5>
                <h6 class="card-subtitle mb-2 text-muted">
                    {{ $report['company_name'] ?? 'N/A' }}
                </h6>

                <div class="info-row">
                    <span class="info-label">Revenue:</span>
                    <span class="info-value">
                        {{ $report['revenue_rupiah'] ?? 'N/A' }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Cost of Revenue:</span>
                    <span class="info-value">
                        {{ $report['cost_of_revenue_rupiah'] ?? 'N/A' }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Gross Profit:</span>
                    <span class="info-value">
                        {{ $report['gross_profit_rupiah'] ?? 'N/A' }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Net Profit/Loss:</span>
                    <span class="info-value">
                        {{ $report['net_profit_loss_rupiah'] ?? 'N/A' }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Net Profit Margin:</span>
                    <span class="info-value">
                        @if(isset($report['net_profit_margin_pct']))
                            {{ number_format($report['net_profit_margin_pct'] * 100, 2) }}%
                        @else
                            N/A
                        @endif
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Total Assets:</span>
                    <span class="info-value">
                        {{ $report['total_assets_rupiah'] ?? 'N/A' }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Total Liabilities:</span>
                    <span class="info-value">
                        {{ $report['total_liabilities_rupiah'] ?? 'N/A' }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Total Equity:</span>
                    <span class="info-value">
                        {{ $report['total_equity_rupiah'] ?? 'N/A' }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Debt to Equity Ratio:</span>
                    <span class="info-value">
                        @if(isset($report['debt_to_equity_ratio']))
                            {{ number_format($report['debt_to_equity_ratio'], 4) }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Subsector:</span>
                    <span class="info-value">
                        {{ $report['subsector'] ?? 'N/A' }}
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Retrieved At:</span>
                    <span class="info-value">
                        @if(isset($report['retrieved_at']))
                            {{ \Carbon\Carbon::parse($report['retrieved_at'])->locale('id')->translatedFormat('d M Y, H:i') }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>
            </div>
            
            <div class="card-footer bg-white border-0 text-end text-muted">
                <small>
                    Periode: {{ strtoupper($report['period'] ?? request('period', 'tw1')) }} 
                    Tahun: {{ $report['year'] ?? request('year', '2021') }}
                </small>
            </div>
        </div>
    </div>
@empty
    <div class="reports-card-wrapper">
        <div class="alert alert-info text-center">
            Tidak ada laporan keuangan yang ditemukan untuk tahun dan periode ini.
            <br>
            <small class="text-muted mt-2">
                Coba ubah filter tahun atau periode di atas, atau periksa data yang tersedia.
            </small>
        </div>
    </div>
@endforelse