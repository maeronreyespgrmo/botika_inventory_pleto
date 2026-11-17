@extends('layouts.admin')
@section('title', 'Suppliers')
@section('subtitle', 'Manage supplier contacts and addresses.')
@section('content')
@if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
@if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
<div class="card">
    <div class="card-header d-flex align-items-center"><h2 class="card-title">Supplier directory</h2><a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-plus mr-1" aria-hidden="true"></i>Add supplier</a></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th scope="col">Supplier</th><th scope="col">Contact person</th><th scope="col">Phone</th><th scope="col">Email</th><th scope="col">Address</th><th scope="col" class="text-right">Actions</th></tr></thead>
            <tbody>
            @forelse($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->supplier_name }}</td><td>{{ $supplier->contact_person ?? '—' }}</td><td>{{ $supplier->phone ?? '—' }}</td><td>{{ $supplier->email ?? '—' }}</td><td style="white-space:pre-line;overflow-wrap:anywhere">{{ $supplier->address ?? '—' }}</td>
                    <td class="text-right text-nowrap"><a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-primary btn-sm" aria-label="Edit {{ $supplier->supplier_name }}">Edit</a>
                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this supplier? This cannot be undone.');">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Delete {{ $supplier->supplier_name }}">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No suppliers yet. Add a supplier to get started.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())<div class="card-footer">{{ $suppliers->links('pagination::bootstrap-4') }}</div>@endif
</div>
@endsection
