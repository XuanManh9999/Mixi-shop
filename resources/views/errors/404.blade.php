@extends('layouts.storefront')

@section('title', 'Không tìm thấy trang')

@section('content')
<div class="container text-center py-5">
    <div class="display-1 text-muted mb-4">404</div>
    <h2 class="mb-3">Không tìm thấy trang</h2>
    <p class="lead mb-4">Trang bạn đang tìm kiếm không tồn tại hoặc đã được di chuyển.</p>
    <a href="{{ route('home') }}" class="btn btn-primary">Về trang chủ</a>
</div>
@endsection
