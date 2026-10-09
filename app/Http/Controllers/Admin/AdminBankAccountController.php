<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminBankAccountController extends Controller
{
    /**
     * فرم ویرایش حساب بانکی
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        // لیست روستاهای مجاز
        $villages = match ($user->role) {
            'super_admin'   => Village::orderBy('name')->get(),
            'section_admin' => Village::where('section_id', $user->section_id)->orderBy('name')->get(),
            'village_admin' => Village::where('id', $user->village_id)->get(),
            default         => collect(),
        };

        if ($villages->isEmpty()) {
            abort(403, 'دسترسی ندارید.');
        }

        $villageId = (int) ($request->query('village_id') ?? $villages->first()->id);

        // چک دسترسی به روستای انتخاب‌شده
        if (!$villages->contains('id', $villageId)) {
            abort(403, 'دسترسی به این روستا ندارید.');
        }

        $village = $villages->firstWhere('id', $villageId);
        $account = BankAccount::getDefaultForVillage($villageId);

        return view('admin.tariff-invoices.account', compact('villages', 'villageId', 'village', 'account'));
    }

    /**
     * ذخیره حساب بانکی
     */
    public function save(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'village_id'     => ['required', 'integer', 'exists:villages,id'],
            'bank_name'      => ['required', 'string', 'in:melli,post'],
            'account_holder' => ['required', 'string', 'max:200'],
            'iban'           => ['nullable', 'string', 'max:30'],
            'card_number'    => ['nullable', 'string', 'max:20'],
            'account_number' => ['nullable', 'string', 'max:30'],
            'note'           => ['nullable', 'string', 'max:1000'],
        ]);

        // چک دسترسی
        $canManage = match ($user->role) {
            'super_admin'   => true,
            'section_admin' => Village::where('id', $data['village_id'])
                                ->where('section_id', $user->section_id)
                                ->exists(),
            'village_admin' => (int) $user->village_id === (int) $data['village_id'],
            default         => false,
        };

        if (!$canManage) {
            abort(403, 'دسترسی ندارید.');
        }

        try {
            DB::beginTransaction();

            // آپدیت یا ساخت
            $account = BankAccount::getDefaultForVillage($data['village_id']);

            if (!$account) {
                $account = new BankAccount();
                $account->village_id = $data['village_id'];
                $account->is_default = true;
                $account->is_active = true;
            }

            $account->fill([
                'bank_name'      => $data['bank_name'],
                'account_holder' => $data['account_holder'],
                'iban'           => $data['iban'],
                'card_number'    => $data['card_number'],
                'account_number' => $data['account_number'],
                'note'           => $data['note'],
                'is_active'      => true,
            ]);

            $account->save();

            DB::commit();

            return back()->with('success', 'حساب بانکی ذخیره شد.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطا: ' . $e->getMessage()]);
        }
    }
}
