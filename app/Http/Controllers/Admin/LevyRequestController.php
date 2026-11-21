<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LevyPublicRequest;
use App\Models\Village;
use Illuminate\Http\Request;

/**
 * بررسی درخواستهای مردمی عوارض (تأیید / رد رسید پرداخت)
 */
class LevyRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = LevyPublicRequest::with('village')->orderByDesc('id');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }
        if ($villageId = $request->get('village_id')) {
            $query->where('village_id', (int) $villageId);
        }
        if ($q = trim((string) $request->get('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('tracking_code', 'like', "%{$q}%")
                  ->orWhere('payer_name', 'like', "%{$q}%")
                  ->orWhere('payer_mobile', 'like', "%{$q}%");
            });
        }

        $rows = $query->paginate(25)->withQueryString();

        return view('admin.levy.requests.index', [
            'rows'     => $rows,
            'villages' => Village::orderBy('name')->get(['id', 'name']),
            'filters'  => $request->only(['status', 'type', 'village_id', 'q']),
            'counts'   => [
                'all'        => LevyPublicRequest::count(),
                'awaiting'   => LevyPublicRequest::where('status', 'awaiting_receipt')->count(),
                'under'      => LevyPublicRequest::where('status', 'under_review')->count(),
                'approved'   => LevyPublicRequest::where('status', 'approved')->count(),
                'rejected'   => LevyPublicRequest::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function show(LevyPublicRequest $levyRequest)
    {
        $levyRequest->load('village', 'items', 'events.user', 'reviewer');

        return view('admin.levy.requests.show', ['row' => $levyRequest]);
    }

    public function approve(LevyPublicRequest $levyRequest)
    {
        $levyRequest->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => null,
        ]);
        $levyRequest->addEvent('approved', 'تأیید رسید پرداخت توسط کارشناس');

        return back()->with('success', 'رسید پرداخت تأیید شد.');
    }

    public function reject(Request $request, LevyPublicRequest $levyRequest)
    {
        $data = $request->validate([
            'review_note' => ['required', 'string', 'max:1000'],
        ], [
            'review_note.required' => 'دلیل رد درخواست را وارد کنید.',
        ]);

        $levyRequest->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $data['review_note'],
        ]);
        $levyRequest->addEvent('rejected', 'رد درخواست: ' . $data['review_note']);

        return back()->with('success', 'درخواست رد شد و دلیل آن ثبت گردید.');
    }
}
