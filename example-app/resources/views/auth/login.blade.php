@extends('layouts.base')
@section('title', 'Sign in')
@section('body-class', 'hold-transition login-page')
@section('body')
<main class="login-box" style="max-width: calc(100% - 32px)">
    <div class="login-logo"><b>Merr</b> Inventory</div>
    <div class="card card-outline card-primary">
        <div class="card-body login-card-body">
            <h1 class="h4 text-center">Welcome back</h1>
            <p class="login-box-msg">Sign in to your inventory dashboard</p>
            @if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
            @if($errors->any())
                <div class="alert alert-danger" role="alert"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <label for="username">Username</label>
                <div class="input-group mb-3">
                    <input id="username" name="username" type="text" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" autocomplete="username" required autofocus>
                    <div class="input-group-append"><div class="input-group-text"><span class="fas fa-user" aria-hidden="true"></span></div></div>
                </div>
                <label for="password">Password</label>
                <div class="input-group mb-3">
                    <input id="password" name="password" type="password" class="form-control" autocomplete="current-password" required>
                    <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock" aria-hidden="true"></span></div></div>
                </div>
                <div class="custom-control custom-checkbox mb-3">
                    <input id="remember" name="remember" type="checkbox" value="1" class="custom-control-input" @checked(old('remember'))>
                    <label for="remember" class="custom-control-label">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Sign in <span class="fas fa-arrow-right ml-2" aria-hidden="true"></span></button>
            </form>
        </div>
    </div>
    <p class="text-center text-muted small">Contact your administrator if you need an account.</p>
</main>
@endsection

