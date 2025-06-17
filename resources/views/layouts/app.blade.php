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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    {{-- Vite akan menangani pemuatan CSS dan JS kita --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="dashboard-container">
        <div class="row g-0">
            
            {{-- Sidebar dengan ikon yang sudah diperbaiki --}}
            <div class="col-md-2 sidebar">
                <a href="/" class="menu-item">
                    <i class="bi bi-house"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('news.index') }}" class="menu-item">
                    <i class="bi bi-newspaper"></i>
                    <span>Berita</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Grafik Saham</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="bi bi-search"></i>
                    <span>Detail Saham</span>
                </a>
                <a href="{{ route('emiten.index') }}" class="menu-item">
                    <i class="bi bi-buildings"></i>
                    <span>Daftar Emiten</span>
                </a>
                
                {{-- <div class="section-label">SUPPORT</div>
                <a href="#" class="menu-item">
                    <i class="bi bi-people"></i>
                    <span>Community</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="bi bi-question-circle"></i>
                    <span>Help & Support</span>
                </a> --}}
            </div>
            
            @yield('content')

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>