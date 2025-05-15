<?php

namespace App\Http\Controllers\Encuesta;

use App\Enums\EstadosEnum;
use App\Enums\GeneralEnum;
use App\Http\Controllers\Controller;
use App\Models\Encuesta\Encuesta;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class EncuestaController extends Controller
{
    use ResponseTrait, PaginationTrait;

    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'string|max:50|regex:/^[a-zA-Z\sáéíóúñÁÉÍÓÚÑ]+$/',
                'grupo_meta' => 'string|max:50|regex:/^[a-zA-Z\sáéíóúñÁÉÍÓÚÑ]+$/',
                'id_estado' => [
                    'integer',
                    Rule::in(EstadosEnum::encuestas())
                ],
            ], [
                'titulo.regex' => 'Caracteres no válidos en el título',
                'titulo.max' => 'El filtro por título no puede exceder los 50 caracteres',
                'grupo_meta.regex' => 'Caracteres no válidos en el grupo meta',
                'grupo_meta.max' => 'El filtro por grupo meta no puede exceder los 50 caracteres',
                'id_estado.integer' => 'El filtro por estado debe ser un número entero',
                'id_estado.in' => 'El estado de encuesta seleccionado no es válido',
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedData = $validator->validated();
            // Obtener las encuestas con los filtros aplicados
            $query = Encuesta::with(['grupoMeta:id,nombre', 'user:id,username,email', 'estado:id,nombre'])
                ->orderBy('created_at', 'desc');
            // Aplicar filtros
            if (isset($validatedData['titulo'])) {
                $query->where('titulo', 'ilike', '%' . $validatedData['titulo'] . '%');
            }
            if (isset($validatedData['grupo_meta'])) {
                $query->whereHas('grupoMeta', function ($q) use ($validatedData) {
                    $q->where('nombre', 'ilike', '%' . $validatedData['grupo_meta'] . '%');
                });
            }
            if (isset($validatedData['id_estado'])) {
                $query->where('id_estado', $validatedData['id_estado']);
            }
            $encuestas = $query->get();
            if ($request['paginate'] === "true") {
                $paginatedData = $this->paginate($encuestas->toArray(), $request['per_page'] ?? GeneralEnum::PAGINACION->value, $request['page'] ?? 1);
                return $this->successPaginated('Encuestas obtenidas exitosamente', $paginatedData, Response::HTTP_OK);
            } else {
                return $this->success('Encuestas obtenidas exitosamente', $encuestas, Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return $this->error('Error al obtener las encuestas', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function initNewSurvey(Request $request)
    {
        try {

            DB::beginTransaction();
            $usuario = auth()->user();
            $encuesta = Encuesta::create([
                'id_usuario' => $usuario->id,
                'id_grupo_meta' => null,
                'id_estado' => EstadosEnum::EN_EDICION->value,
                'codigo' => Str::random(10),
                'titulo' => 'Mi Encuesta',
                'objetivo' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'descripcion' => 'Por favor, responda las siguientes preguntas.',
                'fecha_publicacion' => null,
            ]);
            DB::commit();
            return $this->success('Encuesta creada exitosamente', $encuesta, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al crear la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showInternalData($id)
    {
        try {
            $encuesta = Encuesta::select('id', 'objetivo', 'codigo', 'id_estado', 'id_grupo_meta', 'id_usuario')->with(['grupoMeta:id,nombre', 'estado:id,nombre'])->find($id);
            if (!$encuesta) {
                return $this->error('Encuesta no encontrada', 'No se encontró la encuesta con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }

            if ($encuesta->id_usuario !== auth('api')->user()->id) {
                return $this->error('Acceso denegado', 'No tienes permiso para acceder a esta encuesta', Response::HTTP_FORBIDDEN);
            }

            return $this->success('Datos internos de la encuesta obtenidos exitosamente', $encuesta, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error('Error al obtener los datos internos de la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showGeneralInfo($id)
    {
        try {
            $encuesta = Encuesta::select('id', 'titulo', 'descripcion', 'id_usuario')->find($id);
            if (!$encuesta) {
                return $this->error('Encuesta no encontrada', 'No se encontró la encuesta con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            if ($encuesta->id_usuario !== auth('api')->user()->id) {
                return $this->error('Acceso denegado', 'No tienes permiso para acceder a esta encuesta', Response::HTTP_FORBIDDEN);
            }

            return $this->success('Informacion general de la encuesta obtenida exitosamente', $encuesta, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error('Error al obtener la informacion general de la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showForm($id)
    {
        try {
            $encuesta = Encuesta::select('id', 'id_usuario')->find($id);
            if (!$encuesta) {
                return $this->error('Encuesta no encontrada', 'No se encontró la encuesta con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            if ($encuesta->id_usuario !== auth('api')->user()->id) {
                return $this->error('Acceso denegado', 'No tienes permiso para acceder a esta encuesta', Response::HTTP_FORBIDDEN);
            }

            // TODO: Implementar la lógica para mostrar el formulario de la encuesta

            return $this->success('Formulario de la encuesta obtenido exitosamente', $encuesta, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error('Error al obtener el formulario de la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateInternalData(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_grupo_meta' => 'integer|exists:srvy_grupos_metas,id',
                'objetivo' => 'string|max:500',
                'identificador' => 'string|max:50',
            ], [
                'id_grupo_meta.integer' => 'El grupo meta debe ser un número entero',
                'id_grupo_meta.exists' => 'El grupo meta seleccionado no es válido',
                'objetivo.string' => 'El objetivo debe ser una cadena de texto',
                'objetivo.max' => 'El objetivo no puede exceder los 500 caracteres',
                'identificador.string' => 'El identificador debe ser una cadena de texto',
                'identificador.max' => 'El identificador no puede exceder los 50 caracteres',
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Verificar si el grupo meta pertenece al usuario
            $grupoMeta = $request->input('id_grupo_meta');
            if ($grupoMeta) {
                $grupoMeta = DB::table('srvy_grupos_metas')->where('id', $grupoMeta)->where('id_usuario', auth()->user()->id)->first();
                if (!$grupoMeta) {
                    return $this->error('Grupo meta no encontrado', 'No se encontró el grupo meta con el ID proporcionado', Response::HTTP_NOT_FOUND);
                }
            }

            $validatedData = $validator->validated();
            $encuesta = Encuesta::find($id);
            if (!$encuesta) {
                return $this->error('Encuesta no encontrada', 'No se encontró la encuesta con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            if ($encuesta->id_usuario !== auth()->user()->id) {
                return $this->error('Acceso denegado', 'No tienes permiso para acceder a esta encuesta', Response::HTTP_FORBIDDEN);
            }
            DB::beginTransaction();
            $encuesta->update([
                'id_grupo_meta' => $validatedData['id_grupo_meta'] ?? $encuesta->id_grupo_meta,
                'objetivo' => $validatedData['objetivo'] ?? $encuesta->objetivo,
                'codigo' => $validatedData['identificador'] ?? $encuesta->codigo,
            ]);
            DB::commit();
            return $this->success('Datos internos de la encuesta actualizados exitosamente', $encuesta, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al actualizar los datos internos de la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateGeneralInfo(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'string|max:50|regex:/^[a-zA-Z\sáéíóúñÁÉÍÓÚÑ]+$/',
                'descripcion' => 'string|max:500',
            ], [
                'titulo.regex' => 'Caracteres no válidos en el título',
                'titulo.max' => 'El título no puede exceder los 50 caracteres',
                'descripcion.string' => 'La descripcion debe ser una cadena de texto',
                'descripcion.max' => 'La descripcion no puede exceder los 500 caracteres',
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $validatedData = $validator->validated();
            $encuesta = Encuesta::find($id);
            if (!$encuesta) {
                return $this->error('Encuesta no encontrada', 'No se encontró la encuesta con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            if ($encuesta->id_usuario !== auth('api')->user()->id) {
                return $this->error('Acceso denegado', 'No tienes permiso para acceder a esta encuesta', Response::HTTP_FORBIDDEN);
            }
            DB::beginTransaction();
            $encuesta->update([
                'titulo' => $validatedData['titulo'] ?? $encuesta->titulo,
                'descripcion' => $validatedData['descripcion'] ?? $encuesta->descripcion,
            ]);
            DB::commit();
            return $this->success('Información general de la encuesta actualizada exitosamente', $encuesta, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al actualizar la información general de la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateForm(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'formulario' => 'array|required',
                'formulario.*.id_categoria_pregunta' => 'required|integer|exists:qst_categorias_preguntas,id',
                'formulario.*.descripcion_pregunta' => 'required|string|max:50',
                'formulario.*.false_txt' => 'string|max:50',
                'formulario.*.true_txt' => 'string|max:50',
                'formulario.*.min_val' => 'integer|min:0|max:100',
                'formulario.*.max_val' => 'integer|min:0|max:100',
                'formulario.*.opciones' => 'array',
                'formulario.*.opciones.*.opcion' => 'string|max:50',
                'formulario.*.opciones.*.orden_inicial' => 'integer|min:0|max:100',
            ], [
                'formulario.required' => 'El formulario es obligatorio',
                'formulario.array' => 'El formulario debe ser un arreglo',
                'formulario.*.id_categoria_pregunta.required' => 'La categoría de pregunta es obligatoria',
                'formulario.*.id_categoria_pregunta.integer' => 'La categoría de pregunta debe ser un número entero',
                'formulario.*.id_categoria_pregunta.exists' => 'La categoría de pregunta no existe',
                'formulario.*.descripcion_pregunta.required' => 'La descripción de la pregunta es obligatoria',
                'formulario.*.descripcion_pregunta.string' => 'La descripción de la pregunta debe ser una cadena de texto',
                'formulario.*.descripcion_pregunta.max' => 'La descripción de la pregunta no puede exceder los 50 caracteres',
                'formulario.*.false_txt.string' => 'El texto falso debe ser una cadena de texto',
                'formulario.*.false_txt.max' => 'El texto falso no puede exceder los 50 caracteres',
                'formulario.*.true_txt.string' => 'El texto verdadero debe ser una cadena de texto',
                'formulario.*.true_txt.max' => 'El texto verdadero no puede exceder los 50 caracteres',
                'formulario.*.min_val.integer' => 'El valor mínimo de la escala numerica debe ser un número entero',
                'formulario.*.min_val.min' => 'El valor mínimo de la escala numerica no puede ser menor que 0',
                'formulario.*.min_val.max' => 'El valor mínimo de la escala numerica no puede ser mayor que 100',
                'formulario.*.max_val.integer' => 'El valor máximo de la escala numerica debe ser un número entero',
                'formulario.*.max_val.min' => 'El valor máximo de la escala numerica no puede ser menor que 0',
                'formulario.*.max_val.max' => 'El valor máximo de la escala numerica no puede ser mayor que 100',
            ]);

            $encuesta = Encuesta::find($id);
            if (!$encuesta) {
                return $this->error('Encuesta no encontrada', 'No se encontró la encuesta con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            if ($encuesta->id_usuario !== auth('api')->user()->id) {
                return $this->error('Acceso denegado', 'No tienes permiso para acceder a esta encuesta', Response::HTTP_FORBIDDEN);
            }

            // TODO: Implementar la lógica para actualizar el formulario de la encuesta
            
            
            return $this->success('Formulario de la encuesta actualizado exitosamente', $encuesta, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar el formulario de la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $encuesta = Encuesta::find($id);
            if (!$encuesta) {
                return $this->error('Encuesta no encontrada', 'No se encontró la encuesta con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            if ($encuesta->id_usuario !== auth('api')->user()->id) {
                return $this->error('Acceso denegado', 'No tienes permiso para acceder a esta encuesta', Response::HTTP_FORBIDDEN);
            }
            DB::beginTransaction();
            $encuesta->delete();
            DB::commit();
            return $this->success('Encuesta eliminada exitosamente', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al eliminar la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
