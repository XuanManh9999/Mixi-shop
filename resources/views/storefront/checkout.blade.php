@extends('layouts.storefront')

@section('title', 'Thanh toán')

@section('content')
<div class="container">
    <h1 class="h4 mb-3">Thông tin thanh toán</h1>
    <div class="row g-4">
        <div class="col-lg-7">
            <form id="checkoutForm" method="POST" action="{{ route('checkout.place') }}">@csrf
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ tên</label>
                                <input name="customer_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input name="customer_phone" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Địa chỉ giao hàng</label>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4">
                                        <select id="provinceSelect" class="form-select" aria-label="Tỉnh/Thành phố">
                                            <option value="">Chọn Tỉnh/Thành phố</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="districtSelect" class="form-select" aria-label="Quận/Huyện" disabled>
                                            <option value="">Chọn Quận/Huyện</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="wardSelect" class="form-select" aria-label="Phường/Xã" disabled>
                                            <option value="">Chọn Phường/Xã</option>
                                        </select>
                                    </div>
                                </div>
                                <input id="streetInput" class="form-control mb-2" placeholder="Số nhà, tên đường, thôn/xóm...">
                                <textarea name="customer_address" id="addressTextarea" class="form-control" rows="3" required placeholder="Địa chỉ đầy đủ"></textarea>
                                <input type="hidden" name="ship_city" id="shipCityInput">
                                <input type="hidden" name="ship_district" id="shipDistrictInput">
                                <input type="hidden" name="ship_ward" id="shipWardInput">
                                <div class="form-text">Chọn Tỉnh/Quận/Xã và nhập số nhà, hệ thống sẽ tự ghép địa chỉ đầy đủ.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Phương thức thanh toán</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                                    <option value="vnpay">VNPay</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vận chuyển</label>
                                <select name="shipping_method" class="form-select" required>
                                    <option value="standard">Tiêu chuẩn</option>
                                    <option value="express">Hỏa tốc</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="items" id="itemsInput">
                <div class="text-end">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">Quay lại giỏ hàng</a>
                    <button type="submit" class="btn btn-dark" id="submitBtn">
                        <span class="btn-text">Lên đơn hàng</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </div>
            </form>

            <!-- Thông tin đơn hàng sau khi lên đơn thành công -->
            <div id="orderSuccess" class="d-none mt-4">
                <div class="alert alert-success">
                    <h4><i class="fas fa-check-circle me-2"></i>Lên đơn hàng thành công!</h4>
                    <p class="mb-0">Đơn hàng của bạn đã được tạo và đang chờ xác nhận.</p>
                </div>

                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-receipt me-3 fa-2x text-primary"></i>
                            <div>
                                <h4 class="mb-0 text-primary">Mã đơn hàng: #<span id="orderId"></span></h4>
                                <small class="text-muted">Vui lòng lưu lại mã này để tra cứu</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Thời gian:</strong> <span id="orderTime"></span></p>
                                <p><strong>Phương thức thanh toán:</strong> <span id="orderPaymentMethod"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Trạng thái:</strong> <span id="orderStatus" class="badge bg-warning"></span></p>
                                <p><strong>Tổng tiền:</strong> <span id="orderTotal" class="text-success fw-bold"></span></p>
                            </div>
                        </div>

                        <h6 class="mt-3">Sản phẩm đã đặt:</h6>
                        <div id="orderItems"></div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Đơn hàng đang chờ được xác nhận bởi quản trị viên</li>
                                <li>Chúng tôi sẽ liên hệ với bạn để xác nhận và thông báo thời gian giao hàng</li>
                                <li>Bạn có thể tra cứu đơn hàng bằng mã đơn hàng và số điện thoại</li>
                            </ul>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('orders.track') }}" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Tra cứu đơn hàng
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-1"></i>Về trang chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">Tóm tắt đơn hàng</div>
                <div class="card-body" id="summaryBody"></div>
                <div class="card-body border-top">
                    <div class="mb-3">
                        <label class="form-label">Mã giảm giá</label>
                        <div class="input-group">
                            <input id="couponInput" class="form-control" placeholder="Nhập mã (ví dụ: MIXI20)">
                            <button id="applyCouponBtn" class="btn btn-outline-secondary" type="button">Áp dụng</button>
                        </div>
                        <div id="couponHelp" class="form-text"></div>
                        <input type="hidden" name="coupon_code" id="couponCodeInput">
                    </div>
                    <div class="d-flex justify-content-between small mb-2"><span>Tạm tính</span><span id="summarySubtotal">0₫</span></div>
                    <div class="d-flex justify-content-between small mb-2"><span>Phí vận chuyển</span><span id="summaryShipping">0₫</span></div>
                    <div class="d-flex justify-content-between small mb-2 text-success d-none" id="discountRow"><span>Giảm giá</span><span id="summaryDiscount">-0₫</span></div>
                    <div class="d-flex justify-content-between"><div>Tổng cộng</div><div class="fw-semibold" id="summaryTotal">0₫</div></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    // Wait for DOM to be ready to avoid null elements
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCheckout);
    } else {
        initCheckout();
    }

    function initCheckout(){
        const STORAGE_KEY = 'mixishop_cart_v1';
        function loadCart(){
            try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }
            catch(e){ return []; }
        }

        const items = Array.isArray(loadCart()) ? loadCart() : [];
        const summaryBody = document.getElementById('summaryBody');
        const summaryTotal = document.getElementById('summaryTotal');
        const summarySubtotal = document.getElementById('summarySubtotal');
        const discountRow = document.getElementById('discountRow');
        const summaryShipping = document.getElementById('summaryShipping');
        const summaryDiscount = document.getElementById('summaryDiscount');
        const itemsInput = document.getElementById('itemsInput');
        const couponInput = document.getElementById('couponInput');
        const applyCouponBtn = document.getElementById('applyCouponBtn');
        const couponHelp = document.getElementById('couponHelp');
        const couponCodeHidden = document.getElementById('couponCodeInput');
        const couponCodeInput = document.getElementById('couponCodeInput');

        let discount = 0;
        let couponCode = '';

        function render(){
            if(!summaryBody) return;
            if(items.length === 0){
                summaryBody.innerHTML = '<div class="text-muted">Giỏ hàng trống.</div>';
                if (summarySubtotal) summarySubtotal.textContent = '0₫';
                if (summaryTotal) summaryTotal.textContent = '0₫';
                return;
            }
            let total = 0;
            summaryBody.innerHTML = items.map(i => {
                const price = Number(i.price) || 0;
                const qty = Number(i.quantity) || 0;
                const line = price * qty; total += line;
                return `<div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <img src="${i.image}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid #eee" class="me-2">
                        <div>
                            <div class="fw-semibold">${i.name}</div>
                            <div class="small text-muted">x${qty}</div>
                        </div>
                    </div>
                    <div>${line.toLocaleString('vi-VN')}₫</div>
                </div>`;
            }).join('');
            if (summarySubtotal) summarySubtotal.textContent = total.toLocaleString('vi-VN') + '₫';
            const payable = Math.max(0, total - discount);
            if (summaryTotal) summaryTotal.textContent = payable.toLocaleString('vi-VN') + '₫';
            if (discountRow) discountRow.classList.toggle('d-none', discount <= 0);
            if (summaryDiscount) summaryDiscount.textContent = '-' + discount.toLocaleString('vi-VN') + '₫';
            if (itemsInput) itemsInput.value = JSON.stringify(items.map(i => ({ id: Number(i.id), quantity: Number(i.quantity) })));
        }
        render();

        // ====== Address selects using provinces.open-api.vn ======
        const provinceSelect = document.getElementById('provinceSelect');
        const districtSelect = document.getElementById('districtSelect');
        const wardSelect = document.getElementById('wardSelect');
        const streetInput = document.getElementById('streetInput');
        const addressTextarea = document.getElementById('addressTextarea');
        const shipCityInput = document.getElementById('shipCityInput');
        const shipDistrictInput = document.getElementById('shipDistrictInput');
        const shipWardInput = document.getElementById('shipWardInput');

        async function fetchJSON(url){ const res = await fetch(url); return await res.json(); }

        async function loadProvinces(){
            if(!provinceSelect) return;
            const data = await fetchJSON('https://provinces.open-api.vn/api/p/');
            provinceSelect.innerHTML = '<option value="">Chọn Tỉnh/Thành phố</option>' + data.map(p=>`<option value="${p.code}" data-name="${p.name}">${p.name}</option>`).join('');
        }
        async function loadDistricts(provinceCode){
            if(!districtSelect) return;
            districtSelect.disabled = true; wardSelect.disabled = true;
            wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
            if(!provinceCode){ districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>'; return; }
            const data = await fetchJSON(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
            districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>' + (data.districts||[]).map(d=>`<option value="${d.code}" data-name="${d.name}">${d.name}</option>`).join('');
            districtSelect.disabled = false;
        }
        async function loadWards(districtCode){
            if(!wardSelect) return;
            wardSelect.disabled = true;
            if(!districtCode){ wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>'; return; }
            const data = await fetchJSON(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
            wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>' + (data.wards||[]).map(w=>`<option value="${w.code}" data-name="${w.name}">${w.name}</option>`).join('');
            wardSelect.disabled = false;
        }

        function composeAddress(){
            if(!addressTextarea) return;
            const street = (streetInput?.value || '').trim();
            const wardName = wardSelect?.selectedOptions[0]?.getAttribute('data-name') || '';
            const districtName = districtSelect?.selectedOptions[0]?.getAttribute('data-name') || '';
            const provinceName = provinceSelect?.selectedOptions[0]?.getAttribute('data-name') || '';
            const parts = [street, wardName, districtName, provinceName].filter(Boolean);
            addressTextarea.value = parts.join(', ');
            if (shipCityInput) shipCityInput.value = provinceName;
            if (shipDistrictInput) shipDistrictInput.value = districtName;
            if (shipWardInput) shipWardInput.value = wardName;
        }

        // Bind events
        if (provinceSelect) provinceSelect.addEventListener('change', (e)=>{ loadDistricts(e.target.value); composeAddress(); });
        if (districtSelect) districtSelect.addEventListener('change', (e)=>{ loadWards(e.target.value); composeAddress(); });
        if (wardSelect) wardSelect.addEventListener('change', composeAddress);
        if (streetInput) streetInput.addEventListener('input', composeAddress);
        loadProvinces();

        // Hiển thị phí ship theo lựa chọn
        const shippingSelect = document.querySelector('select[name="shipping_method"]');
        function updateShipping(){
            const fee = shippingSelect && shippingSelect.value === 'express' ? 60000 : 30000;
            if(summaryShipping) summaryShipping.textContent = fee.toLocaleString('vi-VN') + '₫';
            // cập nhật tổng
            const subtotal = items.reduce((s,i)=> s + ((Number(i.price)||0) * (Number(i.quantity)||0)), 0);
            const payable = Math.max(0, subtotal + fee - discount);
            if(summarySubtotal) summarySubtotal.textContent = subtotal.toLocaleString('vi-VN') + '₫';
            if(summaryTotal) summaryTotal.textContent = payable.toLocaleString('vi-VN') + '₫';
        }
        if (shippingSelect){
            shippingSelect.addEventListener('change', updateShipping);
            updateShipping();
        }

        async function applyCoupon(){
            if(!couponInput) return;
            const code = (couponInput.value||'').trim();
            if(code===''){ if(couponHelp){ couponHelp.textContent=''; couponHelp.className='form-text'; } discount=0; render(); return; }
            const subtotal = items.reduce((s,i)=> s + ((Number(i.price)||0) * (Number(i.quantity)||0)), 0);
            try{
                const url = new URL(`{{ route('coupon.validate') }}`, window.location.origin);
                url.searchParams.set('code', code);
                url.searchParams.set('amount', String(subtotal));
                const res = await fetch(url.toString());
                const data = await res.json();
                if(!data.ok){ if(couponHelp){ couponHelp.textContent = data.message || 'Mã không hợp lệ'; couponHelp.className='form-text text-danger'; } discount = 0; couponCode=''; if(couponCodeHidden) couponCodeHidden.value=''; }
                else { if(couponHelp){ couponHelp.textContent = `Đã áp dụng: ${data.code} (giảm ${Number(data.discount||0).toLocaleString('vi-VN')}₫)`; couponHelp.className='form-text text-success'; } discount = Number(data.discount)||0; couponCode = data.code; if(couponCodeHidden) couponCodeHidden.value = data.code; }
                render();
            }catch(e){ if(couponHelp){ couponHelp.textContent='Không thể áp dụng mã. Thử lại sau.'; couponHelp.className='form-text text-danger'; } }
        }
        if (applyCouponBtn) applyCouponBtn.addEventListener('click', applyCoupon);
        if (couponInput) couponInput.addEventListener('keydown', (e)=>{ if(e.key==='Enter'){ e.preventDefault(); applyCoupon(); } });

        // AJAX form submission
        const form = document.getElementById('checkoutForm');
        if (form) form.addEventListener('submit', async (e)=>{
            e.preventDefault(); // Prevent default form submission
            
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const spinner = submitBtn.querySelector('.spinner-border');
            
            // Show loading state
            submitBtn.disabled = true;
            btnText.textContent = 'Đang xử lý...';
            spinner.classList.remove('d-none');
            
            try {
                // Prepare form data
                composeAddress();
                if (couponCodeHidden && !couponCodeHidden.value && couponInput){
                    const raw = (couponInput.value||'').trim();
                    if (raw) couponCodeHidden.value = raw.toUpperCase();
                }

                // Submit via AJAX
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    // Hide form and show success
                    form.style.display = 'none';
                    document.getElementById('orderSuccess').classList.remove('d-none');
                    
                    // Populate order info
                    document.getElementById('orderId').textContent = result.order.id;
                    document.getElementById('orderTime').textContent = result.order.created_at;
                    document.getElementById('orderPaymentMethod').textContent = result.order.payment_method;
                    document.getElementById('orderStatus').textContent = result.order.status;
                    document.getElementById('orderTotal').textContent = result.order.total_amount;
                    
                    // Populate order items
                    const itemsHtml = result.order.items.map(item => `
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <div>
                                <strong>${item.name}</strong>
                                <br>
                                <small class="text-muted">${item.price} × ${item.quantity}</small>
                            </div>
                            <div class="text-end">
                                <strong>${item.total}</strong>
                            </div>
                        </div>
                    `).join('');
                    document.getElementById('orderItems').innerHTML = itemsHtml;
                    
                    // Clear cart
                    const STORAGE_KEY = 'mixishop_cart_v1';
                    try { localStorage.removeItem(STORAGE_KEY); } catch(e) {}
                    const badge = document.getElementById('cartBadge');
                    if (badge) { badge.textContent = '0'; badge.classList.add('d-none'); }
                    
                    // Scroll to success message
                    document.getElementById('orderSuccess').scrollIntoView({ behavior: 'smooth' });
                    
                } else {
                    alert(result.message || 'Có lỗi xảy ra. Vui lòng thử lại!');
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại!');
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                btnText.textContent = 'Lên đơn hàng';
                spinner.classList.add('d-none');
            }
        });
    }
})();
</script>
@endpush

@endsection


