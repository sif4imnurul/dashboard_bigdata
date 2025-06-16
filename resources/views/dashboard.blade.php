{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        .dashboard-container {
            background-color: white;
            overflow: hidden;
            height: 100vh;
            width: 100%;
            margin: 0;
        }
        .sidebar {
            background-color: white;
            height: 100vh;
            border-right: 1px solid #f0f0f0;
            padding-top: 15px;
        }
        .logo-circle {
            width: 40px;
            height: 40px;
            background-color: #0d6efd;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-left: 20px;
            margin-bottom: 20px;
        }
        .section-label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
            padding-left: 20px;
            margin-bottom: 10px;
            margin-top: 20px;
        }
        .watchlist-item {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 10px 15px;
            margin: 0 15px 10px 15px;
        }
        .stock-symbol {
            width: 36px;
            height: 36px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 12px;
            color: #666;
        }
        .stock-name {
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }
        .stock-price {
            font-size: 13px;
            color: #666;
        }
        .stock-change {
            font-size: 12px;
            color: #00c176;
        }
        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #666;
            text-decoration: none;
        }
        .menu-item:hover {
            background-color: #f8f9fa;
        }
        .menu-item.active {
            color: #333;
        }
        .menu-item i {
            margin-right: 12px;
            font-size: 18px;
        }
        .menu-item .chevron {
            margin-left: auto;
        }
        .submenu {
            padding-left: 20px;
        }
        .submenu .menu-item {
            padding: 8px 20px 8px 40px;
            font-size: 14px;
        }
        .submenu .menu-item.active {
            color: #0d6efd;
            font-weight: 500;
        }
        .search-container {
            padding: 15px 20px;
            display: flex;
            justify-content: center;
        }
        .search-bar {
            background-color: #f8f9fa;
            border-radius: 50px;
            padding: 8px 20px;
            width: 100%;
            max-width: 500px;
            display: flex;
            align-items: center;
        }
        .search-bar input {
            border: none;
            background: transparent;
            width: 100%;
            outline: none;
            margin-left: 10px;
        }
        .content-section {
            padding: 0 20px 20px 20px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .news-card {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            border: 1px solid #f0f0f0;
            height: 100%;
        }
        .news-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
            text-transform: uppercase;
        }
        .news-date {
            font-size: 11px;
            color: #999;
            margin-bottom: 8px;
        }
        .news-content {
            font-size: 12px;
            color: #666;
            line-height: 1.5;
        }
        .chart-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }
        .chart-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .chart-symbol {
            width: 30px;
            height: 30px;
            background-color: #f0f0f0;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            margin-right: 10px;
        }
        .chart-name {
            font-weight: 600;
            font-size: 16px;
        }
        .chart-price {
            font-size: 24px;
            font-weight: 600;
            margin-right: 10px;
        }
        .chart-change {
            font-size: 14px;
            color: #00c176;
        }
        .chart-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 15px;
        }
        .chart-tab {
            background-color: #f8f9fa;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 12px;
            cursor: pointer;
        }
        .chart-tab.active {
            background-color: #e9ecef;
            font-weight: 500;
        }
        .chart-info {
            font-size: 11px;
            color: #999;
            margin-bottom: 15px;
        }
        .chart-canvas-container {
            height: 200px;
            width: 100%;
            position: relative;
            margin-bottom: 10px;
        }
        .chart-timeline {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #999;
        }
        .details-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #f0f0f0;
            height: 100%;
        }
        .market-cap-badge {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .market-cap-icon {
            width: 36px;
            height: 36px;
            background-color: #20c997;
            border-radius: 6px;
            margin-right: 10px;
        }
        .market-cap-label {
            font-size: 12px;
            color: #999;
        }
        .market-cap-value {
            font-size: 16px;
            font-weight: 600;
        }
        .details-table {
            width: 100%;
        }
        .details-table tr {
            border-bottom: 1px solid #f0f0f0;
        }
        .details-table tr:last-child {
            border-bottom: none;
        }
        .details-table td {
            padding: 10px 0;
            font-size: 13px;
            color: #333;
        }
        .details-table td:last-child {
            text-align: right;
            font-weight: 500;
        }
        .time-badge {
            display: flex;
            align-items: center;
            font-size: 12px;
            color: #666;
            background-color: #f8f9fa;
            border-radius: 20px;
            padding: 2px 8px;
            margin-left: 5px;
        }
        .time-badge i {
            font-size: 10px;
            margin-right: 3px;
        }
        #chartTooltip {
            position: absolute;
            background-color: rgba(0,0,0,0.7);
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 12px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .tooltip-date {
            font-size: 11px;
            margin-bottom: 2px;
        }
        .tooltip-value {
            font-size: 16px;
            font-weight: 600;
            text-align: center;
        }
        .main-content {
            height: 100vh;
            overflow-y: auto;
        }
        .three-dots {
            cursor: pointer;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="logo-circle">TP</div>
                
                <div class="section-label">AI WATCHLIST</div>
                <div class="watchlist-item">
                    <div class="d-flex align-items-center">
                        <div class="stock-symbol me-2">S&P</div>
                        <div>
                            <div class="stock-name">S&P 500</div>
                            <div class="d-flex align-items-center">
                                <div class="stock-price me-2">4,566.78</div>
                                <div class="stock-change">+0.30%</div>
                            </div>
                            <div class="stock-change">+13.62</div>
                        </div>
                    </div>
                </div>
                <div class="watchlist-item">
                    <div class="d-flex align-items-center">
                        <div class="stock-symbol me-2">S&P</div>
                        <div>
                            <div class="stock-name">S&P 500</div>
                            <div class="d-flex align-items-center">
                                <div class="stock-price me-2">4,566.78</div>
                                <div class="stock-change">+0.30%</div>
                            </div>
                            <div class="stock-change">+13.62</div>
                        </div>
                    </div>
                </div>
                
                <div class="section-label">MAIN MENU</div>
                <a href="#" class="menu-item">
                    <i class="bi bi-house"></i>
                    <span>Home</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Exchange</span>
                </a>
                <a href="#" class="menu-item active">
                    <i class="bi bi-graph-up"></i>
                    <span>Stock & Fund</span>
                    <i class="bi bi-chevron-down chevron"></i>
                </a>
                <div class="submenu">
                    <a href="#" class="menu-item">Stock/ETF</a>
                    <a href="#" class="menu-item active">Index</a>
                    <a href="#" class="menu-item">Currency</a>
                    <a href="#" class="menu-item">Mutual Fund</a>
                </div>
                <a href="#" class="menu-item">
                    <i class="bi bi-wallet2"></i>
                    <span>Wallets</span>
                    <i class="bi bi-chevron-down chevron"></i>
                </a>
                <a href="#" class="menu-item">
                    <i class="bi bi-currency-bitcoin"></i>
                    <span>Crypto</span>
                </a>
                
                <div class="section-label">SUPPORT</div>
                <a href="#" class="menu-item">
                    <i class="bi bi-people"></i>
                    <span>Community</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="bi bi-question-circle"></i>
                    <span>Help & Support</span>
                </a>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Search Bar -->
                <div class="search-container">
                    <div class="search-bar">
                        <i class="bi bi-search"></i>
                        <input type="text" placeholder="Cari saham & lainnya">
                    </div>
                </div>
                
                <!-- News Section -->
                <div class="content-section">
                    <div class="section-title">Berita Saham</div>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="news-card">
                                <div class="news-title">HGII: ROBIN SUNYOTO TAMBAH KEPEMILIKAN SAHAM HGII</div>
                                <div class="news-date">Thursday, 7 March 2024 at 16:30</div>
                                <div class="news-content">
                                    IOPlus, (6/3) - Robin Sunyoto selaku Direktur Utama PT Hero Global Investment Tbk (HGII) kembali menambah porsi kepemilikan sahamnya pada tanggal 28 Februari hingga 5 Maret 2025. Dalam keterangan tertulisnya yang ditulis pada Kamis (6/3) Robin Sunyoto menyampaikan bahwa telah membeli saham HGII sebanyak 1.065.000 lembar saham diharga Rp173-Rp179 per saham...
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="news-card">
                                <div class="news-title">HGII: ROBIN SUNYOTO TAMBAH KEPEMILIKAN SAHAM HGII</div>
                                <div class="news-date">Thursday, 7 March 2024 at 16:30</div>
                                <div class="news-content">
                                    IOPlus, (6/3) - Robin Sunyoto selaku Direktur Utama PT Hero Global Investment Tbk (HGII) kembali menambah porsi kepemilikan sahamnya pada tanggal 28 Februari hingga 5 Maret 2025. Dalam keterangan tertulisnya yang ditulis pada Kamis (6/3) Robin Sunyoto menyampaikan bahwa telah membeli saham HGII sebanyak 1.065.000 lembar saham diharga Rp173-Rp179 per saham...
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="news-card">
                                <div class="news-title">HGII: ROBIN SUNYOTO TAMBAH KEPEMILIKAN SAHAM HGII</div>
                                <div class="news-date">Thursday, 7 March 2024 at 16:30</div>
                                <div class="news-content">
                                    IOPlus, (6/3) - Robin Sunyoto selaku Direktur Utama PT Hero Global Investment Tbk (HGII) kembali menambah porsi kepemilikan sahamnya pada tanggal 28 Februari hingga 5 Maret 2025. Dalam keterangan tertulisnya yang ditulis pada Kamis (6/3) Robin Sunyoto menyampaikan bahwa telah membeli saham HGII sebanyak 1.065.000 lembar saham diharga Rp173-Rp179 per saham...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Chart and Details Section -->
                <div class="content-section">
                    <div class="row g-3">
                        <!-- Chart Section -->
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
                        
                        <!-- Details Section -->
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data dummy untuk grafik
            const chartData = {
                '1d': generateDailyData(),
                '5d': generateDailyData(5),
                '1m': generateMonthlyData(1),
                '6m': generateMonthlyData(6),
                '1y': generateYearlyData(1),
                '5y': generateYearlyData(5),
                'max': generateMaxData()
            };
            
            // Inisialisasi grafik dengan data default (max)
            initChart('max');
            
            // Event listener untuk tab periode
            document.querySelectorAll('.chart-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    // Hapus kelas active dari semua tab
                    document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
                    // Tambahkan kelas active ke tab yang diklik
                    this.classList.add('active');
                    // Update grafik dengan data periode yang dipilih
                    initChart(this.getAttribute('data-period'));
                });
            });
            
            // Fungsi untuk inisialisasi grafik
            function initChart(period) {
                const ctx = document.getElementById('stockChart').getContext('2d');
                
                // Hapus grafik lama jika ada
                if (window.stockChart) {
                    window.stockChart.destroy();
                }
                
                // Data untuk periode yang dipilih
                const data = chartData[period];
                
                // Konfigurasi gradient fill
                const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                gradient.addColorStop(0, 'rgba(0, 193, 118, 0.4)');
                gradient.addColorStop(1, 'rgba(0, 193, 118, 0)');
                
                // Buat grafik baru
                window.stockChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'S&P 500',
                            data: data.values,
                            borderColor: '#00c176',
                            backgroundColor: gradient,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#00c176',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false,
                                external: externalTooltipHandler
                            }
                        },
                        scales: {
                            x: {
                                display: false
                            },
                            y: {
                                display: false
                            }
                        }
                    }
                });
            }
            
            // Custom tooltip handler
            function externalTooltipHandler(context) {
                // Tooltip element
                const tooltipEl = document.getElementById('chartTooltip');
                
                // Hide if no tooltip
                const tooltipModel = context.tooltip;
                if (tooltipModel.opacity === 0) {
                    tooltipEl.style.opacity = 0;
                    return;
                }
                
                // Set Text
                if (tooltipModel.body) {
                    const titleLines = tooltipModel.title || [];
                    const bodyLines = tooltipModel.body.map(b => b.lines);
                    
                    // Format date
                    const dateEl = tooltipEl.querySelector('.tooltip-date');
                    dateEl.textContent = titleLines[0];
                    
                    // Format value
                    const valueEl = tooltipEl.querySelector('.tooltip-value');
                    valueEl.textContent = bodyLines[0];
                }
                
                // Position tooltip
                const position = context.chart.canvas.getBoundingClientRect();
                tooltipEl.style.opacity = 1;
                tooltipEl.style.left = position.left + context.tooltip.caretX + 'px';
                tooltipEl.style.top = position.top + context.tooltip.caretY + 'px';
                tooltipEl.style.transform = 'translate(-50%, -100%)';
            }
            
            // Fungsi untuk generate data dummy
            function generateDailyData(days = 1) {
                const labels = [];
                const values = [];
                const baseValue = 4500;
                const now = new Date();
                
                for (let i = 0; i < 24 * days; i++) {
                    const time = new Date(now);
                    time.setHours(time.getHours() - (24 * days - i));
                    
                    const formattedTime = time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    labels.push(formattedTime);
                    
                    // Generate random value with trend
                    const randomChange = (Math.random() - 0.3) * 20;
                    const newValue = i === 0 ? baseValue : values[i - 1] + randomChange;
                    values.push(newValue);
                }
                
                return { labels, values };
            }
            
            function generateMonthlyData(months = 1) {
                const labels = [];
                const values = [];
                const baseValue = 4300;
                const now = new Date();
                const daysInPeriod = 30 * months;
                
                for (let i = 0; i < daysInPeriod; i++) {
                    const date = new Date(now);
                    date.setDate(date.getDate() - (daysInPeriod - i));
                    
                    const formattedDate = date.toLocaleDateString([], { month: 'short', day: 'numeric' });
                    labels.push(formattedDate);
                    
                    // Generate random value with upward trend
                    const trend = (i / daysInPeriod) * 300; // Upward trend over period
                    const randomChange = (Math.random() - 0.3) * 30;
                    const newValue = i === 0 ? baseValue : values[i - 1] + randomChange;
                    values.push(newValue + trend);
                }
                
                return { labels, values };
            }
            
            function generateYearlyData(years = 1) {
                const labels = [];
                const values = [];
                const baseValue = 3800;
                const now = new Date();
                const daysInPeriod = 365 * years;
                const dataPoints = Math.min(daysInPeriod, 100); // Limit data points for performance
                const interval = Math.floor(daysInPeriod / dataPoints);
                
                for (let i = 0; i < dataPoints; i++) {
                    const date = new Date(now);
                    date.setDate(date.getDate() - (daysInPeriod - i * interval));
                    
                    const formattedDate = date.toLocaleDateString([], { month: 'short', year: 'numeric' });
                    labels.push(formattedDate);
                    
                    // Generate random value with upward trend
                    const trend = (i / dataPoints) * 800; // Stronger upward trend over years
                    const randomChange = (Math.random() - 0.3) * 50;
                    const newValue = i === 0 ? baseValue : values[i - 1] + randomChange;
                    values.push(newValue + trend);
                }
                
                return { labels, values };
            }
            
            function generateMaxData() {
                const labels = [];
                const values = [];
                const baseValue = 3200;
                const dataPoints = 100;
                
                // Generate data for Jul 2023 to Nov 2023
                const startDate = new Date('2023-07-01');
                const endDate = new Date('2023-11-30');
                const daysDiff = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24));
                const interval = Math.floor(daysDiff / dataPoints);
                
                for (let i = 0; i < dataPoints; i++) {
                    const date = new Date(startDate);
                    date.setDate(date.getDate() + i * interval);
                    
                    const formattedDate = date.toLocaleDateString([], { month: 'short', day: 'numeric' });
                    labels.push(formattedDate);
                    
                    // Pattern similar to the image: up, down, up with a dip in the middle
                    let trend;
                    if (i < dataPoints * 0.2) {
                        // Initial rise
                        trend = (i / (dataPoints * 0.2)) * 300;
                    } else if (i < dataPoints * 0.4) {
                        // First peak and decline
                        trend = 300 - ((i - dataPoints * 0.2) / (dataPoints * 0.2)) * 200;
                    } else if (i < dataPoints * 0.6) {
                        // Second rise
                        trend = 100 + ((i - dataPoints * 0.4) / (dataPoints * 0.2)) * 400;
                    } else if (i < dataPoints * 0.8) {
                        // Second peak and decline
                        trend = 500 - ((i - dataPoints * 0.6) / (dataPoints * 0.2)) * 200;
                    } else {
                        // Final rise
                        trend = 300 + ((i - dataPoints * 0.8) / (dataPoints * 0.2)) * 200;
                    }
                    
                    const randomChange = (Math.random() - 0.3) * 30;
                    const newValue = baseValue + trend + randomChange;
                    values.push(newValue);
                }
                
                return { labels, values };
            }
        });
    </script>
