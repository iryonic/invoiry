<?php

namespace App\Http\Controllers;

use App\Services\TenantContext;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the official Privacy Policy.
     */
    public function privacy(): View
    {
        $tenant = TenantContext::get();

        return view('pages.privacy', compact('tenant'));
    }

    /**
     * Display the official Terms and Conditions / Terms of Service.
     */
    public function terms(): View
    {
        $tenant = TenantContext::get();

        return view('pages.terms', compact('tenant'));
    }

    /**
     * Display comprehensive Help & Documentation.
     */
    public function help(): View
    {
        $tenant = TenantContext::get();

        return view('pages.help', compact('tenant'));
    }
}
