<?php

namespace App\Http\Controllers\Seguridad\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use IvanoMatteo\LaravelDeviceTracking\Facades\DeviceTracker;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        if (auth()->user()->hasVerifiedEmail()) {
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

        $select = DB::table('password_reset_tokens')
            ->where('email', $request->user()->email)
            ->where('token', $request->input('code'));

        if ($select->get()->isEmpty()) {
            Session::flash('message', [
                'type' => 'error',
                'content' => 'El código ingresado es inválido'
            ]);
            return back()->withInput();
        }

        $select = DB::table('password_reset_tokens')
            ->where('email', $request->user()->email)
            ->where('token', $request->input('code'))
            ->delete();

        auth()->user()->markEmailAsVerified();

        event(new Verified(auth()->user()));

        DeviceTracker::detectFindAndUpdate();
        DeviceTracker::flagCurrentAsVerified();

        // Auth::login($request->user());

        Session::flash('message', [
            'type' => 'success',
            'content' => '¡Bienvenido!'
        ]);
        return redirect(RouteServiceProvider::HOME);
    }
}
