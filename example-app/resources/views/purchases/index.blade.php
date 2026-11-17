@extends('layouts.admin')
@section('title', 'Purchases')
@section('subtitle', 'Manage purchase records and supplier references.')
@section('content')
@if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
@if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
<div class="card">
    <div class="card-header d-flex align-items-center"><h2 class="card-title">Purchase records</h2><a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-plus mr-1" aria-hidden="true"></i>Add purchase</a></div>
    <div class="card-body table-responsive p-0"><table class="table table-hover mb-0">
        <thead><tr><th scope="col">Purchase</th><th scope="col">Supplier</th><th scope="col">Date</th><th scope="col">Reference</th><th scope="col">Status</th><th scope="col">Created by</th><th scope="col" class="text-right">Actions</th></tr></thead>
        <tbody>
        @forelse($purchases as $purchase)
            <tr><td>#{{ $purchase->id }}</td><td>{{ $purchase->supplier->supplier_name }}</td><td class="text-nowrap">{{ $purchase->purchase_date->format('Y-m-d H:i') }}</td><td>{{ $purchase->reference_number ?? '—' }}</td><td><span class="badge badge-{{ ['pending' => 'warning', 'received' => 'success', 'cancelled' => 'secondary'][$purchase->status] }}">{{ ucfirst($purchase->status) }}</span></td><td>{{ $purchase->user->full_name }}</td>
                <td class="text-right text-nowrap"><a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-outline-primary btn-sm" aria-label="Edit purchase {{ $purchase->id }}">Edit</a>
                    <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" class="d-inline" onsubmit="return confirm('Delete this purchase? This cannot be undone.');">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Delete purchase {{ $purchase->id }}">Delete</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-5">No purchases yet. Add a purchase to get started.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($purchases->hasPages())<div class="card-footer">{{ $purchases->links('pagination::bootstrap-4') }}</div>@endif
</div>
@endsection
