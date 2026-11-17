@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="row">
    @foreach([
        ['Medicines', $medicineCount, 'bg-info', 'fa-capsules'],
        ['Active medicines', $activeCount, 'bg-success', 'fa-check-circle'],
        ['Suppliers', $supplierCount, 'bg-warning', 'fa-truck'],
        ['Purchases', $purchaseCount, 'bg-primary', 'fa-shopping-bag'],
    ] as [$label, $count, $color, $icon])
    <div class="col-lg-3 col-6"><div class="small-box {{ $color }}"><div class="inner"><h2 class="h1 font-weight-bold">{{ number_format($count) }}</h2><p>{{ $label }}</p></div><div class="icon"><i class="fas {{ $icon }}" aria-hidden="true"></i></div></div></div>
    @endforeach
</div>
<div class="card">
    <div class="card-header"><h2 class="card-title">Recently added medicines</h2><span class="float-right text-muted small">Latest 8 records</span></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap mb-0">
            <thead><tr><th scope="col">Medicine</th><th scope="col">Brand</th><th scope="col">Strength</th><th scope="col">Unit price</th><th scope="col">Status</th></tr></thead>
            <tbody>
                @forelse($medicines as $medicine)
                    <tr><td>{{ $medicine->generic_name }}</td><td>{{ $medicine->brand_name ?? '—' }}</td><td>{{ $medicine->dosage_strength ?? '—' }}</td><td>{{ number_format($medicine->selling_price, 2) }}</td><td><span class="badge badge-{{ $medicine->is_active ? 'success' : 'secondary' }}">{{ $medicine->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-box-open fa-2x mb-3 d-block" aria-hidden="true"></i>No medicines yet. Your medicine records will appear here.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
