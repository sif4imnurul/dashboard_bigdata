<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Dashboard</title>
    
    {{-- Memuat library eksternal (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    {{-- Chart.js dimuat di sini agar tersedia secara global untuk script kita --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    {{-- Vite akan menangani pemuatan CSS dan JS kita --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="dashboard-container">
        <div class="row g-0">
            
            <div class="col-md-2 sidebar">
                <div class="logo-circle">TP</div>
                
                <div class="section-label">AI WATCHLIST</div>
                {{-- ... item watchlist ... --}}
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
                
                {{-- MODIFIKASI: Tambahkan class "has-submenu" --}}
                <a href="#" class="menu-item active has-submenu">
                    <i class="bi bi-graph-up"></i>
                    <span>Stock & Fund</span>
                    <i class="bi bi-chevron-down chevron"></i>
                </a>
                {{-- MODIFIKASI: Tambahkan class "show" agar awalnya terbuka karena menu-item-nya active --}}
                <div class="submenu show">
                    <a href="#" class="menu-item">Stock/ETF</a>
                    <a href="#" class="menu-item active">Index</a>
                    <a href="#" class="menu-item">Currency</a>
                    <a href="#" class="menu-item">Mutual Fund</a>
                </div>

                {{-- MODIFIKASI: Tambahkan class "has-submenu" --}}
                <a href="#" class="menu-item has-submenu">
                    <i class="bi bi-wallet2"></i>
                    <span>Wallets</span>
                    <i class="bi bi-chevron-down chevron"></i>
                </a>
                {{-- Submenu ini awalnya akan tersembunyi --}}
                <div class="submenu">
                    <a href="#" class="menu-item">Wallet 1</a>
                    <a href="#" class="menu-item">Wallet 2</a>
                </div>

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
            
            @yield('content')

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>