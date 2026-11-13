<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BrochureGeneration;
use App\Models\BrochureModule;
use App\Models\BrochurePlan;
use App\Models\BrochureScreenshot;
use App\Models\BrochureSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BrochureController extends Controller
{
    /**
     * Display the Brochure & Pricing PDF Management interface.
     */
    public function index(): View
    {
        $settings = BrochureSetting::all()->pluck('value', 'key')->toArray();
        $modules = BrochureModule::with('screenshots')->orderBy('sort_order')->get();
        $screenshots = BrochureScreenshot::with('module')->orderBy('sort_order')->get();
        $plans = BrochurePlan::orderBy('sort_order')->get();
        $latestGeneration = BrochureGeneration::latest('id')->first();
        $generationHistory = BrochureGeneration::with('generator')->latest('id')->take(10)->get();

        return view('superadmin.brochure.index', compact(
            'settings',
            'modules',
            'screenshots',
            'plans',
            'latestGeneration',
            'generationHistory'
        ));
    }

    /**
     * Update general settings, typography, branding, and section visibility.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:100'],
            'product_tagline' => ['nullable', 'string', 'max:255'],
            'document_title' => ['required', 'string', 'max:255'],
            'document_subtitle' => ['nullable', 'string', 'max:255'],
            'version' => ['required', 'string', 'max:50'],
            'hero_headline' => ['required', 'string', 'max:255'],
            'hero_subheadline' => ['nullable', 'string'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'contact_phone_1' => ['required', 'string', 'max:50'],
            'contact_phone_2' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['required', 'email', 'max:100'],
            'contact_website' => ['nullable', 'string', 'max:255'],
            'contact_address' => ['nullable', 'string'],
            'overview_what_it_does' => ['nullable', 'string'],
            'overview_problems_solved' => ['nullable', 'string'],
            'overview_target_audience' => ['nullable', 'string'],
            'overview_key_benefits' => ['nullable', 'string'],
            'terms_notes' => ['nullable', 'string'],
            'show_cover' => ['nullable', 'boolean'],
            'show_overview' => ['nullable', 'boolean'],
            'show_modules' => ['nullable', 'boolean'],
            'show_screenshots' => ['nullable', 'boolean'],
            'show_pricing' => ['nullable', 'boolean'],
            'show_specifications' => ['nullable', 'boolean'],
            'show_contact' => ['nullable', 'boolean'],
        ]);

        $booleanKeys = [
            'show_cover',
            'show_overview',
            'show_modules',
            'show_screenshots',
            'show_pricing',
            'show_specifications',
            'show_contact',
        ];

        foreach ($booleanKeys as $bKey) {
            $validated[$bKey] = $request->has($bKey) ? '1' : '0';
        }

        foreach ($validated as $key => $val) {
            BrochureSetting::set($key, (string) $val);
        }

        return back()->with('success', 'Brochure settings and branding configuration updated successfully.');
    }

    /**
     * Store a new functional software module.
     */
    public function storeModule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'badge' => ['nullable', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string'],
            'features_text' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        $features = [];
        if (! empty($validated['features_text'])) {
            $features = array_values(array_filter(array_map('trim', explode("\n", $validated['features_text']))));
        }

        BrochureModule::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'icon' => $validated['icon'] ?? 'check-circle',
            'description' => $validated['description'],
            'features' => $features,
            'sort_order' => $validated['sort_order'] ?? (BrochureModule::max('sort_order') + 1),
            'is_enabled' => $request->has('is_enabled'),
        ]);

        return back()->with('success', 'Software module created successfully.');
    }

    /**
     * Update an existing module.
     */
    public function updateModule(Request $request, BrochureModule $module): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'badge' => ['nullable', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string'],
            'features_text' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        $features = [];
        if (! empty($validated['features_text'])) {
            $features = array_values(array_filter(array_map('trim', explode("\n", $validated['features_text']))));
        }

        $module->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'icon' => $validated['icon'] ?? $module->icon,
            'description' => $validated['description'],
            'features' => $features,
            'sort_order' => $validated['sort_order'] ?? $module->sort_order,
            'is_enabled' => $request->has('is_enabled'),
        ]);

        return back()->with('success', "Module \"{$module->title}\" updated successfully.");
    }

    /**
     * Delete a module.
     */
    public function destroyModule(BrochureModule $module): RedirectResponse
    {
        $title = $module->title;
        $module->delete();

        return back()->with('success', "Module \"{$title}\" removed.");
    }

    /**
     * Reorder modules.
     */
    public function reorderModules(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:brochure_modules,id'],
        ]);

        foreach ($validated['order'] as $index => $id) {
            BrochureModule::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['status' => 'success', 'message' => 'Modules reordered.']);
    }

    /**
     * Store and upload a new application screenshot.
     */
    public function storeScreenshot(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'caption' => ['nullable', 'string'],
            'module_id' => ['nullable', 'exists:brochure_modules,id'],
            'image' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('image')->store('brochure/screenshots', 'public');

        BrochureScreenshot::create([
            'module_id' => $validated['module_id'] ?? null,
            'title' => $validated['title'],
            'caption' => $validated['caption'] ?? null,
            'image_path' => 'storage/'.$path,
            'sort_order' => $validated['sort_order'] ?? (BrochureScreenshot::max('sort_order') + 1),
            'is_enabled' => $request->has('is_enabled'),
        ]);

        return back()->with('success', 'Screenshot uploaded and registered successfully.');
    }

    /**
     * Update an existing screenshot and metadata.
     */
    public function updateScreenshot(Request $request, BrochureScreenshot $screenshot): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'caption' => ['nullable', 'string'],
            'module_id' => ['nullable', 'exists:brochure_modules,id'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        $imagePath = $screenshot->image_path;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('brochure/screenshots', 'public');
            $imagePath = 'storage/'.$path;
        }

        $screenshot->update([
            'module_id' => $validated['module_id'] ?? null,
            'title' => $validated['title'],
            'caption' => $validated['caption'] ?? null,
            'image_path' => $imagePath,
            'sort_order' => $validated['sort_order'] ?? $screenshot->sort_order,
            'is_enabled' => $request->has('is_enabled'),
        ]);

        return back()->with('success', "Screenshot \"{$screenshot->title}\" updated successfully.");
    }

    /**
     * Remove a screenshot.
     */
    public function destroyScreenshot(BrochureScreenshot $screenshot): RedirectResponse
    {
        $title = $screenshot->title;
        $screenshot->delete();

        return back()->with('success', "Screenshot \"{$title}\" removed.");
    }

    /**
     * Store a new pricing tier.
     */
    public function storePlan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'string', 'max:50'],
            'period' => ['required', 'string', 'max:100'],
            'billing_note' => ['nullable', 'string', 'max:255'],
            'badge' => ['nullable', 'string', 'max:50'],
            'is_popular' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'features_text' => ['nullable', 'string'],
            'limitations' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        $features = [];
        if (! empty($validated['features_text'])) {
            $features = array_values(array_filter(array_map('trim', explode("\n", $validated['features_text']))));
        }

        BrochurePlan::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'period' => $validated['period'],
            'billing_note' => $validated['billing_note'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'is_popular' => $request->has('is_popular'),
            'description' => $validated['description'] ?? null,
            'features' => $features,
            'limitations' => $validated['limitations'] ?? null,
            'sort_order' => $validated['sort_order'] ?? (BrochurePlan::max('sort_order') + 1),
            'is_enabled' => $request->has('is_enabled'),
        ]);

        return back()->with('success', 'Commercial pricing plan created successfully.');
    }

    /**
     * Update an existing pricing tier.
     */
    public function updatePlan(Request $request, BrochurePlan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'string', 'max:50'],
            'period' => ['required', 'string', 'max:100'],
            'billing_note' => ['nullable', 'string', 'max:255'],
            'badge' => ['nullable', 'string', 'max:50'],
            'is_popular' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'features_text' => ['nullable', 'string'],
            'limitations' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        $features = [];
        if (! empty($validated['features_text'])) {
            $features = array_values(array_filter(array_map('trim', explode("\n", $validated['features_text']))));
        }

        $plan->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'period' => $validated['period'],
            'billing_note' => $validated['billing_note'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'is_popular' => $request->has('is_popular'),
            'description' => $validated['description'] ?? null,
            'features' => $features,
            'limitations' => $validated['limitations'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $plan->sort_order,
            'is_enabled' => $request->has('is_enabled'),
        ]);

        return back()->with('success', "Plan \"{$plan->name}\" updated successfully.");
    }

    /**
     * Remove a pricing plan.
     */
    public function destroyPlan(BrochurePlan $plan): RedirectResponse
    {
        $name = $plan->name;
        $plan->delete();

        return back()->with('success', "Plan \"{$name}\" removed.");
    }

    /**
     * Render the live preview of the A4 PDF directly in the browser.
     */
    public function preview(): View
    {
        $data = $this->collectBrochureData();

        return view('superadmin.brochure.pdf', $data);
    }

    /**
     * Compile and generate the latest downloadable PDF document.
     */
    public function generate(Request $request): RedirectResponse
    {
        $data = $this->collectBrochureData();
        $version = $data['settings']['version'] ?? '1.0.0';

        // Increment patch version if requested
        if ($request->input('bump_version')) {
            $parts = explode('.', $version);
            if (count($parts) === 3) {
                $parts[2] = (int) $parts[2] + 1;
                $version = implode('.', $parts);
                BrochureSetting::set('version', $version);
                $data['settings']['version'] = $version;
            }
        }

        $pdf = Pdf::loadView('superadmin.brochure.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);

        $filename = "invoiry-features-and-pricing-v{$version}.pdf";
        $latestFilename = 'invoiry-features-and-pricing-latest.pdf';

        $generatedDir = storage_path('app/public/brochure/generated');
        if (! File::isDirectory($generatedDir)) {
            File::makeDirectory($generatedDir, 0755, true);
        }

        $filePath = "{$generatedDir}/{$filename}";
        $latestPath = "{$generatedDir}/{$latestFilename}";

        $pdf->save($filePath);
        File::copy($filePath, $latestPath);

        $fileSize = File::size($filePath);

        BrochureGeneration::create([
            'version' => $version,
            'file_path' => "storage/brochure/generated/{$filename}",
            'file_size' => $fileSize,
            'generated_by' => auth()->id(),
            'notes' => $request->input('notes', 'Generated via Super Admin Console'),
        ]);

        return back()->with('success', "PDF Brochure v{$version} generated successfully (".round($fileSize / 1024, 1).' KB).');
    }

    /**
     * Download the latest generated PDF brochure.
     */
    public function download(): BinaryFileResponse|Response
    {
        $latestPath = storage_path('app/public/brochure/generated/invoiry-features-and-pricing-latest.pdf');

        if (File::exists($latestPath)) {
            return response()->download($latestPath, 'invoiry-features-and-pricing.pdf', [
                'Content-Type' => 'application/pdf',
            ]);
        }

        // Generate on the fly if not yet compiled
        $data = $this->collectBrochureData();
        $pdf = Pdf::loadView('superadmin.brochure.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);

        return $pdf->download('invoiry-features-and-pricing.pdf');
    }

    /**
     * Public direct download endpoint for prospects and customers.
     */
    public function publicDownload(): BinaryFileResponse|Response
    {
        return $this->download();
    }

    /**
     * Collect and prepare all data needed to render the brochure.
     *
     * @return array<string, mixed>
     */
    protected function collectBrochureData(): array
    {
        $settings = BrochureSetting::all()->pluck('value', 'key')->toArray();
        $modules = BrochureModule::where('is_enabled', true)->orderBy('sort_order')->get();
        $screenshots = BrochureScreenshot::where('is_enabled', true)->orderBy('sort_order')->get();
        $plans = BrochurePlan::where('is_enabled', true)->orderBy('sort_order')->get();

        return compact('settings', 'modules', 'screenshots', 'plans');
    }
}
