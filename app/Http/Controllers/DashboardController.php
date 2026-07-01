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
            'activities' => ActivityLog::with('user')->latest()->take(8)->get(),
        ]);
    }
}
