<?php

namespace App\Policies\Catalogo;

use App\Models\Catalogo\TipoPregunta;
use App\Models\Seguridad\User;
use Illuminate\Auth\Access\Response;

class TipoPreguntaPolicy
{
    /*
     *
     * Determine whether the user can view any models.
    */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('REPORTES_CREAR');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TipoPregunta $tipoPregunta): bool
    {
        return $user->hasPermissionTo('REPORTES_CREAR');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TipoPregunta $tipoPregunta): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TipoPregunta $tipoPregunta): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TipoPregunta $tipoPregunta): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TipoPregunta $tipoPregunta): bool
    {
        return true;
    }
}
