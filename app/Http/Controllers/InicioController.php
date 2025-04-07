<?php

namespace App\Http\Controllers;

use App\Models\Reportes\AccionesReporte;
use App\Models\Reportes\Estado;
use App\Models\Reportes\HistorialAccionesReporte;
use App\Models\Reportes\Reporte;
use Illuminate\Http\Request;

class InicioController extends Controller
{
    function inicio(){

        $estados = Estado::orderBy('id')->pluck('nombre');
        $estados->push('NO ASIGNADO');
        $estados->push('NO PROCEDE');

        $reportesNoProcede = Reporte::where('no_procede', true);
        $reportes = Reporte::where('no_procede', false);

        $reportesNuevos7Dias = clone $reportes;
        $reportesNuevos15Dias = clone $reportes;

        $reportesEnProceso7Dias = clone $reportes;
        $reportesEnProceso15Dias = clone $reportes;

        $reportesFinalizados7Dias = clone $reportes;
        $reportesFinalizados15Dias = clone $reportes;

        $reportesNoProcede7Dias = clone $reportesNoProcede;
        $reportesNoProcede15Dias = clone $reportesNoProcede;

        // Semana actual
        $reportesNuevos7Dias = $reportesNuevos7Dias->where('fecha_reporte', '>=', now()->subDays(7))->count();
        $reportesEnProceso7Dias = $reportesEnProceso7Dias->whereHas('accionesReporte.historialAccionesReporte', function($query) {
            // y que no tenga un historial de acciones con estado 3, 4, 5 o 6
            $query->where('id_estado', 2)->where('fecha_actualizacion', '>=', now()->subDays(7));
        })->whereDoesntHave('accionesReporte.historialAccionesReporte', function($query) {
            $query->whereIn('id_estado', [3, 4, 5, 6]);
        })->count();

        $reportesFinalizados7Dias = $reportesFinalizados7Dias->whereHas('accionesReporte.historialAccionesReporte', function($query) {
            $query->where('id_estado', 5)->where('fecha_actualizacion', '>=', now()->subDays(7));
        })->count();
        $reportesNoProcede7Dias = $reportesNoProcede7Dias->where('updated_at', '>=', now()->subDays(7))->count();

        // Semana anterior
        $reportesNuevos15Dias = $reportesNuevos15Dias->where('fecha_reporte', '>=', now()->subDays(15))->where('fecha_reporte', '<', now()->subDays(7))->count();
        $reportesEnProceso15Dias = $reportesEnProceso15Dias->whereHas('accionesReporte.historialAccionesReporte', function($query) {
            $query->where('id_estado', 2)->where('fecha_actualizacion', '>=', now()->subDays(15))->where('fecha_actualizacion', '<', now()->subDays(7));
        })->whereDoesntHave('accionesReporte.historialAccionesReporte', function($query) {
            $query->whereIn('id_estado', [3, 4, 5, 6]);
        })->count();
        $reportesFinalizados15Dias = $reportesFinalizados15Dias->whereHas('accionesReporte.historialAccionesReporte', function($query) {
            $query->where('id_estado', 5)->where('fecha_actualizacion', '>=', now()->subDays(15))->where('fecha_actualizacion', '<', now()->subDays(7));
        })->count();
        $reportesNoProcede15Dias = $reportesNoProcede15Dias->where('updated_at', '>=', now()->subDays(15))->where('updated_at', '<', now()->subDays(7))->count();

        //Ahora brindame el porcentaje ya sea negativo o positivo de los reportes en los ultimos 7 dias
        if ($reportesNuevos15Dias == 0){
            $porcentajeReportesNuevos7Dias = $reportesNuevos7Dias > 0 ? 100 : 0;
        } else {
            $porcentajeReportesNuevos7Dias = round((($reportesNuevos7Dias - $reportesNuevos15Dias) / $reportesNuevos15Dias) * 100, 2);
        }

        if($reportesEnProceso15Dias == 0){
            $porcentajeReportesEnProceso7Dias = $reportesEnProceso7Dias > 0 ? 100 : 0;
        } else {
            $porcentajeReportesEnProceso7Dias = round((($reportesEnProceso7Dias - $reportesEnProceso15Dias) / $reportesEnProceso15Dias) * 100, 2);
        }

        if($reportesFinalizados15Dias == 0){
            $porcentajeReportesFinalizados7Dias = $reportesFinalizados7Dias > 0 ? 100 : 0;
        } else {
            $porcentajeReportesFinalizados7Dias = round((($reportesFinalizados7Dias - $reportesFinalizados15Dias) / $reportesFinalizados15Dias) * 100, 2);
        }

        if($reportesNoProcede15Dias == 0){
            $porcentajeReportesNoProcede7Dias = $reportesNoProcede7Dias > 0 ? 100 : 0;
        } else {
            $porcentajeReportesNoProcede7Dias = round((($reportesNoProcede7Dias - $reportesNoProcede15Dias) / $reportesNoProcede15Dias) * 100, 2);
        }

        $dataReportesNuevos = [
            'reportesNuevos7Dias' => $reportesNuevos7Dias,
            'reportesNuevos15Dias' => $reportesNuevos15Dias,
            'porcentajeReportesNuevos' => $porcentajeReportesNuevos7Dias >= 0 ? '+'.$porcentajeReportesNuevos7Dias : $porcentajeReportesNuevos7Dias
        ];

        $dataReportesEnProceso = [
            'reportesEnProceso7Dias' => $reportesEnProceso7Dias,
            'reportesEnProceso15Dias' => $reportesEnProceso15Dias,
            'porcentajeReportesEnProceso' => $porcentajeReportesEnProceso7Dias >= 0 ? '+'.$porcentajeReportesEnProceso7Dias : $porcentajeReportesEnProceso7Dias
        ];

        $dataReportesFinalizados = [
            'reportesFinalizados7Dias' => $reportesFinalizados7Dias,
            'reportesFinalizados15Dias' => $reportesFinalizados15Dias,
            'porcentajeReportesFinalizados' => $porcentajeReportesFinalizados7Dias >= 0 ? '+'.$porcentajeReportesFinalizados7Dias : $porcentajeReportesFinalizados7Dias
        ];

        $dataReportesNoProcede = [
            'reportesNoProcede7Dias' => $reportesNoProcede7Dias,
            'reportesNoProcede15Dias' => $reportesNoProcede15Dias,
            'porcentajeReportesNoProcede' => $porcentajeReportesNoProcede7Dias >= 0 ? '+'.$porcentajeReportesNoProcede7Dias : $porcentajeReportesNoProcede7Dias
        ];

        // Dame los 5 reportes que se han asignado mas recientemente
        $reportesEstadosMásRecientes = HistorialAccionesReporte::whereHas('accionesReporte.reporte')->orderBy('created_at', 'desc')->limit(5)->get();
        $reportesNoProcedeRecientes = Reporte::where('no_procede', true)->orderBy('updated_at', 'desc')->limit(5)->get();
        $reportesRecientes = Reporte::orderBy('created_at', 'desc')->limit(5)->get();

        //Ordena cada una de las acciones de los reportes por cuales son mas recientes y limitas a solo 5 de todas estas a la siguiente estructura
        // [
        //     'reporte' => ID_DEL_REPORTE,
        //     'acciones' => 'ACTUALIZACION | ASIGNACION | CREACION',
        //     'estado' => 'ESTADO_DEL REPORTE', en caso existira
        //     'fecha' => 'FECHA DE LA ACCION' para ordenarlas segun la fecha
        // ]
        $actividadReciente = [];

        foreach ($reportesEstadosMásRecientes as $reporte) {
            $actividadReciente[] = [
                'reporte' => $reporte->accionesReporte->reporte->id,
                'acciones' => 'ACTUALIZACION',
                'estado' => $reporte->estado->nombre,
                'fecha' => $reporte->created_at
            ];
        }

        foreach ($reportesNoProcedeRecientes as $reporte) {
            $actividadReciente[] = [
                'reporte' => $reporte->id,
                'acciones' => 'ACTUALIZACION',
                'estado' => 'NO PROCEDE',
                'fecha' => $reporte->updated_at
            ];
        }

        foreach ($reportesRecientes as $reporte) {
            $actividadReciente[] = [
                'reporte' => $reporte->id,
                'acciones' => 'CREACION',
                'fecha' => $reporte->created_at
            ];
        }

        usort($actividadReciente, function($a, $b) {
            return $b['fecha'] <=> $a['fecha'];
        });

        foreach ($actividadReciente as $key => $actividad) {
            $actividadReciente[$key]['fecha'] = $actividad['fecha']->format('d/m/Y H:i:s');
        }

        if(count($actividadReciente) > 5) {
            $actividadReciente = array_slice($actividadReciente, 0, 5);
        }
        // dd($actividadReciente);

        return view('dashboard', compact('dataReportesNuevos', 'dataReportesEnProceso', 'dataReportesFinalizados', 'dataReportesNoProcede', 'actividadReciente'));
    }
}
