@extends('layouts.simple')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>로그인</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/login">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">이메일</label>
                        <input id="email" type="email" class="form-control" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">비밀번호</label>
                        <input id="password" type="password" class="form-control" name="password" required>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input id="remember" type="checkbox" class="form-check-input" name="remember">
                            <label class="form-check-label" for="remember">로그인 상태 유지</label>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">로그인</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <a href="/register">회원가입</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection