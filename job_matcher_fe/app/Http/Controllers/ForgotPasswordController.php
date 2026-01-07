<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        $resetLink = url('/reset-password/'.$token);

        Mail::raw("Nhấn vào link để đặt lại mật khẩu: $resetLink", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Đặt lại mật khẩu');
        });

        return back()->with('success','Link đặt lại mật khẩu đã được gửi về email!');
    }
}
