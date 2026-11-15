<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Item;
use App\Models\StockAdjustment;
use App\Services\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Display the dedicated device stock inventory & management ledger.
     */
    public function index(Request $request): View
    {
        $tenant = TenantContext::get();
        $query = Item::where('tenant_id', $tenant->id)->with('supplier');

        // Status / Availability filter
        $status = $request->get('status', 'all');
        if ($status === 'in_stock') {
            $query->where('status', 'in_stock')->where('quantity', '>', 0);
        } elseif ($status === 'low_stock') {
            $query->where('status', 'in_stock')->where('quantity', '>', 0)->where('quantity', '<=', 2);
        } elseif ($status === 'out_of_stock' || $status === 'sold') {
            $query->where(function ($q) {
                $q->where('status', 'sold')->orWhere('quantity', '<=', 0);
            });
        }

        // Search by IMEI 1, IMEI 2, Serial, Brand, Model
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('imei_1', 'like', "%{$search}%")
                    ->orWhere('imei_2', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('storage', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_items' => Item::where('tenant_id', $tenant->id)->count(),
            'total_units_in_stock' => (int) Item::where('tenant_id', $tenant->id)->where('status', 'in_stock')->sum('quantity'),
            'low_stock_count' => Item::where('tenant_id', $tenant->id)->where('status', 'in_stock')->where('quantity', '>', 0)->where('quantity', '<=', 2)->count(),
            'out_of_stock_count' => Item::where('tenant_id', $tenant->id)->where(function ($q) {
                $q->where('status', 'sold')->orWhere('quantity', '<=', 0);
            })->count(),
            'total_valuation' => (float) Item::where('tenant_id', $tenant->id)->where('status', 'in_stock')->get()->sum(function ($it) {
                return (float) $it->purchase_price * max(1, (int) $it->quantity);
            }),
        ];

        // Recent stock movements for quick sidebar/footer visibility
        $recentMovements = StockAdjustment::where('tenant_id', $tenant->id)
            ->with(['item', 'user'])
            ->latest()
            ->take(8)
            ->get();

        return view('stock.index', compact('items', 'stats', 'status', 'recentMovements'));
    }

    /**
     * Record a manual stock adjustment with reasoning & audit tracking.
     */
    public function adjust(Request $request, Item $item): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_if($item->tenant_id !== $tenant->id, 403);

        $type = match ($request->input('adjustment_type') ?? $request->input('action')) {
            'add', 'increase' => 'add',
            'reduce', 'decrease' => 'reduce',
            'set' => 'set',
            default => null,
        };

        $request->merge([
            'adjustment_type' => $type,
            'quantity' => $request->input('quantity') ?? $request->input('amount'),
        ]);

        $validated = $request->validate([
            'adjustment_type' => ['required', 'in:add,reduce,set'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($item, $validated, $tenant) {
            $lockedItem = Item::where('id', $item->id)->lockForUpdate()->first();
            $currentQty = (int) $lockedItem->quantity;
            $qtyInput = (int) $validated['quantity'];

            $newQty = match ($validated['adjustment_type']) {
                'add' => $currentQty + $qtyInput,
                'reduce' => max(0, $currentQty - $qtyInput),
                'set' => max(0, $qtyInput),
            };

            $adjustment = $lockedItem->adjustStock(
                $newQty,
                'manual_'.$validated['adjustment_type'],
                $validated['reason'],
                auth()->id()
            );

            AuditLog::record(
                'stock.adjusted',
                "Adjusted stock for '{$lockedItem->item_name}' (IMEI: {$lockedItem->imei_1}): {$currentQty} -> {$newQty} ({$validated['reason']})",
                $tenant->id,
                auth()->id()
            );
        });

        return back()->with('success', "Stock updated for '{$item->item_name}'. New quantity: {$item->fresh()->quantity}.");
    }

    /**
     * Return JSON stock adjustment history for modal viewing.
     */
    public function history(Item $item): JsonResponse
    {
        $tenant = TenantContext::get();
        abort_if($item->tenant_id !== $tenant->id, 403);

        $history = $item->stockAdjustments()
            ->with('user')
            ->latest()
            ->take(30)
            ->get()
            ->map(function ($adj) {
                return [
                    'id' => $adj->id,
                    'type' => $adj->adjustment_type,
                    'change' => ($adj->quantity_change > 0 ? '+'.$adj->quantity_change : (string) $adj->quantity_change),
                    'before' => $adj->quantity_before,
                    'after' => $adj->quantity_after,
                    'reason' => $adj->reason ?: 'No reason provided',
                    'user' => $adj->user?->name ?: 'System',
                    'date' => $adj->created_at->format('d M Y, h:i A'),
                ];
            });

        return response()->json([
            'item_name' => $item->item_name,
            'imei_1' => $item->imei_1,
            'current_quantity' => $item->quantity,
            'history' => $history,
        ]);
    }
}
