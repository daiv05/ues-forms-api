<?php

namespace App\Http\Controllers\Seguridad\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect(RouteServiceProvider::HOME);
        }

        $verify =  DB::table('password_reset_tokens')->where([
            ['email', $request->user()->email]
        ]);

        if ($verify->exists()) {
            $verify->delete();
        }

        $code = rand(100000, 999999);

        DB::table('password_reset_tokens')
            ->insert(
                [
                    'email' => $request->user()->email,
                    'token' => $code
                ]
            );

        Mail::to($request->user()->email)->send(new VerifyEmail($code));

        Session::flash('code-send', true);

        return redirect()->back()->with('message', [
                'type' => 'success',
                'content' => 'Código de verificación enviado'
            ]);
    }
}
