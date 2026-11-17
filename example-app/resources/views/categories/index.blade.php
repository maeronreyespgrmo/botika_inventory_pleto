@extends('layouts.admin')
@section('title', 'Categories')
@section('subtitle', 'Organize medicines into categories.')
@section('content')
@if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
@if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h2 class="card-title">Medicine categories</h2>
        <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-plus mr-1" aria-hidden="true"></i>Add category</a>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th scope="col">Name</th><th scope="col">Description</th><th scope="col" class="text-right">Actions</th></tr></thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>{{ $category->category_name }}</td><td style="overflow-wrap:anywhere">{{ $category->description ?: '—' }}</td>
                    <td class="text-right text-nowrap">
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary btn-sm" aria-label="Edit {{ $category->category_name }}">Edit</a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category? This cannot be undone.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Delete {{ $category->category_name }}">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-5">No categories yet. Add a category to get started.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())<div class="card-footer">{{ $categories->links('pagination::bootstrap-4') }}</div>@endif
</div>
@endsection
