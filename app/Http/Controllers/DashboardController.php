<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Asset;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'summary' => [
                'total' => Asset::count(),
                'baik' => Asset::where('condition', 'baik')->count(),
                'rusak' => Asset::where('condition', 'rusak')->count(),
                'lokasi' => Asset::distinct('location')->count('location'),
            ],
            'latestAssets' => Asset::latest()->take(5)->get(),
            'activities' => ActivityLog::with('user')
                ->whereNotIn('action', ['export_word_asset', 'export_word_assets_bulk'])
                ->latest()
                ->take(8)
                ->get(),
            'exportCount' => ActivityLog::whereIn('action', ['export_word_asset', 'export_word_assets_bulk'])->count(),
        ]);
    }
}
