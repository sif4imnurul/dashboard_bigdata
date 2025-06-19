@forelse ($news as $item)
    <div class="news-item-dynamic">
        <div class="news-card-wrapper">
            <div class="news-card">
                <div class="news-body">
                    <div class="news-title">{{ $item['title'] }}</div>
                    <div class="news-date">
                        {{-- PERBAIKAN: Gunakan Carbon::parse() yang lebih aman --}}
                        @if(!empty($item['original_date']))
                            {{ \Carbon\Carbon::parse($item['original_date'])->locale('id')->translatedFormat('l, d F Y H:i') }}
                        @endif
                    </div>
                    <div class="news-content">
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
