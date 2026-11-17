<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    public function index()
    {
        return view('medicines.index', ['medicines' => Medicine::with('category')->orderBy('generic_name')->orderBy('id')->paginate(15)]);
    }

    public function create()
    {
        return view('medicines.form', [
            'medicine' => new Medicine(['reorder_level' => 10, 'is_active' => true]),
            'categories' => Category::orderBy('category_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Medicine::create($this->validated($request));

        return redirect()->route('medicines.index')->with('status', 'Medicine created successfully.');
    }

    public function edit(Medicine $medicine)
    {
        return view('medicines.form', ['medicine' => $medicine, 'categories' => Category::orderBy('category_name')->get()]);
    }

    public function update(Request $request, Medicine $medicine)
    {
        $medicine->update($this->validated($request, $medicine));

        return redirect()->route('medicines.index')->with('status', 'Medicine updated successfully.');
    }

    public function destroy(Medicine $medicine)
    {
        if (DB::table('stock_batches')->where('medicine_id', $medicine->id)->exists()
            || DB::table('sale_items')->where('medicine_id', $medicine->id)->exists()) {
            return $this->inUse();
        }

        try {
            $medicine->delete();
        } catch (QueryException $exception) {
            if (in_array($exception->errorInfo[1] ?? null, [1451, 19], true)) {
                return $this->inUse();
            }

            throw $exception;
        }

        return redirect()->route('medicines.index')->with('status', 'Medicine deleted successfully.');
    }

    private function validated(Request $request, ?Medicine $medicine = null): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'generic_name' => ['required', 'string', 'max:150'],
            'brand_name' => ['nullable', 'string', 'max:150'],
            'dosage_strength' => ['nullable', 'string', 'max:50'],
            'dosage_form' => ['nullable', 'string', 'max:50'],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('medicines', 'barcode')->ignore($medicine)],
            'unit' => ['required', 'string', 'max:30'],
            'selling_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
            'reorder_level' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'requires_prescription' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    private function inUse()
    {
        return redirect()->route('medicines.index')->with('error', 'This medicine has stock batches or sales and cannot be deleted. Mark it inactive instead.');
    }
}
