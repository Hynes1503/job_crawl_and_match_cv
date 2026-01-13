@extends('layouts.app')
@section('title','Gửi phản ánh')
@section('content')
<div class="container py-4">
<form method="POST" action="{{ route('support.store') }}">
@csrf
<input name="title" class="form-control mb-2" placeholder="Tiêu đề" required>

<select name="category" class="form-control mb-2">
    <option value="bug">Báo lỗi</option>
    <option value="account">Tài khoản</option>
    <option value="payment">Thanh toán</option>
    <option value="other">Khác</option>
</select>

<select name="priority" class="form-control mb-2">
    <option value="low">Thấp</option>
    <option value="normal">Bình thường</option>
    <option value="high">Cao</option>
</select>

<textarea name="message" class="form-control mb-2" rows="5" placeholder="Nội dung..." required></textarea>

<button class="btn btn-success">Gửi</button>
</form>
</div>
@endsection
