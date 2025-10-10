<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetRegistration;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch all assets to display site & asset list
        $assets = AssetRegistration::all();

        return view('dashboard', compact('assets'));
    }
}
