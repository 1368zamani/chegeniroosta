<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SectionRequest;
use Illuminate\Http\Request;

class PublicSectionRequestController extends Controller
{
    private function getSection(): Section
    {
        if (!app()->bound('currentSection')) {
            abort(404, 'بخش یافت نشد.');
        }
        return app('currentSection');
    }

    public function requestCreate()
    {
        $section = $this->getSection();

        $subjects = [
            'آب', 'برق', 'گاز', 'مخابرات', 'راه',
            'پسماند', 'روشنایی', 'عمران', 'دهیاری', 'سایر'
        ];

        return view('section.site.request-create', compact('section', 'subjects'));
    }

    public function requestStore(Request $request)
    {
        $section = $this->getSection();

        $data = $request->validate([
            'full_name'   => 'required|string|max:100',
            'phone'       => 'required|string|max:20',
            'subject'     => 'required|string|max:100',
            'description' => 'required|string|max:2000',
            'attachment'  => 'nullable|file|max:25600',
        ]);

        $data['section_id'] = $section->id;
        $data['tracking_code'] = SectionRequest::generateTrackingCode();
        $data['status'] = 'submitted';
        $data['ip_address'] = $request->ip();

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('section-requests/' . $section->id, 'public');
        }

        $newRequest = SectionRequest::create($data);

        return redirect()->route('section.site.request.success', ['code' => $newRequest->tracking_code]);
    }

    public function requestSuccess($code)
    {
        $section = $this->getSection();

        $request = SectionRequest::where('section_id', $section->id)
            ->where('tracking_code', $code)
            ->firstOrFail();

        return view('section.site.request-success', compact('section', 'request'));
    }

    public function requestTrack()
    {
        $section = $this->getSection();
        return view('section.site.request-track', compact('section'));
    }

    public function requestTrackResult(Request $request)
    {
        $section = $this->getSection();

        $request->validate(['tracking_code' => 'required|string']);

        $found = SectionRequest::where('section_id', $section->id)
            ->where('tracking_code', $request->tracking_code)
            ->first();

        return view('section.site.request-track-result', compact('section', 'found'));
    }
}
