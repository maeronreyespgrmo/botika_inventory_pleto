@extends('layouts.admin')
@section('title', $category->exists ? 'Edit category' : 'Add category')
@section('subtitle', 'Set the category name and an optional description.')
@section('content')
<div class="row"><div class="col-lg-8">
<div class="card card-outline card-primary">
    <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
        @csrf
        @if($category->exists) @method('PUT') @endif
        <div class="card-body">
            <div class="form-group">
                <label for="category_name">Category name <span class="text-danger" aria-hidden="true">*</span></label>
                <input id="category_name" name="category_name" type="text" maxlength="100" required autofocus value="{{ old('category_name', $category->category_name) }}" class="form-control @error('category_name') is-invalid @enderror" @error('category_name') aria-invalid="true" aria-describedby="category_name_error" @enderror>
                @error('category_name')<div id="category_name_error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="description">Description <span class="text-muted font-weight-normal">(optional)</span></label>
                <textarea id="description" name="description" rows="4" maxlength="5000" class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="card-footer"><button type="submit" class="btn btn-primary">{{ $category->exists ? 'Save changes' : 'Create category' }}</button><a href="{{ route('categories.index') }}" class="btn btn-default ml-2">Cancel</a></div>
    </form>
</div>
</div></div>
@endsection
