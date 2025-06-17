@extends('layouts.app')

@section('content')
<div class="col-md-10 main-content">
    {{-- Search Bar --}}
    <div class="search-container mb-4">
        <div class="search-bar">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Cari berita saham...">
        </div>
    </div>

    <div class="content-section">
        <div class="section-title">
            Semua Berita Saham
        </div>

        {{-- News Grid --}}
        <div class="row g-4 mt-3">
            @forelse ($news as $item)
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="news-card-wrapper" data-bs-toggle="modal" data-bs-target="#newsModal-{{ $loop->index }}">
                        <div class="news-card">
                            <div class="news-body">
                                <div class="news-title">{{ $item['title'] }}</div>
                                <div class="news-date">{{ $item['original_date'] }}</div>
                                <div class="news-content">
                                    {{ \Illuminate\Support\Str::limit($item['summary'], 150) }}
                                </div>
                                <div class="news-read-more">Baca selengkapnya</div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal for each news item --}}
                    <div class="modal fade" id="newsModal-{{ $loop->index }}" tabindex="-1" aria-labelledby="newsModalLabel-{{ $loop->index }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="newsModalLabel-{{ $loop->index }}">{{ $item['title'] }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted small">{{ $item['original_date'] }}</span>
                                        @if(isset($item['source']))
                                            <span class="badge bg-primary">{{ $item['source'] }}</span>
                                        @endif
                                    </div>
                                    <div class="news-modal-content">
                                        {!! nl2br(e($item['original_content'])) !!}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        Tidak ada berita yang tersedia untuk ditampilkan.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-5">
            {{ $news->links('layouts.pagination') }}
        </div>
    </div>
</div>
@endsection