<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $persona = $request->user()->persona;
        $persona->fecha_nacimiento = Carbon::createFromFormat('Y-m-d', $persona->fecha_nacimiento)->format('m/d/Y');
        return view('seguridad.profile.edit', [
            'user' => $request->user(),
            'persona' => $request->user()->persona
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
         $request->merge([
            'fecha_nacimiento' => Carbon::createFromFormat('d/m/Y', $request->input('fecha_nacimiento'))->format('Y-m-d')
        ]);
        $user = $request->user();
        $persona = $user->persona;

        $user->fill($request->only(['carnet', 'email']));
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        $persona->update($request->only(['nombre', 'apellido', 'fecha_nacimiento', 'telefono']));

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated')
            ->with('message', [
                'type' => 'success',
                'content' => 'Perfil actualizado exitosamente.',
            ]);
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
