<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LevyAccount;
use App\Models\LevyAuditLog;
use App\Models\LevyFile;
use App\Models\LevyFileLine;
use App\Models\LevyVillageParam;
use App\Models\Village;
use App\Services\LevyXlsxWriter;
use App\Support\Number;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

class LevyController extends Controller
{
    /** سه ردیف عوارض سمت چپ اکسل (F4:F6) */
    private const DEFAULT_LINES = [
        'عوارض حصار کشی',
        'عوارض احداث غیرمسکونی',
        'عوارض تراکم مجاز',
    ];

    private function en($value)
    {
        return Number::en($value);
    }

    /** مشاهدهٔ صفحات کاری عوارض (گزارشها جدایند) */
    private function authorizeView(): void
    {
        abort_unless(Gate::allows('levy.view'), 403);
    }

    /** ویرایش اعداد پروندهٔ اکسل و ضرایب روستاها — مسئول عمرانی */
    private function authorizeManage(): void
    {
        abort_unless(Gate::allows('levy.manage'), 403, 'ویرایش عوارض فقط توسط مسئول عمرانی بخشداری مجاز است.');
    }

    /** مشاهدهٔ گزارشها — شامل ادمین روستا */
    private function authorizeReport(): void
    {
        abort_unless(Gate::allows('levy.report'), 403);
    }

    private function log(LevyFile $file, string $action, array $changes = []): void
    {
        LevyAuditLog::create([
            'levy_file_id' => $file->id,
            'user_id'      => auth()->id(),
            'action'       => $action,
            'changes'      => $changes ?: null,
            'ip'           => request()->ip(),
            'created_at'   => now(),
        ]);
    }

