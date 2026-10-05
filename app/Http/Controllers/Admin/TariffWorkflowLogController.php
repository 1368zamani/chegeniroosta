<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TariffSet;
use Illuminate\Support\Facades\Gate;

class TariffWorkflowLogController extends Controller
{
    public function index(TariffSet $tariffSet)
    {
        Gate::authorize('view', $tariffSet);

        $logs = $tariffSet->workflowLogs()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.tariffs.logs.index', compact('tariffSet', 'logs'));
    }
}
