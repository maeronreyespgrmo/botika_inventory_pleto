@extends('layouts.admin')
@section('title', 'Medicines')
@section('subtitle', 'Manage your medicine catalog, prices, and availability.')
@section('content')
@if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
@if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
<div class="card">
    <div class="card-header d-flex align-items-center"><h2 class="card-title">Medicine catalog</h2><a href="{{ route('medicines.create') }}" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-plus mr-1" aria-hidden="true"></i>Add medicine</a></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th scope="col">Medicine</th><th scope="col">Category</th><th scope="col">Barcode</th><th scope="col">Price / unit</th><th scope="col">Reorder level</th><th scope="col">Status</th><th scope="col" class="text-right">Actions</th></tr></thead>
            <tbody>
            @forelse($medicines as $medicine)
                <tr>
                    <td><strong>{{ $medicine->generic_name }}</strong><div class="small text-muted">{{ $medicine->brand_name }} {{ $medicine->dosage_strength }} {{ $medicine->dosage_form }}</div>@if($medicine->requires_prescription)<span class="badge badge-info">Prescription required</span>@endif</td>
                    <td>{{ $medicine->category?->category_name ?? 'Uncategorized' }}</td><td>{{ $medicine->barcode ?? '—' }}</td>
                    <td>{{ number_format($medicine->selling_price, 2) }} / {{ $medicine->unit }}</td><td>{{ $medicine->reorder_level }}</td>
                    <td><span class="badge badge-{{ $medicine->is_active ? 'success' : 'secondary' }}">{{ $medicine->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-right text-nowrap"><a href="{{ route('medicines.edit', $medicine) }}" class="btn btn-outline-primary btn-sm" aria-label="Edit {{ $medicine->generic_name }}">Edit</a>
                        <form action="{{ route('medicines.destroy', $medicine) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this medicine? This cannot be undone.');">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm" type="submit" aria-label="Delete {{ $medicine->generic_name }}">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No medicines yet. Add a medicine to get started.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($medicines->hasPages())<div class="card-footer">{{ $medicines->links('pagination::bootstrap-4') }}</div>@endif
</div>
@endsection
