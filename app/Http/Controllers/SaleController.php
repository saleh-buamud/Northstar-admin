<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function store(StoreSaleRequest $request, ActivityLogger $activityLogger): RedirectResponse
    {
        DB::transaction(function () use ($request, $activityLogger): void {
            $product = Product::query()->lockForUpdate()->findOrFail($request->integer('product_id'));
            abort_unless($product->status === 'active', 422, __('admin.messages.product_inactive'));
            abort_if($product->stock_quantity < $request->integer('quantity'), 422, __('admin.messages.insufficient_stock'));

            $quantity = $request->integer('quantity');
            $unitPrice = (float) ($product->sale_price ?? $product->price);
            $sale = Sale::query()->create([
                'product_id' => $product->id,
                'created_by' => $request->user()->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => round($unitPrice * $quantity, 2),
                'sold_at' => now(),
            ]);
            $product->decrement('stock_quantity', $quantity);
            $activityLogger->log('sale.recorded', $sale, ['product_id' => $product->id, 'quantity' => $quantity]);
        });

        return redirect()->route('dashboard')->with('success', __('admin.messages.sale_recorded'));
    }
}
