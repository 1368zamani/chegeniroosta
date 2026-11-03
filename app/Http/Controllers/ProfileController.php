<?php

namespace App\Http\Controllers;

use App\Support\PasswordPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * تغییر رمز عبور توسط خود کاربر.
 *
 * در دسترس همه‌ی نقش‌ها (سوپرادمین، بخشدار، ادمین روستا، کارشناس).
 */
class ProfileController extends Controller
{
    /** نمایش فرم تغییر رمز */
    public function passwordForm()
    {
        return view('profile.password');
    }

    /** ذخیره‌ی رمز جدید */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => [
                'required',
                'string',
                'min:' . PasswordPolicy::MIN_LENGTH,
                'max:' . PasswordPolicy::MAX_LENGTH,
                'regex:/\p{L}/u',
                'regex:/[0-9\x{06F0}-\x{06F9}]/u',
                'confirmed',
            ],
        ], array_merge(
            PasswordPolicy::messages('password'),
            [
                'current_password.required' => 'رمز عبور فعلی را وارد کنید.',
                'password.confirmed'        => 'تکرار رمز عبور یکسان نیست.',
            ]
        ));

        // بررسی رمز فعلی
        if (! Hash::check($request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'رمز عبور فعلی نادرست است.',
            ]);
        }

        // جلوگیری از انتخاب رمز تکراری
        if (Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'رمز جدید نباید با رمز فعلی یکسان باشد.',
            ]);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        // بازسازی نشست برای جلوگیری از session fixation
        $request->session()->regenerate();

        return back()->with('success', 'رمز عبور شما با موفقیت تغییر کرد. ✅');
    }
}
