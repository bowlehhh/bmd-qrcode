<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;

class ExportHistoryController extends Controller
{
    public function __invoke(): View
    {
        $query = ActivityLog::with('user')
            ->whereIn('action', ['export_word_asset', 'export_word_assets_bulk'])
            ->latest();

        $logs = (clone $query)->paginate(12);
        $allLogs = (clone $query)->get();

        return view('exports.history', [
            'logs' => $logs,
            'summary' => [
                'total_exports' => $allLogs->count(),
                'single_exports' => $allLogs->where('action', 'export_word_asset')->count(),
                'bulk_exports' => $allLogs->where('action', 'export_word_assets_bulk')->count(),
                'assets_exported' => $allLogs->sum(fn (ActivityLog $log) => (int) ($log->properties['total_assets'] ?? 1)),
            ],
        ]);
    }
}
