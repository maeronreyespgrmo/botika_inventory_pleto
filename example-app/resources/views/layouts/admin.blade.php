@extends('layouts.base')
@section('body')
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light" aria-label="Main navigation">
        <ul class="navbar-nav">
            <li class="nav-item"><button type="button" class="nav-link btn btn-link" data-widget="pushmenu" aria-label="Toggle sidebar"><i class="fas fa-bars" aria-hidden="true"></i></button></li>
            <li class="nav-item d-none d-sm-inline-block"><a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a></li>
        </ul>
        <ul class="navbar-nav ml-auto align-items-center">
            <li class="nav-item mr-3 text-muted">{{ auth()->user()->full_name }}</li>
            <li class="nav-item"><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-sign-out-alt mr-1" aria-hidden="true"></i> Sign out</button></form></li>
        </ul>
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('dashboard') }}" class="brand-link text-center"><i class="fas fa-cubes mr-2" aria-hidden="true"></i><span class="brand-text font-weight-light"><b>Merr</b> Inventory</span></a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex"><div class="info text-white"><i class="fas fa-user-circle mr-2" aria-hidden="true"></i>{{ auth()->user()->full_name }}</div></div>
            <nav aria-label="Sidebar"><ul class="nav nav-pills nav-sidebar flex-column">
                <li class="nav-header">WORKSPACE</li>
                <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif><i class="nav-icon fas fa-tachometer-alt" aria-hidden="true"></i><p>Dashboard</p></a></li>
                <li class="nav-item"><a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" @if(request()->routeIs('categories.*')) aria-current="page" @endif><i class="nav-icon fas fa-tags" aria-hidden="true"></i><p>Categories</p></a></li>
                <li class="nav-item"><a href="{{ route('medicines.index') }}" class="nav-link {{ request()->routeIs('medicines.*') ? 'active' : '' }}" @if(request()->routeIs('medicines.*')) aria-current="page" @endif><i class="nav-icon fas fa-capsules" aria-hidden="true"></i><p>Medicines</p></a></li>
                <li class="nav-item"><a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" @if(request()->routeIs('suppliers.*')) aria-current="page" @endif><i class="nav-icon fas fa-truck" aria-hidden="true"></i><p>Suppliers</p></a></li>
                <li class="nav-item"><a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" @if(request()->routeIs('purchases.*')) aria-current="page" @endif><i class="nav-icon fas fa-shopping-bag" aria-hidden="true"></i><p>Purchases</p></a></li>
            </ul></nav>
        </div>
    </aside>
    <main class="content-wrapper">
        <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-8"><h1 class="m-0">@yield('title', 'Dashboard')</h1><p class="text-muted mt-1 mb-0">@yield('subtitle', 'Your inventory at a glance.')</p></div></div></div></div>
        <section class="content"><div class="container-fluid">@yield('content')</div></section>
    </main>
    <footer class="main-footer"><strong>Merr Inventory</strong><span class="float-right d-none d-sm-inline">{{ now()->year }}</span></footer>
</div>
@endsection




