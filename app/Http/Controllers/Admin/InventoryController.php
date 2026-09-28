<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InventoryReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdjustInventoryRequest;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $inventory) {}

    public function index(): View
    {
        $products = Product::query()
            ->with('category')
            ->orderBy('name')
            ->get();

        $movements = InventoryMovement::query()
            ->with(['product', 'creator'])
            ->latest()
            ->paginate(20);

        return view('admin.inventory.index', [
            'products' => $products,
            'movements' => $movements,
            'reasons' => InventoryReason::cases(),
        ]);
    }

    public function store(AdjustInventoryRequest $request): RedirectResponse
    {
        $product = Product::query()->findOrFail($request->integer('product_id'));

        $this->inventory->record(
            $product,
            $request->integer('quantity_change'),
            InventoryReason::from($request->validated('reason')),
            $request->validated('reference'),
        );

        return back()->with('success', 'Inventory updated.');
    }
}
