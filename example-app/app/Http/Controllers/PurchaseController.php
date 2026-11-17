<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchases.index', ['purchases' => Purchase::with(['supplier', 'user'])->orderByDesc('purchase_date')->orderByDesc('id')->paginate(15)]);
    }

    public function create()
    {
        return view('purchases.form', ['purchase' => new Purchase(['purchase_date' => now(), 'status' => 'pending']), 'suppliers' => Supplier::orderBy('supplier_name')->get()]);
    }

    public function store(Request $request)
    {
        $purchase = new Purchase($this->validated($request));
        $purchase->user_id = $request->user()->id;
        $purchase->save();

        return redirect()->route('purchases.index')->with('status', 'Purchase created successfully.');
    }

    public function edit(Purchase $purchase)
    {
        return view('purchases.form', ['purchase' => $purchase, 'suppliers' => Supplier::orderBy('supplier_name')->get()]);
    }

    public function update(Request $request, Purchase $purchase)
    {
        $data = $this->validated($request);
        $updated = DB::transaction(function () use ($purchase, $data) {
            $locked = Purchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();
            if (DB::table('purchase_items')->where('purchase_id', $locked->id)->exists()) {
                return false;
            }
            $locked->update($data);

            return true;
        });

        return $updated
            ? redirect()->route('purchases.index')->with('status', 'Purchase updated successfully.')
            : $this->inUse();
    }

    public function destroy(Purchase $purchase)
    {
        if (DB::table('purchase_items')->where('purchase_id', $purchase->id)->exists()) {
            return $this->inUse();
        }

        try {
            $purchase->delete();
        } catch (QueryException $exception) {
            if (in_array($exception->errorInfo[1] ?? null, [1451, 19], true)) {
                return $this->inUse();
            }
            throw $exception;
        }

        return redirect()->route('purchases.index')->with('status', 'Purchase deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date_format:Y-m-d\TH:i'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['pending', 'received', 'cancelled'])],
        ]);
    }

    private function inUse()
    {
        return redirect()->route('purchases.index')->with('error', 'This purchase has line items and cannot be edited or deleted.');
    }
}
