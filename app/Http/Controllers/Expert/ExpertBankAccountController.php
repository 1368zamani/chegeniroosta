<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpertBankAccountController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        // ═══ لیست روستاهای مجاز ═══
        $villages = $this->allowedVillages($user);

        if ($villages->isEmpty()) {
            abort(403, 'شما به هیچ روستایی دسترسی ندارید.');
        }

        $villageId = (int) ($request->query('village_id') ?? $villages->first()->id);

        // چک: روستای انتخابی در لیست مجاز هست؟
        $village = $villages->firstWhere('id', $villageId);

        if (!$village) {
            $village = $villages->first();
            $villageId = $village->id;
        }

        $account = BankAccount::getDefaultForVillage($villageId);

        return view('expert.tariff-invoices.account', compact('villages', 'villageId', 'village', 'account'));
    }

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

        // ═══ چک دسترسی ═══
        $this->authorizeVillageAccess($user, $data['village_id']);

        try {
            DB::beginTransaction();

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

    private function allowedVillages($user)
    {
        $q = Village::orderBy('name');

        if ($user->role === 'section_admin' && $user->section_id) {
            $q->where('section_id', $user->section_id);
        } elseif ($user->role === 'village_admin' && $user->village_id) {
            $q->where('id', $user->village_id);
        } elseif ($user->role === 'fee_officer' && $user->section_id) {
            $q->where('section_id', $user->section_id);
        }

        return $q->get();
    }

    private function authorizeVillageAccess($user, int $villageId): void
    {
        if ($user->role === 'super_admin') {
            return;
        }

        $village = Village::find($villageId);
        if (!$village) {
            abort(404);
        }

        if ($user->role === 'section_admin' && $user->section_id) {
            if ((int) $village->section_id !== (int) $user->section_id) {
                abort(403, 'شما به این روستا دسترسی ندارید.');
            }
            return;
        }

        if ($user->role === 'village_admin' && $user->village_id) {
            if ((int) $village->id !== (int) $user->village_id) {
                abort(403);
            }
            return;
        }

        if ($user->role === 'fee_officer' && $user->section_id) {
            if ((int) $village->section_id !== (int) $user->section_id) {
                abort(403, 'شما به این روستا دسترسی ندارید.');
            }
            return;
        }

        abort(403);
    }
}
