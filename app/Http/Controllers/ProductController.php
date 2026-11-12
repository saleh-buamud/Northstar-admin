<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->string('sort')->value();
        $direction = $request->string('direction')->value() === 'asc' ? 'asc' : 'desc';
        $sort = in_array($sort, ['name', 'sku', 'price', 'stock_quantity', 'created_at'], true) ? $sort : 'created_at';
        $perPage = $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $products = Product::query()
            ->with('category')
            ->withCount('sales')
            ->when($request->filled('q'), fn($query) => $query->where(function ($query) use ($request): void {
                $query->where('name', 'like', '%' . $request->string('q')->value() . '%')
                    ->orWhere('sku', 'like', '%' . $request->string('q')->value() . '%');
            }))
            ->when($request->filled('status') && in_array($request->input('status'), ['active', 'inactive', 'draft'], true), fn($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('category_id'), fn($query) => $query->where('category_id', $request->integer('category_id')))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(),
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create(): View
    {
        return view('products.create', ['categories' => Category::query()->orderBy('name')->get()]);
    }

    public function store(StoreProductRequest $request, ActivityLogger $activityLogger): RedirectResponse
    {
        $imagePath = $request->file('image')?->store('products', 'public');

        try {
            $product = DB::transaction(function () use ($request, $imagePath, $activityLogger): Product {
                $product = Product::query()->create($request->safe()->except('image') + ['image_path' => $imagePath]);
                $activityLogger->log('product.created', $product);

                return $product;
            });
        } catch (Throwable $exception) {
            if ($imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        return redirect()->route('products.show', $product)->with('success', __('admin.messages.product_created'));
    }

    public function show(Product $product): View
    {
        $this->authorize('view', $product);

        return view('products.show', ['product' => $product->load('category')]);
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        return view('products.edit', [
            'product' => $product,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product, ActivityLogger $activityLogger): RedirectResponse
    {
        $this->authorize('update', $product);
        $oldImage = $product->image_path;
        $newImage = $request->file('image')?->store('products', 'public');
        $removeImage = $request->boolean('remove_image');

        try {
            DB::transaction(function () use ($request, $product, $activityLogger, $newImage, $removeImage): void {
                $attributes = $request->safe()->except(['image', 'remove_image']);

                if ($newImage !== null) {
                    $attributes['image_path'] = $newImage;
                } elseif ($removeImage) {
                    $attributes['image_path'] = null;
                }

                $product->update($attributes);
                $activityLogger->log('product.updated', $product);
            });
        } catch (Throwable $exception) {
            if ($newImage !== null) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }

        if (($newImage !== null || $removeImage) && $oldImage !== null) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('products.show', $product)->with('success', __('admin.messages.product_updated'));
    }

    public function destroy(Product $product, ActivityLogger $activityLogger): RedirectResponse
    {
        $this->authorize('delete', $product);
        abort_if($product->sales()->exists(), 422, __('admin.messages.product_has_sales'));

        DB::transaction(function () use ($product, $activityLogger): void {
            $activityLogger->log('product.deleted', $product);
            $product->delete();
        });

        if ($product->image_path !== null) {
            Storage::disk('public')->delete($product->image_path);
        }

        return redirect()->route('products.index')->with('success', __('admin.messages.product_deleted'));
    }
}
