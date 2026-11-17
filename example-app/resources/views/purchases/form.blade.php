@extends('layouts.admin')
@section('title', $purchase->exists ? 'Edit purchase' : 'Add purchase')
@section('subtitle', 'Enter the supplier, date, reference, and purchase status.')
@section('content')
<div class="row"><div class="col-lg-8"><div class="card card-outline card-primary">
<form method="POST" action="{{ $purchase->exists ? route('purchases.update', $purchase) : route('purchases.store') }}">
    @csrf
    @if($purchase->exists) @method('PUT') @endif
    <div class="card-body">
        @if($suppliers->isEmpty())<div class="alert alert-info">Add a <a href="{{ route('suppliers.create') }}">supplier</a> before creating a purchase.</div>@endif
        <div class="form-group"><label for="supplier_id">Supplier *</label>
            <select id="supplier_id" name="supplier_id" required class="form-control @error('supplier_id') is-invalid @enderror"><option value="">Select a supplier</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected((string) old('supplier_id', $purchase->supplier_id) === (string) $supplier->id)>{{ $supplier->supplier_name }}</option>@endforeach</select>
            @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group"><label for="purchase_date">Purchase date *</label><input type="datetime-local" id="purchase_date" name="purchase_date" required value="{{ old('purchase_date', $purchase->purchase_date?->format('Y-m-d\TH:i')) }}" class="form-control @error('purchase_date') is-invalid @enderror">@error('purchase_date')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="form-group"><label for="reference_number">Reference number (optional)</label><input type="text" id="reference_number" name="reference_number" maxlength="100" value="{{ old('reference_number', $purchase->reference_number) }}" class="form-control @error('reference_number') is-invalid @enderror">@error('reference_number')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="form-group"><label for="status">Status *</label><select id="status" name="status" required class="form-control @error('status') is-invalid @enderror">@foreach(['pending', 'received', 'cancelled'] as $status)<option value="{{ $status }}" @selected(old('status', $purchase->status) === $status)>{{ ucfirst($status) }}</option>@endforeach</select>@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <p class="text-muted small mb-0">This form records purchase details only. Changing status does not add stock; purchase items and stock receiving are managed separately.</p>
    </div>
    <div class="card-footer"><button type="submit" class="btn btn-primary" @disabled($suppliers->isEmpty())>{{ $purchase->exists ? 'Save changes' : 'Create purchase' }}</button><a href="{{ route('purchases.index') }}" class="btn btn-default ml-2">Cancel</a></div>
</form>
</div></div></div>
@endsection
