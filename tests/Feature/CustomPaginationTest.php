<?php

namespace Tests\Feature;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class CustomPaginationTest extends TestCase
{
    /**
     * Test default view configuration in AppServiceProvider.
     */
    public function test_default_pagination_views_are_configured(): void
    {
        $items = collect(range(1, 40));
        $paginator = new LengthAwarePaginator(
            $items->forPage(1, 10),
            40,
            10,
            1,
            ['path' => '/items']
        );

        $html = $paginator->links()->toHtml();

        // Check for INVOIRY custom responsive pagination elements
        $this->assertStringContainsString('aria-label="Pagination Navigation"', $html);
        $this->assertStringContainsString('Showing', $html);
        $this->assertStringContainsString('of', $html);
        $this->assertStringContainsString('results', $html);
        $this->assertStringContainsString('mobile-page-select-page', $html);
        $this->assertStringContainsString('bg-[#111111]', $html);
        $this->assertStringContainsString('text-[#B7FF00]', $html);
    }

    /**
     * Test first page renders disabled previous and active next.
     */
    public function test_first_page_has_disabled_previous_and_active_next(): void
    {
        $items = collect(range(1, 50));
        $paginator = new LengthAwarePaginator(
            $items->forPage(1, 10),
            50,
            10,
            1,
            ['path' => '/items']
        );

        $html = $paginator->links()->toHtml();

        // First page: previous is disabled
        $this->assertStringContainsString('aria-disabled="true" aria-label="Previous page"', $html);
        // Next link is clickable
        $this->assertStringContainsString('rel="next" aria-label="Next page"', $html);
        $this->assertStringContainsString('href="/items?page=2"', $html);
    }

    /**
     * Test middle page renders active previous and active next.
     */
    public function test_middle_page_has_active_previous_and_next(): void
    {
        $items = collect(range(1, 50));
        $paginator = new LengthAwarePaginator(
            $items->forPage(2, 10),
            50,
            10,
            2,
            ['path' => '/items']
        );

        $html = $paginator->links()->toHtml();

        // Middle page: previous and next are both links
        $this->assertStringContainsString('rel="prev" aria-label="Previous page"', $html);
        $this->assertStringContainsString('href="/items?page=1"', $html);
        $this->assertStringContainsString('rel="next" aria-label="Next page"', $html);
        $this->assertStringContainsString('href="/items?page=3"', $html);
    }

    /**
     * Test last page renders active previous and disabled next.
     */
    public function test_last_page_has_active_previous_and_disabled_next(): void
    {
        $items = collect(range(1, 50));
        $paginator = new LengthAwarePaginator(
            $items->forPage(5, 10),
            50,
            10,
            5,
            ['path' => '/items']
        );

        $html = $paginator->links()->toHtml();

        $this->assertStringContainsString('rel="prev" aria-label="Previous page"', $html);
        $this->assertStringContainsString('aria-disabled="true" aria-label="Next page"', $html);
    }

    /**
     * Test query parameters are preserved in links and mobile dropdown.
     */
    public function test_query_parameters_are_preserved(): void
    {
        $items = collect(range(1, 30));
        $paginator = (new LengthAwarePaginator(
            $items->forPage(1, 10),
            30,
            10,
            1,
            ['path' => '/items']
        ))->appends(['search' => 'laptop', 'category' => 'hardware']);

        $html = $paginator->links()->toHtml();

        $this->assertStringContainsString('search=laptop', $html);
        $this->assertStringContainsString('category=hardware', $html);
        $this->assertStringContainsString('page=2', $html);
    }

    /**
     * Test Blade component <x-pagination> with multiple pages.
     */
    public function test_blade_component_renders_multi_page_paginator(): void
    {
        $items = collect(range(1, 40));
        $paginator = new LengthAwarePaginator(
            $items->forPage(2, 10),
            40,
            10,
            2,
            ['path' => '/invoices']
        );

        $view = Blade::render('<x-pagination :paginator="$paginator" class="custom-test-class" />', [
            'paginator' => $paginator,
        ]);

        $this->assertStringContainsString('custom-test-class', $view);
        $this->assertStringContainsString('Showing', $view);
        $this->assertStringContainsString('11', $view);
        $this->assertStringContainsString('20', $view);
        $this->assertStringContainsString('40', $view);
        $this->assertStringContainsString('aria-current="page"', $view);
    }

    /**
     * Test Blade component <x-pagination> with single page results.
     */
    public function test_blade_component_renders_single_page_summary(): void
    {
        $items = collect(range(1, 5));
        $paginator = new LengthAwarePaginator(
            $items->forPage(1, 10),
            5,
            10,
            1,
            ['path' => '/customers']
        );

        $view = Blade::render('<x-pagination :paginator="$paginator" />', [
            'paginator' => $paginator,
        ]);

        $this->assertStringContainsString('Showing all', $view);
        $this->assertStringContainsString('5', $view);
        $this->assertStringContainsString('results', $view);
    }

    /**
     * Test simple paginator view renders correctly.
     */
    public function test_simple_paginator_renders_correctly(): void
    {
        // Paginator determines hasMore by receiving perPage + 1 items
        $items = collect(range(1, 11));
        $paginator = new Paginator(
            $items,
            10,
            1,
            ['path' => '/logs']
        );

        $html = $paginator->links('vendor.pagination.simple-tailwind')->toHtml();

        $this->assertStringContainsString('aria-label="Pagination Navigation"', $html);
        $this->assertStringContainsString('Page 1', $html);
        $this->assertStringContainsString('Next', $html);
    }
}
