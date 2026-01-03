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
        $request->validate([
            'selector_value' => 'required|string',
            'description'    => 'nullable|string|max:255',
            'is_active'      => 'required|boolean',
            'version'        => 'required|string|max:20',
        ]);

        $siteSelector->update($request->only([
            'selector_value',
            'description',
            'is_active',
            'version'
        ]));

        return redirect()->back()
            ->with('success', "Đã lưu thay đổi cho {$siteSelector->site} - {$siteSelector->element_key}");
    }
}
