@extends('layouts.admin')
@section('title', $supplier->exists ? 'Edit supplier' : 'Add supplier')
@section('subtitle', 'Enter the supplier name and optional contact details.')
@section('content')
<div class="row"><div class="col-lg-8"><div class="card card-outline card-primary">
<form method="POST" action="{{ $supplier->exists ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
    @csrf
    @if($supplier->exists) @method('PUT') @endif
    <div class="card-body">
        @foreach([
            ['supplier_name', 'Supplier name *', 'text', 150, true],
            ['contact_person', 'Contact person', 'text', 100, false],
            ['phone', 'Phone', 'tel', 30, false],
            ['email', 'Email', 'email', 100, false],
        ] as [$field, $label, $type, $max, $required])
        <div class="form-group">
            <label for="{{ $field }}">{{ $label }}</label>
            <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" maxlength="{{ $max }}" value="{{ old($field, $supplier->$field) }}" class="form-control @error($field) is-invalid @enderror" @required($required) @error($field) aria-invalid="true" aria-describedby="{{ $field }}_error" @enderror>
            @error($field)<div id="{{ $field }}_error" class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        @endforeach
        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="4" maxlength="5000" class="form-control @error('address') is-invalid @enderror" @error('address') aria-invalid="true" aria-describedby="address_error" @enderror>{{ old('address', $supplier->address) }}</textarea>
            @error('address')<div id="address_error" class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="card-footer"><button type="submit" class="btn btn-primary">{{ $supplier->exists ? 'Save changes' : 'Create supplier' }}</button><a href="{{ route('suppliers.index') }}" class="btn btn-default ml-2">Cancel</a></div>
</form>
</div></div></div>
@endsection
