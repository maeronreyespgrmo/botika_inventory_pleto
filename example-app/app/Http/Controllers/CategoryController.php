<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index', ['categories' => Category::orderBy('category_name')->paginate(15)]);
    }

    public function create()
    {
        return view('categories.form', ['category' => new Category]);
    }

    public function store(Request $request)
    {
        Category::create($this->validated($request));

        return redirect()->route('categories.index')->with('status', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('categories.index')->with('status', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if (DB::table('medicines')->where('category_id', $category->id)->exists()) {
            return $this->inUse();
        }

        try {
            $category->delete();
        } catch (QueryException $exception) {
            // A medicine may have been assigned after the initial check.
            if (in_array($exception->errorInfo[1] ?? null, [1451, 19], true)) {
                return $this->inUse();
            }

            throw $exception;
        }

        return redirect()->route('categories.index')->with('status', 'Category deleted successfully.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'category_name' => ['required', 'string', 'max:100', Rule::unique('categories', 'category_name')->ignore($category)],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function inUse()
    {
        return redirect()->route('categories.index')->with('error', 'This category is used by medicines and cannot be deleted. Reassign those medicines first.');
    }
}