    private function totals($files): array
    {
        return [
            'lines_total'           => (int) $files->sum(fn($f) => $f->lines_total),
            'residential_levy'      => (int) $files->sum(fn($f) => $f->residential_levy),
            'density_levy'          => (int) $files->sum(fn($f) => $f->density_levy),
            'final_levy'            => (int) $files->sum(fn($f) => $f->final_levy),
            'transaction_value_sum' => (int) $files->sum(fn($f) => $f->params?->transaction_value ?? 0),
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // داشبورد و تنظیمات ضرایب
    // ═══════════════════════════════════════════════════════════

    public function dashboard()
    {
        $this->authorizeView();
        $villages = Village::all();
        $files = LevyFile::with('village', 'params', 'lines')->get();

        return view('admin.levy.dashboard', [
            'totalVillages'   => $villages->count(),
            'filesCount'      => $files->count(),
            'completeCount'   => $files->where('status', 'complete')->count(),
            'incompleteCount' => $files->where('status', '!=', 'complete')->count(),
            'totals'          => $this->totals($files),
            'recentFiles'     => $files->sortByDesc('updated_at')->take(8)->values(),
        ]);
    }

    /** ضرایب مرجع تمام روستاها (قابل ویرایش توسط کارشناس) */
    public function villages()
    {
        $this->authorizeView();
        $params = LevyVillageParam::with('village')->get()
            ->sortBy(fn($p) => $p->village?->name ?? '');

        return view('admin.levy.villages', ['params' => $params]);
    }

    public function paramsUpdate(Request $request, LevyVillageParam $param)
    {
        $this->authorizeManage();
        $v = $request->validate([
            'group'                    => ['required', 'string', 'max:4'],
            'transaction_value'        => ['required', 'numeric', 'min:0'],
            'construction_coefficient' => ['required', 'numeric', 'min:0'],
            'density_coefficient'      => ['required', 'numeric', 'min:0'],
            'density_area_source'      => ['required', 'in:Q,R'],
        ]);

        $param->update([
            'group'                    => $v['group'],
            'transaction_value'        => (int) $this->en($v['transaction_value']),
            'construction_coefficient' => (int) $this->en($v['construction_coefficient']),
            'density_coefficient'      => (int) $this->en($v['density_coefficient']),
            'density_area_source'      => $v['density_area_source'],
            'source_note'              => 'ویرایش شده توسط کارشناس',
        ]);

        // بازمحاسبه و وضعیت تمام پروندههای این روستا
        foreach (LevyFile::where('village_id', $param->village_id)->get() as $file) {
            $file->refreshCompletion();
            $file->save();
        }

        return back()->with('success', 'ضرایب روستا بهروز شد و محاسبات پروندهها بازمحاسبه گردید.');
    }

    public function accounts()
    {
        $this->authorizeView();
        return view('admin.levy.accounts', [
            'accounts' => LevyAccount::orderBy('sort_order')->get(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // پروندهها
    // ═══════════════════════════════════════════════════════════

    public function filesIndex(Request $request)
    {
        $this->authorizeView();
        $search = trim((string) $request->get('search', ''));
        $status = (string) $request->get('status', '');

        $query = LevyFile::with('village', 'params', 'lines');

        if ($search !== '') {
            $ids = Village::where('name', 'like', "%{$search}%")->pluck('id');
            $query->whereIn('village_id', $ids);
        }
        if (in_array($status, ['draft', 'incomplete', 'complete'], true)) {
            $query->where('status', $status);
        }

        return view('admin.levy.files.index', [
            'files'    => $query->orderBy('jalali_year', 'desc')->orderBy('id')->paginate(30)->withQueryString(),
            'villages' => Village::orderBy('name')->get(),
            'search'   => $search,
            'status'   => $status,
        ]);
    }

    public function filesCreate()
    {
        $this->authorizeManage();
        return view('admin.levy.files.create', [
            'villages' => Village::orderBy('name')->get(),
            'params'   => LevyVillageParam::all()->keyBy('village_id'),
            'accounts' => LevyAccount::orderBy('sort_order')->get(),
            'years'    => range((int) now()->year - 2, (int) now()->year + 1),
        ]);
    }

    public function filesStore(Request $request)
    {
        $this->authorizeManage();
        $data = $this->validateData($request);

        $exists = LevyFile::withTrashed()
            ->where('village_id', $data['village_id'])
            ->where('jalali_year', $data['jalali_year'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'برای این روستا و سال، پروندهای وجود دارد. از ویرایش همان پرونده استفاده کنید.');
        }

        return DB::transaction(function () use ($data) {
            $file = LevyFile::create($data + [
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            foreach (self::DEFAULT_LINES as $i => $title) {
                $file->lines()->create([
                    'title'      => $title,
                    'amount'     => $data['lines'][$i] ?? 0,
                    'sort_order' => $i + 1,
                ]);
            }

            $file->refreshCompletion();
            $file->save();
            $this->log($file, 'ایجاد پرونده', ['village' => $file->village?->name, 'year' => $file->jalali_year]);

            return redirect()->route('admin.levy.files.show', $file)->with('success', 'پرونده مستقل روستا ایجاد شد.');
        });
    }

    public function filesShow(LevyFile $file)
    {
        $this->authorizeView();
        $file->load('village', 'params', 'lines', 'audits.user');

        return view('admin.levy.files.show', [
            'file'     => $file,
            'accounts' => LevyAccount::orderBy('sort_order')->get(),
        ]);
    }

    public function filesUpdate(Request $request, LevyFile $file)
    {
        $this->authorizeManage();
        $data = $this->validateData($request, $file);

        return DB::transaction(function () use ($data, $file) {
            $before = $file->getRawOriginal();
            $file->fill($data);
            $file->updated_by = auth()->id();
            $file->refreshCompletion();
            $file->save();

            foreach (self::DEFAULT_LINES as $i => $title) {
                $line = $file->lines->firstWhere('sort_order', $i + 1);
                if ($line) {
                    $line->update(['amount' => $data['lines'][$i] ?? 0]);
                }
            }

            $this->log($file, 'ویرایش اطلاعات', $this->diff($before, $file->getRawOriginal()));
            return redirect()->route('admin.levy.files.show', $file)->with('success', 'اطلاعات ذخیره و محاسبات بهروز شدند.');
        });
    }

    /** ذخیرهٔ خودکار (AJAX) */
    public function filesAutosave(Request $request, LevyFile $file): JsonResponse
    {
        $this->authorizeManage();
        $data = $this->validateData($request, $file);

        return DB::transaction(function () use ($data, $file) {
            $before = $file->getRawOriginal();
            $file->fill($data);
            $file->updated_by = auth()->id();
            $file->refreshCompletion();
            $file->save();

            foreach (self::DEFAULT_LINES as $i => $title) {
                $line = $file->lines->firstWhere('sort_order', $i + 1);
                if ($line) {
                    $line->update(['amount' => $data['lines'][$i] ?? 0]);
                }
            }

            $this->log($file, 'ذخیره خودکار', $this->diff($before, $file->getRawOriginal()));

            return response()->json([
                'ok'          => true,
                'saved_at'    => now()->format('H:i:s'),
                'completion'  => $file->completion_percent,
                'status'      => $file->status,
                'lines_total' => $file->lines_total,
                'final_levy'  => $file->final_levy,
            ]);
        });
    }

    public function filesDestroy(LevyFile $file)
    {
        $this->authorizeManage();
        $this->log($file, 'حذف پرونده (بایگانی نرم)', ['village' => $file->village?->name]);
        $file->delete();

        return redirect()->route('admin.levy.files.index')->with('success', 'پرونده به بایگانی منتقل شد.');
    }

    public function filesAudit(LevyFile $file)
    {
        $this->authorizeView();
        $file->load('village');
        return view('admin.levy.files.audit', [
            'file'   => $file,
            'audits' => $file->audits()->with('user')->get(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // reportها
    // ═══════════════════════════════════════════════════════════

    public function reportsVillage(LevyFile $file)
    {
        $this->authorizeReport();
        $file->load('village', 'params', 'lines');
        return view('admin.levy.reports.village', ['file' => $file]);
    }

    public function reportsAll()
    {
        $this->authorizeReport();
        $files = LevyFile::with('village', 'params', 'lines')->get();
        return view('admin.levy.reports.all', [
            'files'  => $files,
            'totals' => $this->totals($files),
        ]);
    }

    public function reportsCompare()
    {
        $this->authorizeReport();
        $files = LevyFile::with('village', 'params', 'lines')->get()
            ->sortByDesc(fn($f) => $f->final_levy)->values();
        return view('admin.levy.reports.compare', ['files' => $files]);
    }

    public function reportsIncomplete()
    {
        $this->authorizeReport();
        $files = LevyFile::with('village', 'params', 'lines')
            ->where('status', '!=', 'complete')->get();
        return view('admin.levy.reports.incomplete', ['files' => $files]);
    }

    // ═══════════════════════════════════════════════════════════
    // خروجی اکسل
    // ═══════════════════════════════════════════════════════════

    public function exportFile(LevyFile $file)
    {
        $this->authorizeReport();
        $file->load('village', 'params', 'lines');
        $xlsx = LevyXlsxWriter::build(collect([$file]));

        return Response::streamDownload(function () use ($xlsx) {
            echo $xlsx;
        }, 'levy-' . $file->village?->slug . '-' . $file->jalali_year . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportExcel()
    {
        $this->authorizeReport();
        $files = LevyFile::with('village', 'params', 'lines')->get();
        $xlsx = LevyXlsxWriter::build($files);

        return Response::streamDownload(function () use ($xlsx) {
            echo $xlsx;
        }, 'levy-all-' . now()->format('Ymd') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ═══════════════════════════════════════════════════════════

    private function validateData(Request $request, ?LevyFile $file = null): array
    {
        $v = $request->validate([
            'village_id'          => ['required', 'exists:villages,id'],
            'jalali_year'         => ['required', 'digits:4'],
            'applicant_name'      => ['nullable', 'string', 'max:255'],
            'dehyari_name'        => ['nullable', 'string', 'max:255'],
            'dehyari_account_no'  => ['nullable', 'string', 'max:255'],
            'iban'                => ['nullable', 'string', 'max:255'],
            'id_number'           => ['nullable', 'string', 'max:255'],
            'village_group'       => ['nullable', 'string', 'max:255'],
            'class_status'        => ['nullable', 'string', 'max:255'],
            'construction_area'   => ['nullable', 'numeric', 'min:0'],
            'property_area'       => ['nullable', 'numeric', 'min:0'],
            'notes'               => ['nullable', 'string'],
            'lines'               => ['nullable', 'array'],
            'lines.*'             => ['nullable', 'numeric', 'min:0'],
        ]);

        $v['village_id'] = (int) $this->en($v['village_id']);
        $v['jalali_year'] = (string) $this->en($v['jalali_year']);
        foreach (['construction_area', 'property_area'] as $k) {
            if (isset($v[$k])) {
                $v[$k] = $this->en($v[$k]);
            }
        }
        $v['lines'] = array_map(fn($x) => (int) round((float) $this->en($x ?? 0)), $v['lines'] ?? []);

        return $v;
    }

    private function diff(array $before, array $after): array
    {
        $out = [];
        foreach ($after as $k => $val) {
            if (($before[$k] ?? null) != $val) {
                $out[$k] = ['old' => $before[$k] ?? null, 'new' => $val];
            }
        }
        return $out;
    }
}