</body>
</html> --}}

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
        
        {{-- Container untuk horizontal scrolling --}}
        <div class="row g-3 horizontal-scroll-row"> 
            @forelse ($news as $item)
                {{-- Kita tidak lagi menggunakan col-md-4 agar lebar kartu konsisten --}}
                <div class="news-card-wrapper"> 
                    <div class="news-card">
                        <div class="news-title">{{ $item['title'] }}</div>
                        <div class="news-date">{{ $item['original_date'] }}</div>
                        <div class="news-content">
                            {{-- Kita gunakan 'summary' agar lebih ringkas --}}
                            {{ $item['summary'] }}
                        </div>
                    </div>
                </div>
            @empty
                {{-- Pesan ini akan muncul jika API gagal atau tidak ada berita --}}
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dummy untuk grafik
        const chartData = {
            '1d': generateDailyData(),
            '5d': generateDailyData(5),
            '1m': generateMonthlyData(1),
            '6m': generateMonthlyData(6),
            '1y': generateYearlyData(1),
            '5y': generateYearlyData(5),
            'max': generateMaxData()
        };
        
        // Inisialisasi grafik dengan data default (max)
        initChart('max');
        
        // Event listener untuk tab periode
        document.querySelectorAll('.chart-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Hapus kelas active dari semua tab
                document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
                // Tambahkan kelas active ke tab yang diklik
                this.classList.add('active');
                // Update grafik dengan data periode yang dipilih
                initChart(this.getAttribute('data-period'));
            });
        });
        
        // Fungsi untuk inisialisasi grafik
        function initChart(period) {
            const ctx = document.getElementById('stockChart').getContext('2d');
            
            // Hapus grafik lama jika ada
            if (window.stockChart) {
                window.stockChart.destroy();
            }
            
            // Data untuk periode yang dipilih
            const data = chartData[period];
            
            // Konfigurasi gradient fill
            const gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(0, 193, 118, 0.4)');
            gradient.addColorStop(1, 'rgba(0, 193, 118, 0)');
            
            // Buat grafik baru
            window.stockChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'S&P 500',
                        data: data.values,
                        borderColor: '#00c176',
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#00c176',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false,
                            external: externalTooltipHandler
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false
                        }
                    }
                }
            });
        }
        
        // Custom tooltip handler
        function externalTooltipHandler(context) {
            // Tooltip element
            const tooltipEl = document.getElementById('chartTooltip');
            
            // Hide if no tooltip
            const tooltipModel = context.tooltip;
            if (tooltipModel.opacity === 0) {
                tooltipEl.style.opacity = 0;
                return;
            }
            
            // Set Text
            if (tooltipModel.body) {
                const titleLines = tooltipModel.title || [];
                const bodyLines = tooltipModel.body.map(b => b.lines);
                
                // Format date
                const dateEl = tooltipEl.querySelector('.tooltip-date');
                dateEl.textContent = titleLines[0];
                
                // Format value
                const valueEl = tooltipEl.querySelector('.tooltip-value');
                valueEl.textContent = bodyLines[0];
            }
            
            // Position tooltip
            const position = context.chart.canvas.getBoundingClientRect();
            tooltipEl.style.opacity = 1;
            tooltipEl.style.left = position.left + context.tooltip.caretX + 'px';
            tooltipEl.style.top = position.top + context.tooltip.caretY + 'px';
            tooltipEl.style.transform = 'translate(-50%, -100%)';
        }
        
        // Fungsi untuk generate data dummy
        function generateDailyData(days = 1) {
            const labels = [];
            const values = [];
            const baseValue = 4500;
            const now = new Date();
            
            for (let i = 0; i < 24 * days; i++) {
                const time = new Date(now);
                time.setHours(time.getHours() - (24 * days - i));
                
                const formattedTime = time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                labels.push(formattedTime);
                
                // Generate random value with trend
                const randomChange = (Math.random() - 0.3) * 20;
                const newValue = i === 0 ? baseValue : values[i - 1] + randomChange;
                values.push(newValue);
            }
            
            return { labels, values };
        }
        
        function generateMonthlyData(months = 1) {
            const labels = [];
            const values = [];
            const baseValue = 4300;
            const now = new Date();
            const daysInPeriod = 30 * months;
            
            for (let i = 0; i < daysInPeriod; i++) {
                const date = new Date(now);
                date.setDate(date.getDate() - (daysInPeriod - i));
                
                const formattedDate = date.toLocaleDateString([], { month: 'short', day: 'numeric' });
                labels.push(formattedDate);
                
                // Generate random value with upward trend
                const trend = (i / daysInPeriod) * 300; // Upward trend over period
                const randomChange = (Math.random() - 0.3) * 30;
                const newValue = i === 0 ? baseValue : values[i - 1] + randomChange;
                values.push(newValue + trend);
            }
            
            return { labels, values };
        }
        
        function generateYearlyData(years = 1) {
            const labels = [];
            const values = [];
            const baseValue = 3800;
            const now = new Date();
            const daysInPeriod = 365 * years;
            const dataPoints = Math.min(daysInPeriod, 100); // Limit data points for performance
            const interval = Math.floor(daysInPeriod / dataPoints);
            
            for (let i = 0; i < dataPoints; i++) {
                const date = new Date(now);
                date.setDate(date.getDate() - (daysInPeriod - i * interval));
                
                const formattedDate = date.toLocaleDateSAtring([], { month: 'short', year: 'numeric' });
                labels.push(formattedDate);
                
                // Generate random value with upward trend
                const trend = (i / dataPoints) * 800; // Stronger upward trend over years
                const randomChange = (Math.random() - 0.3) * 50;
                const newValue = i === 0 ? baseValue : values[i - 1] + randomChange;
                values.push(newValue + trend);
            }
            
            return { labels, values };
        }
        
        function generateMaxData() {
            const labels = [];
            const values = [];
            const baseValue = 3200;
            const dataPoints = 100;
            
            // Generate data for Jul 2023 to Nov 2023
            const startDate = new Date('2023-07-01');
            const endDate = new Date('2023-11-30');
            const daysDiff = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24));
            const interval = Math.floor(daysDiff / dataPoints);
            
            for (let i = 0; i < dataPoints; i++) {
                const date = new Date(startDate);
                date.setDate(date.getDate() + i * interval);
                
                const formattedDate = date.toLocaleDateString([], { month: 'short', day: 'numeric' });
                labels.push(formattedDate);
                
                // Pattern similar to the image: up, down, up with a dip in the middle
                let trend;
                if (i < dataPoints * 0.2) {
                    // Initial rise
                    trend = (i / (dataPoints * 0.2)) * 300;
                } else if (i < dataPoints * 0.4) {
                    // First peak and decline
                    trend = 300 - ((i - dataPoints * 0.2) / (dataPoints * 0.2)) * 200;
                } else if (i < dataPoints * 0.6) {
                    // Second rise
                    trend = 100 + ((i - dataPoints * 0.4) / (dataPoints * 0.2)) * 400;
                } else if (i < dataPoints * 0.8) {
                    // Second peak and decline
                    trend = 500 - ((i - dataPoints * 0.6) / (dataPoints * 0.2)) * 200;
                } else {
                    // Final rise
                    trend = 300 + ((i - dataPoints * 0.8) / (dataPoints * 0.2)) * 200;
                }
                
                const randomChange = (Math.random() - 0.3) * 30;
                const newValue = baseValue + trend + randomChange;
                values.push(newValue);
            }
            
            return { labels, values };
        }
    });
</script>
@endpush