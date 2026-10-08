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
        $request->validate([
            'password' => 'required|min:6'
        ]);


        $user->password = Hash::make($request->password);

        $user->save();


        return back()->with(
            'success',
            'رمز عبور کاربر تغییر کرد.'
        );
    }
}
