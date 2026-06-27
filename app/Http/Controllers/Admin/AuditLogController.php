<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        return view('admin.audit.index', [
            'records' => AuditLog::query()->latest('created_at')->paginate(10),
        ]);
    }
}
