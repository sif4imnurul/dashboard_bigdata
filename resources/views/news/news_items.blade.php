@forelse ($news as $item)
    {{-- Hapus col-lg-4 col-md-6 col-12 untuk grid dinamis --}}
    <div class="news-item-dynamic">
        <div class="news-card-wrapper">
            <div class="news-card">
                <div class="news-body">
                    <div class="news-title">{{ $item['title'] }}</div>
                    <div class="news-date">{{ $item['original_date'] }}</div>
                    <div class="news-content">
                        {{-- Tampilkan konten penuh --}}
                        {{ $item['summary'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="news-item-dynamic">
        <div class="alert alert-info">
            Tidak ada berita yang ditemukan.
        </div>
    </div>
@endforelse