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
        $villages = Village::orderBy('name')->get();

        if ($villages->isEmpty()) {
            abort(403, 'روستایی وجود ندارد.');
        }

        $villageId = (int) ($request->query('village_id') ?? $villages->first()->id);
        $village = $villages->firstWhere('id', $villageId);

        if (!$village) {
            abort(404);
        }

        $account = BankAccount::getDefaultForVillage($villageId);

        return view('expert.tariff-invoices.account', compact('villages', 'villageId', 'village', 'account'));
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'village_id'     => ['required', 'integer', 'exists:villages,id'],
            'bank_name'      => ['required', 'string', 'in:melli,post'],
            'account_holder' => ['required', 'string', 'max:200'],
            'iban'           => ['nullable', 'string', 'max:30'],
            'card_number'    => ['nullable', 'string', 'max:20'],
            'account_number' => ['nullable', 'string', 'max:30'],
            'note'           => ['nullable', 'string', 'max:1000'],
        ]);

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
}
