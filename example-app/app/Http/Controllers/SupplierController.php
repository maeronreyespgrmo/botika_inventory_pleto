<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        return view('suppliers.index', ['suppliers' => Supplier::orderBy('supplier_name')->orderBy('id')->paginate(15)]);
    }

    public function create()
    {
        return view('suppliers.form', ['supplier' => new Supplier]);
    }

    public function store(Request $request)
    {
        Supplier::create($this->validated($request));

        return redirect()->route('suppliers.index')->with('status', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validated($request));

        return redirect()->route('suppliers.index')->with('status', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if (DB::table('purchases')->where('supplier_id', $supplier->id)->exists()) {
            return $this->inUse();
        }

        try {
            $supplier->delete();
        } catch (QueryException $exception) {
            if (in_array($exception->errorInfo[1] ?? null, [1451, 19], true)) {
                return $this->inUse();
            }

            throw $exception;
        }

        return redirect()->route('suppliers.index')->with('status', 'Supplier deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'supplier_name' => ['required', 'string', 'max:150'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function inUse()
    {
        return redirect()->route('suppliers.index')->with('error', 'This supplier has purchase records and cannot be deleted.');
    }
}
