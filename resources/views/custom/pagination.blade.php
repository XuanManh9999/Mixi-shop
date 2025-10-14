@if ($paginator->hasPages())
    <div class="d-flex flex-column align-items-center">
        {{-- Results Info --}}
        <div class="mb-3 d-none d-sm-block">
            <p class="text-muted small mb-0">
                Hiển thị {{ $paginator->firstItem() }} đến {{ $paginator->lastItem() }} trong tổng số {{ $paginator->total() }} kết quả
            </p>
        </div>

        {{-- Pagination Navigation --}}
        <nav aria-label="Điều hướng phân trang">
            <ul class="pagination justify-content-center mb-0">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                            <span class="d-none d-sm-inline ms-1">Trước</span>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="Trang trước">
                            <i class="fas fa-chevron-left"></i>
                            <span class="d-none d-sm-inline ms-1">Trước</span>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}" title="Trang {{ $page }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" title="Trang sau">
                            <span class="d-none d-sm-inline me-1">Sau</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            <span class="d-none d-sm-inline me-1">Sau</span>
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
