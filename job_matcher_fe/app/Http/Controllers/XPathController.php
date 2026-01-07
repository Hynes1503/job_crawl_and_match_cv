<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSelector;

class XPathController extends Controller
{
    public function index()
    {
        $selectors = SiteSelector::orderBy('site')
            ->orderBy('page_type')
            ->orderBy('element_key')
            ->paginate(30);

        return view('admin.site-selectors.index', compact('selectors'));
    }

    public function update(Request $request, SiteSelector $siteSelector)
    {
        $validated = $request->validate([
            'selector_value' => 'required|string',
            'description'    => 'nullable|string|max:255',
            'version'        => 'required|string|max:20',
        ]);

        // Thêm is_active vào validated data
        $validated['is_active'] = $request->has('is_active');

        $siteSelector->update($validated);

        return redirect()->back()
            ->with('success', "Đã lưu thay đổi cho {$siteSelector->site} - {$siteSelector->element_key}");
    }
}
