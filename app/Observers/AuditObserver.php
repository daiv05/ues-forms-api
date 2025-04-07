<?php

namespace App\Observers;

use OwenIt\Auditing\Models\Audit;

class AuditObserver
{
    /**
     * Handle the "creating" event.
     *
     * @param  \OwenIt\Auditing\Models\Audit  $audit
     * @return void
     */
    public function creating(Audit $audit)
    {
        // Mapea los eventos a sus versiones en español
        $translations = [
            'created'  => 'Creado',
            'updated'  => 'Actualizado',
            'deleted'  => 'Eliminado',
            'restored' => 'Restaurado',
            'forceDeleted' => 'Eliminado permanentemente',
            'user_registered' =>'Usuario registrado'
        ];

        // Traduce el evento si está mapeado
        if (isset($translations[$audit->event])) {
            $audit->event = $translations[$audit->event];
        }
    }
}

