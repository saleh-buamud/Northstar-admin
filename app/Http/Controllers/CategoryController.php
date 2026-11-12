<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        return view('categories.index', [
            'categories' => Category::query()
                ->withCount('products')
                ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request): void {
                    $query->where('name', 'like', '%'.$request->string('q')->value().'%')
                        ->orWhere('slug', 'like', '%'.$request->string('q')->value().'%');
                }))
                ->when($request->filled('status') && in_array($request->input('status'), ['active', 'inactive'], true), fn ($query) => $query->where('status', $request->input('status')))
                ->orderBy('name')
                ->paginate($perPage)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request, ActivityLogger $activityLogger): RedirectResponse
    {
        $category = DB::transaction(function () use ($request, $activityLogger): Category {
            $category = Category::query()->create($request->validated() + ['slug' => Str::slug($request->string('name')->value())]);
            $activityLogger->log('category.created', $category);

            return $category;
        });

        return redirect()->route('categories.index')->with('success', __('admin.messages.category_created'));
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', ['category' => $category]);
    }

    public function update(UpdateCategoryRequest $request, Category $category, ActivityLogger $activityLogger): RedirectResponse
    {
        DB::transaction(function () use ($request, $category, $activityLogger): void {
            $category->update($request->validated() + ['slug' => Str::slug($request->string('name')->value())]);
            $activityLogger->log('category.updated', $category);
        });

        return redirect()->route('categories.index')->with('success', __('admin.messages.category_updated'));
    }

    public function destroy(Category $category, ActivityLogger $activityLogger): RedirectResponse
    {
        abort_if($category->products()->exists(), 422, __('admin.messages.category_has_products'));
        DB::transaction(function () use ($category, $activityLogger): void {
            $activityLogger->log('category.deleted', $category);
            $category->delete();
        });

        return redirect()->route('categories.index')->with('success', __('admin.messages.category_deleted'));
    }
}
