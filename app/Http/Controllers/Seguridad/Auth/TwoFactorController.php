<?php

namespace App\Http\Controllers\Seguridad\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TwoFactorController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasDeviceVerified()
            ? redirect(RouteServiceProvider::HOME)
            : view('seguridad.auth.two-factor');
    }

    public function sendTwoFactorCode(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $code = $user->generateTwoFactorCode();

        $user->sendTwoFactorCode($code);

        Session::flash('code-send', true);

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => 'Código de verificación enviado'
        ]);
    }

    function confirmTwoFactorCode(Request $request)
    {
        if (auth()->user()->hasDeviceVerified()) {
            return redirect(RouteServiceProvider::HOME);
        }

        $validator = Validator::make($request->all(), [
            'code' => ['required'],
        ]);

        if ($validator->fails()) {
            Session::flash('message', [
                'type' => 'error',
                'content' => 'Debe ingresar un código válido'
            ]);
            return back()->withInput();
        }

        $select = DB::table('two_factor_tokens')
            ->where('user_id', $request->user()->id)
            ->where('token', $request->input('code'));

        if ($select->get()->isEmpty()) {
            Session::flash('message', [
                'type' => 'error',
                'content' => 'El código ingresado es inválido'
            ]);
            return back()->withInput();
        }

        $select = DB::table('two_factor_tokens')
            ->where('user_id', $request->user()->id)
            ->where('token', $request->input('code'))
            ->delete();

        auth()->user()->markDeviceAsVerified();

        // Auth::login($request->user());

        Session::flash('message', [
            'type' => 'success',
            'content' => '¡Bienvenido!'
        ]);
        return redirect(RouteServiceProvider::HOME);
    }
}
