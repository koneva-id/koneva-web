<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        $logs = AuditLog::query()
            ->with('actor:id,name,email')
            ->latest()
            ->paginate(30);

        return view('superadmin.audit-logs', [
            'logs' => $logs,
        ]);
    }
}
