<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SparePart;
use App\Models\SparePartStockMovement;
use App\Models\Supplier;
use App\Services\DynamicFormConfig;
use App\Services\TenantContext;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SparePartController extends Controller
{
    /**
     * Common categories for mobile spare parts.
     *
     * @var list<string>
     */
    public static array $categories = [
        'Display / Screen',
        'Battery',
        'Charging Port / Sub-board',
        'Camera (Back / Front)',
        'Speaker / Earpiece',
        'Housing / Middle Frame',
        'Back Glass / Door',
        'Motherboard IC / Chipset',
        'Flex Cable / Ribbon',
        'Fingerprint / Sensor',
        'Volume / Power Button',
        'SIM Tray / Card Reader',
        'Other Components',
    ];

    /**
     * Display a listing of spare parts in the catalogue.
     */
    public function index(Request $request): View
    {
        $tenant = TenantContext::get();
        abort_unless($tenant, 403, 'Tenant context not found.');

        $query = SparePart::where('tenant_id', $tenant->id)->with('supplier');

        // Status filter: all, in_stock, low_stock, out_of_stock
        $stockStatus = $request->get('stock_status', 'all');
        if ($stockStatus === 'in_stock') {
            $query->inStock();
        } elseif ($stockStatus === 'low_stock') {
            $query->lowStock();
        } elseif ($stockStatus === 'out_of_stock') {
            $query->outOfStock();
        }

        // Category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Search query (Name, Brand, SKU, Barcode, Compatible Models)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('compatible_models', 'like', "%{$search}%");
            });
        }

        $parts = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_items' => SparePart::where('tenant_id', $tenant->id)->count(),
            'in_stock_count' => SparePart::where('tenant_id', $tenant->id)->inStock()->count(),
            'total_quantity' => (int) SparePart::where('tenant_id', $tenant->id)->sum('stock_quantity'),
            'low_stock_count' => SparePart::where('tenant_id', $tenant->id)->lowStock()->count(),
            'out_of_stock_count' => SparePart::where('tenant_id', $tenant->id)->outOfStock()->count(),
            'inventory_value' => (float) SparePart::where('tenant_id', $tenant->id)->selectRaw('SUM(stock_quantity * purchase_price) as val')->value('val'),
        ];

        $categories = self::$categories;

        return view('spare_parts.index', compact('tenant', 'parts', 'stats', 'stockStatus', 'categories'));
    }

    /**
     * Show form to create a new spare part.
     */
    public function create(): View
    {
        $tenant = TenantContext::get();
        abort_unless($tenant, 403, 'Tenant context not found.');

        $suppliers = Supplier::where('tenant_id', $tenant->id)->orderBy('name')->get();
        $categories = self::$categories;

        return view('spare_parts.create', compact('tenant', 'suppliers', 'categories'));
    }

    /**
     * Store a newly created spare part.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant, 403, 'Tenant context not found.');

        $isPurchaseCostRequired = DynamicFormConfig::isFieldRequired($tenant, 'spare_part', 'purchase_price');
        $isSellingPriceRequired = DynamicFormConfig::isFieldRequired($tenant, 'spare_part', 'selling_price');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'compatible_models' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'specifications' => ['nullable', 'string'],
            'purchase_price' => [$isPurchaseCostRequired ? 'required' : 'nullable', 'numeric', 'min:0'],
            'selling_price' => [$isSellingPriceRequired ? 'required' : 'nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock_level' => ['nullable', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:100'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        $part = DB::transaction(function () use ($tenant, $validated) {
            $qty = max(0, (int) ($validated['stock_quantity'] ?? 0));
            $purchasePrice = (float) ($validated['purchase_price'] ?? 0.00);

            $sparePart = SparePart::create([
                'tenant_id' => $tenant->id,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'name' => $validated['name'],
                'category' => $validated['category'],
                'brand' => $validated['brand'] ?? null,
                'compatible_models' => $validated['compatible_models'] ?? null,
                'sku' => ! empty($validated['sku']) ? trim($validated['sku']) : null,
                'barcode' => ! empty($validated['barcode']) ? trim($validated['barcode']) : null,
                'specifications' => $validated['specifications'] ?? null,
                'purchase_price' => $purchasePrice,
                'selling_price' => (float) ($validated['selling_price'] ?? 0.00),
                'stock_quantity' => $qty,
                'min_stock_level' => (int) ($validated['min_stock_level'] ?? 5),
                'location' => $validated['location'] ?? null,
                'is_active' => true,
            ]);

            if ($qty > 0) {
                SparePartStockMovement::create([
                    'tenant_id' => $tenant->id,
                    'spare_part_id' => $sparePart->id,
                    'user_id' => auth()->id(),
                    'repair_job_id' => null,
                    'type' => 'inward',
                    'quantity_change' => $qty,
                    'quantity_before' => 0,
                    'quantity_after' => $qty,
                    'unit_cost' => $purchasePrice,
                    'notes' => 'Initial stock inward',
                ]);
            }

            return $sparePart;
        });

        AuditLog::record('spare_part.created', "Added spare part '{$part->name}' (Initial Qty: {$part->stock_quantity})", $tenant->id);

        return redirect()->route('spare-parts.index')->with('success', "Spare part '{$part->name}' added to catalogue successfully.");
    }

    /**
     * Show detailed spare part info & stock movement history.
     */
    public function show(SparePart $sparePart): View
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $sparePart->tenant_id === $tenant->id, 403);

        $sparePart->load(['supplier', 'stockMovements.user', 'stockMovements.repairJob.customer']);

        return view('spare_parts.show', compact('tenant', 'sparePart'));
    }

    /**
     * Show form to edit spare part details.
     */
    public function edit(SparePart $sparePart): View
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $sparePart->tenant_id === $tenant->id, 403);

        $suppliers = Supplier::where('tenant_id', $tenant->id)->orderBy('name')->get();
        $categories = self::$categories;

        return view('spare_parts.edit', compact('tenant', 'sparePart', 'suppliers', 'categories'));
    }

    /**
     * Update spare part details.
     */
    public function update(Request $request, SparePart $sparePart): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $sparePart->tenant_id === $tenant->id, 403);

        $isPurchaseCostRequired = DynamicFormConfig::isFieldRequired($tenant, 'spare_part', 'purchase_price');
        $isSellingPriceRequired = DynamicFormConfig::isFieldRequired($tenant, 'spare_part', 'selling_price');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'compatible_models' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'specifications' => ['nullable', 'string'],
            'purchase_price' => [$isPurchaseCostRequired ? 'required' : 'nullable', 'numeric', 'min:0'],
            'selling_price' => [$isSellingPriceRequired ? 'required' : 'nullable', 'numeric', 'min:0'],
            'min_stock_level' => ['nullable', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:100'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        $sparePart->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'brand' => $validated['brand'] ?? null,
            'compatible_models' => $validated['compatible_models'] ?? null,
            'sku' => ! empty($validated['sku']) ? trim($validated['sku']) : null,
            'barcode' => ! empty($validated['barcode']) ? trim($validated['barcode']) : null,
            'specifications' => $validated['specifications'] ?? null,
            'purchase_price' => (float) ($validated['purchase_price'] ?? 0.00),
            'selling_price' => (float) ($validated['selling_price'] ?? 0.00),
            'min_stock_level' => (int) ($validated['min_stock_level'] ?? 5),
            'location' => $validated['location'] ?? null,
            'supplier_id' => $validated['supplier_id'] ?? null,
        ]);

        AuditLog::record('spare_part.updated', "Updated spare part details for '{$sparePart->name}'", $tenant->id);

        return redirect()->route('spare-parts.show', $sparePart)->with('success', "Spare part '{$sparePart->name}' updated successfully.");
    }

    /**
     * Delete a spare part.
     */
    public function destroy(SparePart $sparePart): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $sparePart->tenant_id === $tenant->id, 403);

        if ($sparePart->repairJobItems()->exists()) {
            return back()->with('error', "Cannot delete spare part '{$sparePart->name}' because it has been used in repair jobs. You may adjust its stock or mark it inactive instead.");
        }

        $partName = $sparePart->name;
        $sparePart->delete();

        AuditLog::record('spare_part.deleted', "Deleted spare part '{$partName}' from catalogue", $tenant->id);

        return redirect()->route('spare-parts.index')->with('success', "Spare part '{$partName}' removed from catalogue.");
    }

    /**
     * Adjust stock quantity for a spare part with complete movement audit.
     */
    public function adjustStock(Request $request, SparePart $sparePart): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $sparePart->tenant_id === $tenant->id, 403);

        $validated = $request->validate([
            'adjustment_type' => ['required', 'in:manual_add,manual_reduce,manual_set,damaged,returned'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($tenant, $sparePart, $validated) {
                $lockedPart = SparePart::where('tenant_id', $tenant->id)->lockForUpdate()->findOrFail($sparePart->id);
                $before = $lockedPart->stock_quantity;
                $inputQty = (int) $validated['quantity'];

                $after = match ($validated['adjustment_type']) {
                    'manual_add' => $before + $inputQty,
                    'manual_reduce', 'damaged' => $before - $inputQty,
                    'manual_set' => $inputQty,
                    'returned' => $before + $inputQty,
                };

                if ($after < 0) {
                    throw new DomainException("Cannot reduce stock below 0. Current available stock is {$before} units.");
                }

                $qtyChange = $after - $before;
                $lockedPart->stock_quantity = $after;
                $lockedPart->save();

                SparePartStockMovement::create([
                    'tenant_id' => $tenant->id,
                    'spare_part_id' => $lockedPart->id,
                    'user_id' => auth()->id(),
                    'repair_job_id' => null,
                    'type' => $validated['adjustment_type'],
                    'quantity_change' => $qtyChange,
                    'quantity_before' => $before,
                    'quantity_after' => $after,
                    'unit_cost' => $lockedPart->purchase_price,
                    'notes' => $validated['reason'] ?? ucfirst(str_replace('_', ' ', $validated['adjustment_type'])),
                ]);

                AuditLog::record('spare_part.stock_adjusted', "Adjusted stock for '{$lockedPart->name}': {$before} -> {$after} ({$validated['adjustment_type']})", $tenant->id);
            });

            return back()->with('success', "Stock quantity for '{$sparePart->name}' updated successfully.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('SparePart stock adjustment failed: '.$e->getMessage());

            return back()->with('error', 'Unable to adjust spare part stock. Please try again.');
        }
    }

    /**
     * Dedicated Spare Parts Stock Management Ledger view.
     */
    public function stockIndex(Request $request): View
    {
        $tenant = TenantContext::get();
        abort_unless($tenant, 403, 'Tenant context not found.');

        $query = SparePart::where('tenant_id', $tenant->id)->with('supplier');

        if ($filter = $request->get('filter')) {
            if ($filter === 'low_stock') {
                $query->lowStock();
            } elseif ($filter === 'out_of_stock') {
                $query->outOfStock();
            } elseif ($filter === 'in_stock') {
                $query->inStock();
            }
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('compatible_models', 'like', "%{$search}%");
            });
        }

        $parts = $query->orderBy('stock_quantity')->paginate(20)->withQueryString();

        $recentMovements = SparePartStockMovement::where('tenant_id', $tenant->id)
            ->with(['sparePart', 'user', 'repairJob'])
            ->latest()
            ->take(12)
            ->get();

        $lowStockCount = SparePart::where('tenant_id', $tenant->id)->lowStock()->count();
        $outOfStockCount = SparePart::where('tenant_id', $tenant->id)->outOfStock()->count();
        $totalUnits = (int) SparePart::where('tenant_id', $tenant->id)->sum('stock_quantity');

        return view('spare_parts.stock', compact('tenant', 'parts', 'recentMovements', 'lowStockCount', 'outOfStockCount', 'totalUnits'));
    }

    /**
     * Search API endpoint for barcode scanning and quick lookup.
     */
    public function searchApi(Request $request): JsonResponse
    {
        $tenant = TenantContext::get();
        if (! $tenant) {
            return response()->json(['success' => false, 'message' => 'No tenant context'], 403);
        }

        $q = trim((string) $request->get('q', ''));
        if (strlen($q) === 0) {
            return response()->json(['success' => true, 'parts' => []]);
        }

        $parts = SparePart::where('tenant_id', $tenant->id)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%")
                    ->orWhere('compatible_models', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%");
            })
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($part) {
                return [
                    'id' => $part->id,
                    'name' => $part->name,
                    'category' => $part->category,
                    'brand' => $part->brand,
                    'compatible_models' => $part->compatible_models,
                    'sku' => $part->sku,
                    'barcode' => $part->barcode,
                    'selling_price' => (float) $part->selling_price,
                    'purchase_price' => (float) $part->purchase_price,
                    'stock_quantity' => $part->stock_quantity,
                    'is_in_stock' => $part->stock_quantity > 0,
                    'is_low_stock' => $part->isLowStock(),
                ];
            });

        return response()->json([
            'success' => true,
            'parts' => $parts,
        ]);
    }
}
