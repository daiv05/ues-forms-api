<?php

namespace App\Enums;

enum PermisosEnum: string
{
    // Para gestion de usuarios
    case USUARIO_VER = 'usuario_ver';
    case USUARIO_CREAR = 'usuario_crear';
    case USUARIO_ACTUALIZAR = 'usuario_actualizar';
    // Para gestion de roles
    case ROL_VER = 'rol_ver';
    case ROL_CREAR = 'rol_crear';
    case ROL_ACTUALIZAR = 'rol_actualizar';
    // Para gestion de solicitudes de registro
    case SOLICITUD_REGISTRO_VER = 'solicitud_ver';
    case SOLICITUD_REGISTRO_CREAR = 'solicitud_crear';
    case SOLICITUD_REGISTRO_ACTUALIZAR = 'solicitud_actualizar';
    // Para gestion de solicitudes de desbloqueo
    case SOLICITUD_DESBLOQUEO_VER = 'solicitud_desbloqueo_ver';
    case SOLICITUD_DESBLOQUEO_CREAR = 'solicitud_desbloqueo_crear';
    case SOLICITUD_DESBLOQUEO_ACTUALIZAR = 'solicitud_desbloqueo_actualizar';
    // Para gestion de rutas
    case RUTA_VER = 'ruta_ver';
    case RUTA_CREAR = 'ruta_crear';
    case RUTA_ACTUALIZAR = 'ruta_actualizar';
    // Para gestion de encuestas
    case ENCUESTA_VER = 'encuesta_ver';
    case ENCUESTA_EDITOR = 'encuesta_editor';
    case ENCUESTA_ESTADISTICAS = 'encuesta_estadisticas';
    case ENCUESTA_PUBLICAR = 'encuesta_publicar';
    // Para gestion de grupos meta
    case GRUPO_META_VER = 'grupo_meta_ver';
    case GRUPO_META_CREAR = 'grupo_meta_crear';
    case GRUPO_META_ACTUALIZAR = 'grupo_meta_actualizar';
}
