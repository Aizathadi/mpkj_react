<?php

namespace App\Http\Controllers;

use App\Models\AssetRegistration;
use Illuminate\Http\Request;

class AssetRegistrationController extends Controller
{
    //  Show all assets (grouped by site)
    public function index()
    {
        $assetsBySite = AssetRegistration::all()->groupBy('site_name');
        return view('assets.index', compact('assetsBySite'));
    }

    //  Show form to create a new asset
    public function create()
    {
        return view('assets.create');
    }

    //  Store new asset
    public function store(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'asset_no'  => 'required|string|max:255|unique:asset_registrations,asset_no',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        AssetRegistration::create($request->all());

        return redirect()->route('assets.index')->with('success', 'Asset registered successfully!');
    }

    //  Show form to edit existing asset
    public function edit($id)
    {
        $asset = AssetRegistration::findOrFail($id);
        return view('assets.edit', compact('asset'));
    }

    // Update asset
    public function update(Request $request, $id)
    {
        $asset = AssetRegistration::findOrFail($id);

        $request->validate([
            'site_name' => 'required|string|max:255',
            'asset_no'  => 'required|string|max:255|unique:asset_registrations,asset_no,' . $asset->id,
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $asset->update($request->all());

        return redirect()->route('assets.index')->with('success', 'Asset updated successfully!');
    }

    //  Delete asset
    public function destroy($id)
    {
        $asset = AssetRegistration::findOrFail($id);
        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully!');
    }

    //  API: Get unique site names (for dashboard dropdown)
    public function getSites()
    {
        $sites = AssetRegistration::select('site_name')->distinct()->get();
        return response()->json($sites);
    }

    //  API: Get all assets by site name (for map markers)
    public function getAssetsBySite($site)
    {
        $assets = AssetRegistration::where('site_name', $site)->get();
        return response()->json($assets);
    }
}
