<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminPasswordController extends Controller
{
    public function index()
    {
        $current = auth()->user();

        if ($current->role === 'super_admin') {

            $users = User::whereIn('role', [
                    'super_admin',
                    'section_admin',
                    'village_admin',
                    'fee_officer',
                    'tariff_officer'
                ])
                ->orderBy('role')
                ->orderBy('name')
                ->get();

        } elseif ($current->role === 'section_admin') {

            $users = User::where('section_id', $current->section_id)
                ->whereIn('role', [
                    'section_admin',
                    'village_admin',
                    'fee_officer',
                    'tariff_officer'
                ])
                ->orderBy('role')
                ->orderBy('name')
                ->get();

        } else {

            abort(403);

        }


        return view('admin.passwords.index', compact('users'));
    }



    public function reset(Request $request, User $user)
    {
        // سیاست رمز مشترک: حداقل ۸ کاراکتر + حرف + عدد
        $request->validate(
            \App\Support\PasswordPolicy::rules('password'),
            \App\Support\PasswordPolicy::messages('password')
        );

        // ═══ بررسی دسترسی (هم‌راستا با index) ═══
        // بدون این گارد، هر کاربر وارد‌شده‌ای می‌توانست رمز هر کاربری
        // — از جمله سوپرادمین — را تغییر دهد و کنترل کامل سایت را بگیرد.
        $current = auth()->user();

        if ($current->role === 'super_admin') {

            // سوپرادمین می‌تواند رمز همه را تغییر دهد.

        } elseif ($current->role === 'section_admin') {

            $targetIsSuperAdmin = $user->role === 'super_admin';
            $sameSection        = (int) $user->section_id === (int) $current->section_id;
            $roleAllowed        = in_array($user->role, [
                'section_admin',
                'village_admin',
                'fee_officer',
                'tariff_officer',
            ], true);

            abort_if(
                $targetIsSuperAdmin || !$sameSection || !$roleAllowed,
                403,
                'شما اجازه تغییر رمز این کاربر را ندارید.'
            );

        } else {

            abort(403, 'شما اجازه تغییر رمز کاربران را ندارید.');

        }

        $user->password = Hash::make($request->password);

        $user->save();


        return back()->with(
            'success',
            'رمز عبور کاربر تغییر کرد.'
        );
    }
}
