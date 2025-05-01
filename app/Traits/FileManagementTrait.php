<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


trait FileManagementTrait
{
    /**
     * Maneja el almacenamiento de archivos
     *
     * @param array $documentos Array de archivos subidos
     * @param array $nombresDocumentos Array de nombres asociados a los archivos
     * @param string $nombreDisco donde se almacenara el documento
     * @param string $nombreCarpeta donde se almacenaran los archivos
     * @throws Exception Si hay errores en la subida o validaciones
     */

     public function almacenamientoArchivos($documentos, $nombresDocumentos, $nombreDisco, $nombreCarpeta)
     {
         $archivosGuardados = [];
     
         // Verificación si $nombresDocumentos es un string (por ejemplo, JSON)
         if (is_string($nombresDocumentos)) {
             $decoded = json_decode($nombresDocumentos, true);
             if ($decoded !== null) {
                 $nombresDocumentos = $decoded; // Decodificamos el JSON a un array
             }
         }
     
         // Si $documentos no es un array, lo convertimos en uno, y hacemos lo mismo con $nombresDocumentos
         if (!is_array($documentos)) {
             $documentos = [$documentos]; 
             $nombresDocumentos = is_array($nombresDocumentos) ? $nombresDocumentos : [$nombresDocumentos];
         }
     
         foreach ($documentos as $index => $documento) {
             try {
                 $random = substr(str_shuffle("0123456789"), 0, 10);
                 $formatoDoc = $documento->getClientOriginalExtension();
                 $nombreDoc = $random . '_' . Str::slug(Carbon::now()->format('Y_m_d H_i_s'), '_') . '.' . $formatoDoc;

                $carpetaCompleta = "{$nombreDisco}/{$nombreCarpeta}";
                Storage::disk($nombreDisco)->makeDirectory($carpetaCompleta);

                Storage::disk($nombreDisco)->put("{$carpetaCompleta}/{$nombreDoc}", file_get_contents($documento));

                if (!Storage::disk($nombreDisco)->exists("{$carpetaCompleta}/{$nombreDoc}")) {
                    return ['success' => false, 'message' => 'Error al guardar el documento.'];
                }

     
                 $fileName = isset($nombresDocumentos[$index]['nombre_documento']) 
                     ? $nombresDocumentos[$index]['nombre_documento'] 
                     : 'Documento_Sin_Nombre';
     
                 $archivosGuardados[] = [
                     'nombre' => $fileName,
                     'ruta' => "storage/app/{$nombreDisco}/{$nombreCarpeta}/".$nombreDoc
                 ];
             } catch (\Exception $e) {
                 return ['success' => false, 'message' => $e->getMessage()];
             }
         }
     
         return [
             'success' => true,
             'message' => 'Documentos subidos correctamente.',
             'archivos' => $archivosGuardados
         ];
     }
}