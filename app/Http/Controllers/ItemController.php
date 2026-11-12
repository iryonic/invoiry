<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Item;
use App\Models\Supplier;
use App\Services\DynamicFormConfig;
use App\Services\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * Display a listing of items (inventory / brought items).
     */
    public function index(Request $request): View
    {
        $tenant = TenantContext::get();
        $query = Item::where('tenant_id', $tenant->id)->with('supplier');

        // Status filter (default: in_stock)
        $status = $request->get('status', 'in_stock');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Condition filter
        if ($request->filled('condition') && $request->condition !== 'all') {
            $query->where('condition', $request->condition);
        }

        // Search query (Item name, IMEI 1/2, Serial, Brand)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('imei_1', 'like', "%{$search}%")
                    ->orWhere('imei_2', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'in_stock' => Item::where('tenant_id', $tenant->id)->inStock()->count(),
            'sold' => Item::where('tenant_id', $tenant->id)->sold()->count(),
            'total_value' => Item::where('tenant_id', $tenant->id)->inStock()->sum('purchase_price'),
        ];

        return view('items.index', compact('items', 'status', 'stats'));
    }

    /**
     * Show the form for creating a new item (inward / brought item).
     */
    public function create(): View
    {
        $tenant = TenantContext::get();
        $suppliers = Supplier::where('tenant_id', $tenant->id)->orderBy('name')->get();

        return view('items.create', compact('suppliers'));
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = TenantContext::get();

        $isPurchasePriceRequired = DynamicFormConfig::isFieldRequired($tenant, 'item', 'purchase_price');

        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'storage' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'specs' => ['nullable', 'string'],
            'imei_1' => ['nullable', 'string', 'max:50'],
            'imei_2' => ['nullable', 'string', 'max:50'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'condition' => ['nullable', 'in:new,used,refurbished'],
            'purchase_price' => [$isPurchasePriceRequired ? 'required' : 'nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'warranty_status' => ['nullable', 'in:no_warranty,store_warranty,company_warranty'],
            'warranty_expiry_date' => ['nullable', 'date'],
            'purchase_date' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string'],
            'remark' => ['nullable', 'string'],

            // Optional Inline Supplier fields
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'new_supplier_name' => ['nullable', 'string', 'max:255'],
            'new_supplier_phone' => ['nullable', 'string', 'max:20'],
            'new_supplier_adhar' => ['nullable', 'string', 'max:30'],
            'new_supplier_address' => ['nullable', 'string'],
        ]);

        $validated['condition'] = $validated['condition'] ?? 'new';
        $validated['purchase_price'] = $validated['purchase_price'] ?? 0;
        $validated['purchase_date'] = $validated['purchase_date'] ?? now()->toDateString();
        $validated['payment_method'] = $validated['payment_method'] ?? 'cash';
        $validated['warranty_status'] = $validated['warranty_status'] ?? 'no_warranty';

        $supplierId = $validated['supplier_id'] ?? null;

        // Check for duplicate in-stock IMEI
        if (! empty($validated['imei_1'])) {
            $existing = Item::where('tenant_id', $tenant->id)
                ->where('status', 'in_stock')
                ->where('imei_1', $validated['imei_1'])
                ->exists();
            if ($existing) {
                return back()->withInput()->with('error', "Device with IMEI '{$validated['imei_1']}' is already present in active store inventory.");
            }
        }

        try {
            // If new supplier details provided, create supplier on the fly
            if (! $supplierId && ! empty($validated['new_supplier_name'])) {
                $supplier = Supplier::create([
                    'tenant_id' => $tenant->id,
                    'name' => $validated['new_supplier_name'],
                    'phone' => $validated['new_supplier_phone'] ?? null,
                    'id_proof_number' => $validated['new_supplier_adhar'] ?? null,
                    'address' => $validated['new_supplier_address'] ?? null,
                ]);
                $supplierId = $supplier->id;
            }

            Item::create([
                'tenant_id' => $tenant->id,
                'supplier_id' => $supplierId,
                'item_name' => $validated['item_name'],
                'brand' => $validated['brand'] ?? null,
                'category' => $validated['category'] ?? 'Mobile',
                'storage' => $validated['storage'] ?? null,
                'color' => $validated['color'] ?? null,
                'specs' => $validated['specs'] ?? null,
                'imei_1' => $validated['imei_1'] ?? null,
                'imei_2' => $validated['imei_2'] ?? null,
                'serial_number' => $validated['serial_number'] ?? null,
                'condition' => $validated['condition'],
                'status' => 'in_stock',
                'purchase_price' => $validated['purchase_price'],
                'selling_price' => $validated['selling_price'] ?? null,
                'warranty_status' => $validated['warranty_status'],
                'warranty_expiry_date' => $validated['warranty_expiry_date'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'payment_method' => $validated['payment_method'],
                'remark' => $validated['remark'] ?? null,
            ]);

            return redirect()->route('items.index')->with('success', 'Device successfully added to inventory!');
        } catch (\Throwable $e) {
            Log::error('Item inward failed: '.$e->getMessage());

            return back()->withInput()->with('error', 'Unable to record device inward. Please verify details and try again.');
        }
    }

    /**
     * Show the form for editing an item.
     */
    public function edit(Item $item): View
    {
        $tenant = TenantContext::get();
        abort_if($item->tenant_id !== $tenant->id, 403);

        $suppliers = Supplier::where('tenant_id', $tenant->id)->orderBy('name')->get();

        return view('items.edit', compact('item', 'suppliers'));
    }

    /**
     * Update the specified item.
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_if($item->tenant_id !== $tenant->id, 403);

        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'storage' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'specs' => ['nullable', 'string'],
            'imei_1' => ['nullable', 'string', 'max:50'],
            'imei_2' => ['nullable', 'string', 'max:50'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'condition' => ['required', 'in:new,used,refurbished'],
            'status' => ['required', 'in:in_stock,sold,returned'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'warranty_status' => ['required', 'in:no_warranty,store_warranty,company_warranty'],
            'warranty_expiry_date' => ['nullable', 'date'],
            'purchase_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,upi,bank'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'remark' => ['nullable', 'string'],
        ]);

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Item details updated successfully.');
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_if($item->tenant_id !== $tenant->id, 403);

        if ($item->status === 'sold') {
            return back()->with('error', 'Cannot delete an item that has already been sold. Delete the corresponding invoice first.');
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item removed from inventory.');
    }

    /**
     * API for live search and barcode scan lookup in billing form.
     */
    public function searchApi(Request $request): JsonResponse
    {
        $tenant = TenantContext::get();
        $q = trim((string) $request->get('q', ''));

        if (empty($q)) {
            return response()->json([]);
        }

        $items = Item::where('tenant_id', $tenant->id)
            ->inStock()
            ->where(function ($query) use ($q) {
                $query->where('imei_1', 'like', "%{$q}%")
                    ->orWhere('imei_2', 'like', "%{$q}%")
                    ->orWhere('serial_number', 'like', "%{$q}%")
                    ->orWhere('item_name', 'like', "%{$q}%")
                    ->orWhere('brand', 'like', "%{$q}%");
            })
            ->take(10)
            ->get();

        return response()->json($items);
    }

    /**
     * Reusable Bulk Action on Inventory Items.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $tenant = TenantContext::get() ?? auth()->user()->tenant;

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:items,id'],
            'action' => ['required', 'string', 'in:delete,mark_sold,mark_in_stock'],
        ]);

        if ($validated['action'] === 'delete' && ! auth()->user()->hasPermission('items.delete')) {
            abort(403, 'You do not have permission to delete stock items.');
        }

        if (in_array($validated['action'], ['mark_sold', 'mark_in_stock'], true) && ! auth()->user()->hasPermission('items.edit')) {
            abort(403, 'You do not have permission to edit stock items.');
        }

        $itemsQuery = Item::where('tenant_id', $tenant->id)->whereIn('id', $validated['ids']);
        $count = $itemsQuery->count();

        if ($count === 0) {
            return back()->with('warning', 'No valid store items selected.');
        }

        if ($validated['action'] === 'delete') {
            $itemsQuery->delete();
            AuditLog::record('item.bulk_deleted', "Bulk deleted {$count} stock items", $tenant->id, auth()->id());
            $msg = "{$count} devices removed from inventory.";
        } elseif ($validated['action'] === 'mark_sold') {
            $itemsQuery->update(['status' => 'sold']);
            AuditLog::record('item.bulk_marked_sold', "Bulk marked {$count} items as sold", $tenant->id, auth()->id());
            $msg = "{$count} devices updated to Sold status.";
        } else {
            $itemsQuery->update(['status' => 'in_stock']);
            AuditLog::record('item.bulk_marked_instock', "Bulk marked {$count} items in stock", $tenant->id, auth()->id());
            $msg = "{$count} devices updated to In Stock status.";
        }

        return back()->with('success', $msg);
    }
}
