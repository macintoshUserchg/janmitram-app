<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\Http\Request;

/**
 * Admin controller for legal pages (privacy policy, terms, refund policy).
 *
 * RECONSTRUCTION NOTE: The original Admin LegalPageController was not present
 * in the shipped source (server-withheld). Defined here because the Blade
 * views at resources/views/admin/legalPage/ exist and reference
 * admin.legalPage.edit and admin.legalPage.update routes.
 */
class LegalPageController extends Controller
{
    /**
     * Show the list of legal pages.
     */
    public function index()
    {
        $page = LegalPage::first();

        return view('admin.legalPage.index', compact('page'));
    }

    /**
     * Show the edit form for a legal page.
     */
    public function edit($slug)
    {
        $page = LegalPage::where('slug', $slug)->firstOrFail();

        return view('admin.legalPage.edit', compact('page'));
    }

    /**
     * Update a legal page.
     */
    public function update(Request $request, $slug)
    {
        $page = LegalPage::where('slug', $slug)->firstOrFail();

        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $page->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return back()->withSuccess(__('Legal page updated successfully'));
    }
}
