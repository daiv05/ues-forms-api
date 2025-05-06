<?php

namespace App\Models\Seguridad;

use App\Models\Catalogo\Estado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudDesbloqueo extends Model
{
    use HasFactory;

    protected $table = 'ctrl_solicitudes_desbloqueos';

    protected $fillable = [
        'id_usuario',
        'id_estado',
        'id_usuario_autoriza',
        'justificacion_solicitud',
        'justificacion_rechazo'
    ];
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }
    
    public function usuarioAutoriza()
    {
        return $this->belongsTo(User::class, 'id_usuario_autoriza');
    }
}
