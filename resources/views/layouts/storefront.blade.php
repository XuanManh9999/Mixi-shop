<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MixiShop - Món ngon gần bạn')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='favGradient' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' style='stop-color:%23ff6b6b;stop-opacity:1' /><stop offset='100%' style='stop-color:%23ffa500;stop-opacity:1' /></linearGradient></defs><circle cx='50' cy='50' r='45' fill='url(%23favGradient)' opacity='0.9'/><circle cx='50' cy='50' r='38' fill='none' stroke='white' stroke-width='2'/><rect x='30' y='40' width='40' height='6' rx='3' fill='white'/><rect x='30' y='50' width='40' height='6' rx='3' fill='white'/><rect x='30' y='60' width='40' height='6' rx='3' fill='white'/></svg>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body{font-family:'Inter',sans-serif;background:#f7f7f9}
        .navbar-brand{font-weight:700}
        .search-hero{background:linear-gradient(135deg,#ff6b6b 0%,#ffa500 100%);color:#fff}
        /* Đảm bảo văn bản trong input hiển thị rõ trên nền trắng của ô tìm kiếm */
        .search-hero .form-control{color:#212529;background:#fff}
        .search-hero .form-control::placeholder{color:#6c757d;opacity:1}
        .search-hero .form-control:focus{color:#212529;background:#fff}
        .category-chip{border-radius:999px;border:1px solid #e9ecef;background:#fff;padding:.5rem 1rem;cursor:pointer;transition:.2s}
        .category-chip:hover{background:#f8f9fa}
        .product-card{border:1px solid #eee;border-radius:12px;overflow:hidden;background:#fff;transition:.2s}
        .product-card:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(0,0,0,.08)}
        .product-img{aspect-ratio:4/3;object-fit:cover;width:100%}
        .price{font-weight:700;color:#e5533d}
        .compare{text-decoration:line-through;color:#adb5bd;font-weight:500}
        /* Footer */
        .footer{background:#0f172a;color:#cbd5e1}
        .footer a{color:#cbd5e1;text-decoration:none}
        .footer a:hover{color:#fff}
        .footer-title{color:#fff;font-weight:600;margin-bottom:.75rem}
        .newsletter input{background:#0b1223;border:1px solid #23314f;color:#e2e8f0}
        .newsletter input:focus{border-color:#3b82f6;box-shadow:0 0 0 .2rem rgba(59,130,246,.15)}
        .newsletter .btn{background:#3b82f6;border-color:#3b82f6}
        .newsletter .btn:hover{background:#2563eb;border-color:#2563eb}
        .social-link{width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #23314f;border-radius:50%;transition:.2s}
        .social-link:hover{background:#23314f}
        .payment-badges img{height:26px;filter:grayscale(100%);opacity:.85;margin-right:10px}
        .payment-badges img:hover{filter:none;opacity:1}
        /* Custom Pagination */
        .pagination{margin-bottom:0}
        .pagination .page-link{border:1px solid #dee2e6;color:#6c757d;padding:.5rem .75rem;margin:0 2px;border-radius:6px;transition:all .2s}
        .pagination .page-link:hover{background:#f8f9fa;border-color:#adb5bd;color:#495057}
        .pagination .page-item.active .page-link{background:#007bff;border-color:#007bff;color:#fff}
        .pagination .page-item.disabled .page-link{color:#adb5bd;background:#fff;border-color:#dee2e6}
        .pagination .page-link:focus{box-shadow:0 0 0 .2rem rgba(0,123,255,.25)}
        /* Mobile responsive pagination */
        @media (max-width: 576px) {
            .pagination .page-link{padding:.375rem .5rem;font-size:.875rem}
            .pagination .page-item:not(.active):not(.disabled) .page-link{margin:0 1px}
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="40" height="40" class="me-2">
                    <defs>
                        <linearGradient id="storefrontGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#ff6b6b;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#ffa500;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <!-- Background circle -->
                    <circle cx="50" cy="50" r="45" fill="url(#storefrontGradient)" opacity="0.15"/>
                    <circle cx="50" cy="50" r="38" fill="none" stroke="url(#storefrontGradient)" stroke-width="2"/>
                    <!-- Hamburger icon -->
                    <rect x="30" y="40" width="40" height="6" rx="3" fill="url(#storefrontGradient)"/>
                    <rect x="30" y="50" width="40" height="6" rx="3" fill="url(#storefrontGradient)"/>
                    <rect x="30" y="60" width="40" height="6" rx="3" fill="url(#storefrontGradient)"/>
                </svg>
                <span style="font-weight: 700; color: #ff6b6b;">MixiShop</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item dropdown me-lg-3">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Mã giảm giá
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width:320px">
                            @forelse(($headerCoupons ?? collect()) as $cp)
                                <li>
                                    <div class="px-3 py-2 small">
                                        <div class="d-flex justify-content-between">
                                            <div class="fw-semibold">{{ $cp->code }}</div>
                                            <span class="badge text-bg-{{ $cp->status_color ?? 'success' }}">{{ $cp->status_label ?? 'Hoạt động' }}</span>
                                        </div>
                                        <div class="text-muted">Giảm: <b>{{ $cp->formatted_value ?? ($cp->type==='percentage' ? $cp->value.'%' : number_format($cp->value,0,',','.') . '₫') }}</b></div>
                                        @if($cp->end_at)
                                            <div class="text-muted">HSD: {{ $cp->end_at->format('d/m/Y') }}</div>
                                        @endif
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @empty
                                <li><div class="px-3 py-2 small text-muted">Chưa có ưu đãi.</div></li>
                            @endforelse
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('orders.track') }}">Tra cứu đơn hàng</a></li>
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user-edit me-2"></i>Thông tin cá nhân
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('orders.index') }}">
                                    <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Bảng điều khiển
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">@csrf
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Đăng nhập</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Đăng ký</a></li>
                    @endauth
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-dark position-relative" href="{{ url('/cart') }}">
                            <i class="fa-solid fa-cart-shopping me-1"></i>
                            Giỏ hàng
                            <span id="cartBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @yield('hero')

    <main class="py-4">
        @yield('content')
    </main>

    <footer class="footer mt-5 pt-5">
        <div class="container">
            <div class="row g-4 pb-4">
                <div class="col-lg-4">
                    <a class="navbar-brand text-white p-0 mb-2 d-inline-block" href="{{ route('home') }}">MixiShop</a>
                    <p class="mb-3">Đặt món ngon giao nhanh. Hơn cả bữa ăn, chúng tôi mang đến trải nghiệm.</p>
                    <div class="d-flex gap-2">
                        <a class="social-link" href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a class="social-link" href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a class="social-link" href="#" aria-label="Tiktok"><i class="fa-brands fa-tiktok"></i></a>
                        <a class="social-link" href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="footer-title">Về MixiShop</div>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><a href="#">Giới thiệu</a></li>
                        <li class="mb-2"><a href="#">Tuyển dụng</a></li>
                        <li class="mb-2"><a href="#">Blog</a></li>
                        <li class="mb-2"><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="footer-title">Hỗ trợ</div>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><a href="#">Trung tâm trợ giúp</a></li>
                        <li class="mb-2"><a href="#">Theo dõi đơn hàng</a></li>
                        <li class="mb-2"><a href="#">Hướng dẫn thanh toán</a></li>
                        <li class="mb-2"><a href="#">Chính sách vận chuyển</a></li>
                    </ul>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="footer-title">Nhận ưu đãi mỗi tuần</div>
                    <form class="newsletter" method="POST" action="#" onsubmit="event.preventDefault();">
                        <div class="input-group input-group-lg">
                            <input type="email" class="form-control" placeholder="Nhập email của bạn">
                            <button class="btn btn-primary" type="submit">Đăng ký</button>
                        </div>
                        <div class="form-text mt-2 text-secondary">Bằng việc đăng ký, bạn đồng ý nhận email tiếp thị từ MixiShop.</div>
                    </form>
                </div>
            </div>
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between border-top border-0" style="border-color:#23314f!important">
                <div class="py-3 small">© {{ date('Y') }} MixiShop. All rights reserved.</div>
                <div class="py-3 small d-flex align-items-center gap-3">
                    <a href="#">Điều khoản</a>
                    <a href="#">Quyền riêng tư</a>
                    <a href="#">Cookies</a>
                </div>
                <div class="py-3 payment-badges">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/MoMo_Logo.png" alt="MoMo">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/ZaloPay_logo.svg" alt="ZaloPay">
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
(function(){
  const STORAGE_KEY = 'mixishop_cart_v1';
  const badge = document.getElementById('cartBadge');

  function loadCart(){
    try { 
      const items = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
      // Auto-fix: Nếu giá quá cao (có thể bị nhân 100), tự động sửa
      let needsFix = false;
      items.forEach(item => {
        // Giá bình thường của sản phẩm nên < 500,000đ
        // Nếu giá > 1,000,000đ và chia hết cho 100, có thể bị lỗi
        if (item.price && item.price > 1000000 && item.price % 100 === 0) {
          const newPrice = item.price / 100;
          // Chỉ fix nếu giá mới hợp lý (từ 10k đến 500k)
          if (newPrice >= 10000 && newPrice <= 500000) {
            console.warn(`Auto-fixing price for ${item.name}: ${item.price} -> ${newPrice}`);
            item.price = newPrice;
            needsFix = true;
          }
        }
      });
      // Lưu lại nếu đã fix
      if (needsFix) {
        saveCart(items);
      }
      return items;
    }
    catch(e){ return []; }
  }
  function saveCart(items){ localStorage.setItem(STORAGE_KEY, JSON.stringify(items)); }
  function getCount(){ return loadCart().reduce((s,i)=> s + Number(i.quantity||0), 0); }
  function updateBadge(){
    if(!badge) return;
    const c = getCount();
    badge.textContent = c;
    badge.classList.toggle('d-none', c===0);
  }

  function parsePrice(val){
    // Nhận cả "19.000₫", "19,000", "19000"
    if (val == null) return NaN;
    const s = String(val).replace(/[^\d.,]/g,'').replace(/\./g,'').replace(',', '.');
    return parseFloat(s);
  }

  function toastOK(html){
    const wrap = document.createElement('div');
    wrap.className = 'position-fixed bottom-0 end-0 p-3';
    wrap.style.zIndex = '1080';
    wrap.innerHTML = `
      <div class="toast align-items-center show" role="alert">
        <div class="d-flex">
          <div class="toast-body">${html}</div>
          <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>`;
    document.body.appendChild(wrap);
    setTimeout(()=> wrap.remove(), 2000);
  }

  function addToCart(item){
    const items = loadCart();
    const idx = items.findIndex(i => i.id === item.id); // so sánh string-id
    if (idx > -1) { items[idx].quantity += item.quantity; }
    else { items.push(item); }
    saveCart(items);
    updateBadge();
    toastOK(`Đã thêm <b>${item.name}</b> vào giỏ.`);
  }

  // Delegate click cho nút .js-add-to-cart
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.js-add-to-cart');
    if(!btn) return;

    e.preventDefault();
    e.stopPropagation();

    const idRaw = (btn.dataset.id || '').trim();
    const name   = (btn.dataset.name || '').trim();
    const price  = parsePrice(btn.dataset.price);
    const qty    = Math.max(1, parseInt(btn.dataset.qty || '1', 10));
    const image  = btn.dataset.image || '';
    const slug   = btn.dataset.slug || '';

    // Validate: id phải có (string), price phải là số hợp lệ
    if (!idRaw) { console.warn('Missing data-id'); return; }
    if (!Number.isFinite(price) || price < 0) { console.warn('Invalid price', btn.dataset.price); return; }
    if (!name) { console.warn('Missing name'); return; }

    const item = { id: idRaw, slug, name, price, image, quantity: qty };
    addToCart(item);
  });

  // Render cart page nếu có #cartApp
  function renderCart(){
    const app = document.getElementById('cartApp');
    if(!app) { updateBadge(); return; }

    const tableBody = app.querySelector('tbody');
    const totalEl   = app.querySelector('#cartTotal');

    function refresh(){
      const items = loadCart();
      if(items.length === 0){
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Giỏ hàng trống</td></tr>';
        totalEl.textContent = '0₫';
        updateBadge();
        return;
      }
      let total = 0;
      tableBody.innerHTML = items.map((i, idx) => {
        const line = i.price * i.quantity;
        total += line;
        return `
          <tr data-idx="${idx}">
            <td>
              <img src="${i.image}" style="width:56px;height:56px;object-fit:cover;border-radius:8px;border:1px solid #eee" class="me-2">
              ${i.name}
            </td>
            <td class="text-nowrap">${Number(i.price).toLocaleString('vi-VN')}₫</td>
            <td style="max-width:120px">
              <div class="input-group input-group-sm">
                <button class="btn btn-outline-secondary js-qty-dec" type="button">-</button>
                <input class="form-control text-center js-qty" value="${i.quantity}">
                <button class="btn btn-outline-secondary js-qty-inc" type="button">+</button>
              </div>
            </td>
            <td class="text-nowrap">${line.toLocaleString('vi-VN')}₫</td>
            <td class="text-end"><button class="btn btn-sm btn-outline-danger js-remove" type="button">Xóa</button></td>
          </tr>`;
      }).join('');
      totalEl.textContent = total.toLocaleString('vi-VN') + '₫';
      updateBadge();
    }

    app.addEventListener('click', (e)=>{
      const row = e.target.closest('tr[data-idx]');
      if(!row) return;
      const idx = Number(row.dataset.idx);
      const items = loadCart();

      if (e.target.closest('.js-remove')) {
        items.splice(idx, 1);
        saveCart(items); refresh();
      }
      if (e.target.closest('.js-qty-inc')) {
        items[idx].quantity++;
        saveCart(items); refresh();
      }
      if (e.target.closest('.js-qty-dec')) {
        items[idx].quantity = Math.max(1, (items[idx].quantity - 1));
        saveCart(items); refresh();
      }
    });

    app.addEventListener('change', (e)=>{
      const row = e.target.closest('tr[data-idx]');
      if(!row) return;
      if (e.target.classList.contains('js-qty')) {
        const idx = Number(row.dataset.idx);
        const items = loadCart();
        items[idx].quantity = Math.max(1, parseInt(e.target.value || '1', 10));
        saveCart(items); refresh();
      }
    });

    refresh();
  }

  document.addEventListener('DOMContentLoaded', renderCart);
  updateBadge();
})();
</script>

    @stack('scripts')
</body>
</html>


