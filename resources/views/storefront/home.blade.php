@extends('layouts.storefront')

@section('title', 'MixiShop - Món ngon gần bạn')

@push('styles')
<style>
  /* Giữ overlay của .stretched-link gói gọn trong card sản phẩm */
  .product-card { position: relative; } /* QUAN TRỌNG */

  /* Đảm bảo vùng hero (search) ở trên cùng mọi thứ phía dưới */
  .search-hero { position: relative; z-index: 2; }

  /* Tùy chọn: ngăn overlay ảnh hưởng bởi stacking context kỳ lạ ở vài trình duyệt cũ */
  .product-card .stretched-link::after { pointer-events: auto; }

  /* Tùy chọn: tăng z-index cho khung gợi ý */
  #suggestBox { z-index: 1050; }
</style>
@endpush

@section('hero')
<section class="search-hero py-4 py-lg-5">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <h1 class="h3 h-lg2 fw-bold mb-2">Đặt đồ ăn, giao hàng từ 20’</h1>
        <p class="mb-3">Tìm địa điểm, món ăn, cửa hàng...</p>

        <form method="GET" class="d-flex gap-2 position-relative" action="{{ route('home') }}" autocomplete="off" id="searchForm">
          <input id="searchInput" type="text" name="q" value="{{ $search }}"
                 class="form-control form-control-lg"
                 placeholder="Tìm món ăn, cửa hàng..."
                 aria-label="Tìm kiếm món ăn hoặc cửa hàng">
          <button class="btn btn-dark btn-lg" type="submit">
            <i class="fa-solid fa-magnifying-glass me-2"></i>Tìm
          </button>
          <div id="suggestBox" class="position-absolute w-100 mt-2 bg-white rounded-3 border shadow-sm d-none"
               style="top:100%; left:0;">
            <div id="suggestList" class="list-group list-group-flush"></div>
          </div>
        </form>

        <div class="mt-3 d-flex flex-wrap gap-2">
          <a href="{{ route('home') }}" class="category-chip {{ !$categorySlug ? 'bg-dark text-white' : '' }}">Tất cả</a>
          @foreach($categories as $cat)
            <a href="{{ route('home', ['category' => $cat->slug, 'q' => $search]) }}"
               class="category-chip {{ $categorySlug === $cat->slug ? 'bg-dark text-white' : '' }}">
              {{ $cat->name }}
            </a>
          @endforeach
        </div>
      </div>

      <div class="col-lg-5 d-none d-lg-block">
        <img class="w-100 rounded-3 shadow" src="{{ asset('images/products/banner.jpg') }}" alt="MixiShop banner">
      </div>
    </div>
  </div>
</section>
@endsection

