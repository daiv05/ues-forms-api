<?php

namespace App\Http\Controllers\Auditorias;

use App\Enums\GeneralEnum;
use OwenIt\Auditing\Models\Audit;
use App\Models\Seguridad\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Jenssegers\Agent\Agent;
use App\Models\Registro\Persona;

class UserAuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::query();
        $filtersApplied = false;

        if ($request->has('model') && $request->model) {
            $query->where('auditable_type', $request->model);
            $filtersApplied = true;
        }

        if ($request->has('event') && $request->event) {
            $query->where('event', $request->event);
            $filtersApplied = true;
        }


        if ($request->has('titulo') && $request->titulo) {
            $personas = Persona::where('nombre', 'like', '%' . $request->titulo . '%')
                ->orWhere('apellido', 'like', '%' . $request->titulo . '%')
                ->pluck('id');

            if ($personas->isNotEmpty()) {
                $query->whereIn('user_id', $personas);
                $filtersApplied = true;
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
            $query->whereRaw('DATE(created_at) BETWEEN ? AND ?', [$request->start_date, $request->end_date]);
            $filtersApplied = true;
        }


        if (!$filtersApplied) {
            $audits = new LengthAwarePaginator([], 0, 5);
        } else {
            $audits = $query->paginate(GeneralEnum::PAGINACION->value);
        }

        $models = Audit::select('auditable_type')->distinct()->get();

        $events = [];
        if ($request->has('model') && $request->model) {
            $events = Audit::select('event')
                ->where('auditable_type', $request->model)
                ->distinct()
                ->get();
        }

        $users = User::all();

        return view('audits.index', compact('audits', 'models', 'events', 'users'));
    }
    public function getEvents(Request $request)
    {
        if ($request->has('model')) {
            $events = Audit::select('event')
                ->where('auditable_type', $request->model)
                ->distinct()
                ->get();

            return response()->json($events);
        }

        return response()->json([]);
    }
}
