<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\RepairJob;
use App\Models\SparePart;
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
        $supportsSelling = $tenant->supportsSelling();
        $supportsRepair = $tenant->supportsRepair();

        // Key Metrics - Sales
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

        $inStockCount = $supportsSelling ? Item::where('tenant_id', $tenantId)->inStock()->count() : 0;
        $inStockValue = $supportsSelling ? (float) Item::where('tenant_id', $tenantId)->inStock()->sum('purchase_price') : 0.0;

        $totalDue = (float) (Invoice::where('tenant_id', $tenantId)
            ->whereIn('payment_status', ['partial', 'unpaid'])
            ->selectRaw('SUM(grand_total - amount_paid) as total_due')
            ->value('total_due') ?? 0.0);

        // Recent Invoices (last 10)
        $recentInvoices = Invoice::where('tenant_id', $tenantId)
            ->with(['customer', 'items'])
            ->latest()
            ->take(10)
            ->get();

        // Recently Inward Stock (last 6)
        $recentStock = $supportsSelling ? Item::where('tenant_id', $tenantId)
            ->with('supplier')
            ->latest()
            ->take(6)
            ->get() : collect();

        // Repair Metrics (if repair supported)
        $activeRepairsCount = 0;
        $completedRepairsCount = 0;
        $recentRepairs = collect();
        $sparePartsLowStockCount = 0;
        $sparePartsCount = 0;
        $sparePartsValue = 0.0;

        if ($supportsRepair) {
            $activeRepairsCount = RepairJob::where('tenant_id', $tenantId)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->count();

            $completedRepairsCount = RepairJob::where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->count();

            $recentRepairs = RepairJob::where('tenant_id', $tenantId)
                ->with(['customer', 'technician'])
                ->latest()
                ->take(8)
                ->get();

            $sparePartsLowStockCount = SparePart::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->lowStock()
                ->count();

            $sparePartsCount = SparePart::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->count();

            $sparePartsValue = (float) SparePart::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->selectRaw('SUM(stock_quantity * purchase_price) as val')
                ->value('val');
        }

        return view('dashboard', compact(
            'tenant',
            'supportsSelling',
            'supportsRepair',
            'todaySales',
            'todayInvoicesCount',
            'monthSales',
            'inStockCount',
            'inStockValue',
            'totalDue',
            'recentInvoices',
            'recentStock',
            'activeRepairsCount',
            'completedRepairsCount',
            'recentRepairs',
            'sparePartsLowStockCount',
            'sparePartsCount',
            'sparePartsValue'
        ));
    }
}
