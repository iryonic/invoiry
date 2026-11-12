<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Item;
use App\Services\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the tenant dashboard.
     */
    public function index(): View|RedirectResponse
    {
        $tenant = TenantContext::get();

        if (! $tenant) {
            if (auth()->user()?->isSuperAdmin()) {
                return redirect()->route('superadmin.index');
            }
            abort(403, 'No tenant context found for this user.');
        }

        $tenantId = $tenant->id;

        // Key Metrics
        $todaySales = (float) Invoice::where('tenant_id', $tenantId)
            ->whereDate('invoice_date', today())
            ->sum('grand_total');

        $todayInvoicesCount = Invoice::where('tenant_id', $tenantId)
            ->whereDate('invoice_date', today())
            ->count();

        $monthSales = (float) Invoice::where('tenant_id', $tenantId)
            ->whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->sum('grand_total');

        $inStockCount = Item::where('tenant_id', $tenantId)
            ->inStock()
            ->count();

        $inStockValue = (float) Item::where('tenant_id', $tenantId)
            ->inStock()
            ->sum('purchase_price');

        $totalDue = (float) Invoice::where('tenant_id', $tenantId)
            ->whereIn('payment_status', ['partial', 'unpaid'])
            ->get()
            ->sum(fn ($inv) => $inv->amount_due);

        // Recent Invoices (last 10)
        $recentInvoices = Invoice::where('tenant_id', $tenantId)
            ->with(['customer', 'items'])
            ->latest()
            ->take(10)
            ->get();

        // Recently Inward Stock (last 6)
        $recentStock = Item::where('tenant_id', $tenantId)
            ->with('supplier')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'tenant',
            'todaySales',
            'todayInvoicesCount',
            'monthSales',
            'inStockCount',
            'inStockValue',
            'totalDue',
            'recentInvoices',
            'recentStock'
        ));
    }
}