@section('content')
<div class="container">
  <!-- Tính năng nổi bật -->
  <div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
      <div class="h-100 p-3 rounded-3 bg-white border d-flex align-items-center gap-3">
        <div class="text-danger fs-3"><i class="fa-solid fa-bolt"></i></div>
        <div>
          <div class="fw-semibold">Giao nhanh từ 20’</div>
          <div class="small text-muted">Phủ sóng rộng, shipper chủ động.</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="h-100 p-3 rounded-3 bg-white border d-flex align-items-center gap-3">
        <div class="text-warning fs-3"><i class="fa-solid fa-badge-percent"></i></div>
        <div>
          <div class="fw-semibold">Ưu đãi mỗi ngày</div>
          <div class="small text-muted">Mã giảm, combo siêu hời.</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="h-100 p-3 rounded-3 bg-white border d-flex align-items-center gap-3">
        <div class="text-success fs-3"><i class="fa-solid fa-headset"></i></div>
        <div>
          <div class="fw-semibold">Hỗ trợ 24/7</div>
          <div class="small text-muted">Sẵn sàng giúp bạn mọi lúc.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Danh mục nổi bật -->
  @if($categories->count())
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="h5 mb-0">Danh mục nổi bật</h2>
      <a class="small" href="{{ route('home') }}">Xem tất cả</a>
    </div>
    <div class="row g-3 mb-4">
      @foreach($categories->take(8) as $cat)
        <div class="col-6 col-md-3 col-lg-3">
          <a href="{{ route('categories.show', $cat->slug) }}" class="text-decoration-none">
            <div class="p-3 bg-white border rounded-3 h-100 d-flex align-items-center justify-content-between">
              <div>
                <div class="fw-semibold text-dark">{{ $cat->name }}</div>
                <div class="small text-muted">
                  {{ $cat->active_products_count ?? $cat->products()->where('is_active',1)->count() }} món
                </div>
              </div>
              <div class="text-secondary"><i class="fa-solid fa-chevron-right"></i></div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  @endif

  <!-- Banner ưu đãi -->
  <div class="mb-4">
    <div class="p-4 p-md-5 rounded-4"
         style="background:linear-gradient(135deg,#ffedd5,#fde68a); border:1px solid #ffe4b5;">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
          <div class="h5 mb-1">Nhập mã MIXI20 giảm 20k cho đơn đầu tiên</div>
          <div class="small text-muted">Áp dụng cho đơn từ 99k. Số lượng có hạn.</div>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-dark btn-lg">Đặt ngay</a>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h5 mb-0">Ưu đãi</h2>
    @if($search)
      <div class="text-muted">Kết quả cho “{{ $search }}”</div>
    @endif
  </div>

  @if($products->count() === 0)
    <div class="alert alert-light border">Không tìm thấy sản phẩm phù hợp.</div>
  @endif

  <div class="row g-3">
    @foreach($products as $product)
      <div class="col-6 col-md-4 col-lg-3">
        <div class="product-card h-100"> <!-- ĐÃ có position: relative từ CSS -->
          <img src="{{ $product->main_image }}" class="product-img" alt="{{ $product->name }}">
          <div class="p-3">
            <div class="small text-muted mb-1">{{ optional($product->category)->name }}</div>
            <div class="fw-semibold mb-1 text-truncate" title="{{ $product->name }}">{{ $product->name }}</div>
            <div class="d-flex align-items-center gap-2">
              <span class="price">{{ $product->formatted_price }}</span>
              @if($product->formatted_compare_price)
                <span class="compare">{{ $product->formatted_compare_price }}</span>
                <span class="badge text-bg-danger">-{{ $product->discount_percentage }}%</span>
              @endif
            </div>
            <a href="{{ route('products.show', $product->slug) }}" class="stretched-link" aria-label="Xem chi tiết {{ $product->name }}"></a>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-4">
    {{ $products->links() }}
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const input = document.getElementById('searchInput');
  const form  = document.getElementById('searchForm');
  const box   = document.getElementById('suggestBox');
  const list  = document.getElementById('suggestList');
  let timer;

  function hide(){ box.classList.add('d-none'); }
  function show(){ box.classList.remove('d-none'); }

  // Ngăn submit khi rỗng
  form.addEventListener('submit', (e)=>{
    const q = input.value.trim();
    if(q.length === 0){ e.preventDefault(); hide(); input.focus(); }
  });

  // Gợi ý
  input.addEventListener('input', () => {
    const q = input.value.trim();
    clearTimeout(timer);
    if(q.length < 2){ hide(); return; }
    timer = setTimeout(async () => {
      try {
        const res  = await fetch(`{{ route('home.suggest') }}?q=${encodeURIComponent(q)}`);
        const data = await res.json();
        if(!Array.isArray(data) || data.length === 0){ hide(); return; }
        list.innerHTML = data.map(item => `
          <a class="list-group-item list-group-item-action d-flex align-items-center gap-3" href="${item.url}">
            <img src="${item.image}" alt="${item.name}"
                 style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid #eee">
            <div class="flex-grow-1">
              <div class="fw-semibold">${item.name}</div>
              <div class="small text-muted">${item.category ?? ''}</div>
            </div>
            <div class="text-danger fw-semibold">${item.price}</div>
          </a>`).join('');
        show();
      } catch(e){ hide(); }
    }, 250);
  });

  // Enter để submit
  input.addEventListener('keydown', (e)=>{
    if(e.key === 'Enter'){
      const q = input.value.trim();
      if(q.length === 0){ e.preventDefault(); hide(); return; }
      e.preventDefault();
      hide();
      form.submit();
    }
  });

  // Ngăn nổi bọt để không kích hoạt link khác
  ['mousedown','click','touchstart'].forEach(ev=>{
    input.addEventListener(ev, (e)=>{ e.stopPropagation(); }, {passive: ev==='touchstart'});
    box.addEventListener(ev,   (e)=>{ e.stopPropagation(); }, {passive: ev==='touchstart'});
  });

  // Click ngoài để đóng gợi ý
  document.addEventListener('click', (e)=>{
    if(!box.contains(e.target) && e.target !== input){ hide(); }
  });
})();
</script>
@endpush
