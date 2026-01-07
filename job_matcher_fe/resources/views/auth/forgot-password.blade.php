@extends('layouts.app')

@section('content')
<div class="container col-md-4">
    <h3>Quên mật khẩu</h3>
    @if(session('success')) <div class="alert alert-success">{{session('success')}}</div> @endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <input type="email" name="email" class="form-control mb-2" placeholder="Nhập email" required>
        <button class="btn btn-primary w-100">Gửi link reset</button>
    </form>
</div>
@endsection
