@extends('layouts.admin')
@section('title', $medicine->exists ? 'Edit medicine' : 'Add medicine')
@section('subtitle', 'Enter medicine details. Fields marked * are required.')
@section('content')
<div class="card card-outline card-primary">
<form method="POST" action="{{ $medicine->exists ? route('medicines.update', $medicine) : route('medicines.store') }}">
    @csrf
    @if($medicine->exists) @method('PUT') @endif
    <div class="card-body">
        @if($errors->any())<div class="alert alert-danger" role="alert">Please correct the highlighted fields.</div>@endif
        <div class="row">
        @foreach([
            ['generic_name', 'Generic name *', 150, true],
            ['brand_name', 'Brand name', 150, false],
            ['dosage_strength', 'Dosage strength', 50, false],
            ['dosage_form', 'Dosage form', 50, false],
            ['barcode', 'Barcode', 100, false],
            ['unit', 'Unit *', 30, true],
        ] as [$field, $label, $max, $required])
            <div class="col-md-6 form-group">
                <label for="{{ $field }}">{{ $label }}</label>
                <input type="text" id="{{ $field }}" name="{{ $field }}" maxlength="{{ $max }}" value="{{ old($field, $medicine->$field) }}" class="form-control @error($field) is-invalid @enderror" @required($required) @error($field) aria-invalid="true" aria-describedby="{{ $field }}_error" @enderror>
                @error($field)<div id="{{ $field }}_error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        @endforeach
            <div class="col-md-6 form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                    <option value="">Uncategorized</option>
                    @foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', $medicine->category_id) === (string) $category->id)>{{ $category->category_name }}</option>@endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group">
                <label for="selling_price">Selling price *</label>
                <input type="number" id="selling_price" name="selling_price" min="0" max="9999999999.99" step="0.01" required value="{{ old('selling_price', $medicine->selling_price) }}" class="form-control @error('selling_price') is-invalid @enderror">
                @error('selling_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group">
                <label for="reorder_level">Reorder level *</label>
                <input type="number" id="reorder_level" name="reorder_level" min="0" max="4294967295" step="1" required value="{{ old('reorder_level', $medicine->reorder_level) }}" class="form-control @error('reorder_level') is-invalid @enderror">
                @error('reorder_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        @foreach(['requires_prescription' => 'Requires prescription', 'is_active' => 'Active'] as $field => $label)
            <input type="hidden" name="{{ $field }}" value="0">
            <div class="custom-control custom-checkbox mb-2"><input type="checkbox" id="{{ $field }}" name="{{ $field }}" value="1" class="custom-control-input @error($field) is-invalid @enderror" @checked(old($field, $medicine->$field))><label class="custom-control-label" for="{{ $field }}">{{ $label }}</label>@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        @endforeach
    </div>
    <div class="card-footer"><button type="submit" class="btn btn-primary">{{ $medicine->exists ? 'Save changes' : 'Create medicine' }}</button><a href="{{ route('medicines.index') }}" class="btn btn-default ml-2">Cancel</a></div>
</form>
</div>
@endsection
