<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showForm($token)
    {
        $record = DB::table('password_reset_tokens')->where('token', $token)->first();
        abort_if(!$record, 404);

        return view('auth.reset-password', ['token' => $token, 'email' => $record->email]);
    }

    public function reset(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        $record = DB::table('password_reset_tokens')->where('token', $token)->first();
        abort_if(!$record, 404);

        if (Carbon::parse($record->created_at)->addMinutes(30)->isPast()) {
            DB::table('password_reset_tokens')->where('token',$token)->delete();
            return redirect()->route('password.request')->withErrors(['email'=>'Link đã hết hạn']);
        }

        User::where('email',$record->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where('token',$token)->delete();

        return redirect()->route('login')->with('success','Đổi mật khẩu thành công!');
    }
}

