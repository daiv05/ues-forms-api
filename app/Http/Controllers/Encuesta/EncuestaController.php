<?php

namespace App\Http\Controllers\Encuesta;

use App\Enums\CategoriaPreguntasEnum;
use App\Enums\EstadosEnum;
use App\Enums\GeneralEnum;
use App\Http\Controllers\Controller;
use App\Models\Encuesta\Encuesta;
use App\Models\Encuesta\Pregunta;
use App\Models\Respuesta\Encuestado;
use App\Models\Respuesta\EncuestaRespuesta;
use App\Models\Respuesta\OpcionSeleccionada;
use App\Models\Respuesta\RespuestaPregunta;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

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
                ->where('id_usuario', JWTAuth::user()->id)
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

            $encuesta->load(['preguntas.preguntasOpciones', 'preguntas.preguntasTextosBooleanos', 'preguntas.preguntasEscalasNumericas']);

            $formularioResponse = [];
            foreach ($encuesta->preguntas as $pregunta) {
                $optionsList = [];
                if ($pregunta->categoriaPregunta->codigo === CategoriaPreguntasEnum::FALSO_VERDADERO->value) {
                    $optionsList = [
                        $pregunta->preguntasTextosBooleanos?->true_txt,
                        $pregunta->preguntasTextosBooleanos?->false_txt,
                    ];
                } else {
                    $optionsList = $pregunta->preguntasOpciones->pluck('opcion');
                }
                $formularioResponse[] = [
                    'nombre' => $pregunta->categoriaPregunta->nombre,
                    'type' => $pregunta->categoriaPregunta->codigo,
                    'shortQuestion' => $pregunta->descripcion,
                    'allowOtherOption' => $pregunta->es_abierta,
                    'options' => $optionsList,
                    'rangeFrom' => $pregunta->preguntasEscalasNumericas->first()?->min_val ?? 0,
                    'rangeTo' => $pregunta->preguntasEscalasNumericas->first()?->max_val ?? 0,
                ];
            }
            $encuesta->formulario = $formularioResponse;
            $encuestaResponse = $encuesta->only('id', 'formulario');
            return $this->success('Formulario de la encuesta obtenido exitosamente', $encuestaResponse, Response::HTTP_OK);
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
                'formulario.*.type' => [
                    'required',
                    'string',
                    Rule::in(array_map(fn($item) => $item->value, CategoriaPreguntasEnum::cases())),
                ],
                'formulario.*.shortQuestion' => 'required|string|max:50',
                'formulario.*.rangeFrom' => 'integer|min:0|max:100',
                'formulario.*.rangeTo' => 'integer|min:0|max:100',
                // 'formulario.*.rangeFrom' => 'string',
                // 'formulario.*.rangeTo' => 'string',
                'formulario.*.options' => 'array',
                'formulario.*.options.*' => 'string|max:50',
                'formulario.*.allowOtherOption' => 'boolean',
            ], [
                'formulario.required' => 'El formulario es obligatorio',
                'formulario.array' => 'El formulario debe ser un arreglo',
                'formulario.*.type.required' => 'El tipo de pregunta es obligatorio',
                'formulario.*.type.string' => 'El tipo de pregunta debe ser una cadena de texto',
                'formulario.*.type.in' => 'El tipo de pregunta no es válido',
                'formulario.*.shortQuestion.required' => 'La pregunta corta es obligatoria',
                'formulario.*.shortQuestion.string' => 'La pregunta corta debe ser una cadena de texto',
                'formulario.*.shortQuestion.max' => 'La pregunta corta no puede exceder los 50 caracteres',
                'formulario.*.rangeFrom.integer' => 'El rango desde debe ser un número entero',
                'formulario.*.rangeFrom.min' => 'El rango desde debe ser mayor o igual a 0',
                'formulario.*.rangeFrom.max' => 'El rango desde debe ser menor o igual a 100',
                'formulario.*.rangeTo.integer' => 'El rango hasta debe ser un número entero',
                'formulario.*.rangeTo.min' => 'El rango hasta debe ser mayor o igual a 0',
                'formulario.*.rangeTo.max' => 'El rango hasta debe ser menor o igual a 100',
                // 'formulario.*.rangeFrom.string' => 'El rango desde debe ser una cadena de texto',
                // 'formulario.*.rangeTo.string' => 'El rango hasta debe ser una cadena de texto',
                'formulario.*.options.array' => 'Las opciones deben ser un arreglo',
                'formulario.*.options.*.string' => 'Las opciones deben ser cadenas de texto',
                'formulario.*.options.*.max' => 'Las opciones no pueden exceder los 50 caracteres',
                'formulario.*.allowOtherOption.boolean' => 'El campo allowOtherOption debe ser verdadero o falso',
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $encuesta = Encuesta::find($id);
            if (!$encuesta) {
                return $this->error('Encuesta no encontrada', 'No se encontró la encuesta con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            if ($encuesta->id_usuario !== auth('api')->user()->id) {
                return $this->error('Acceso denegado', 'No tienes permiso para acceder a esta encuesta', Response::HTTP_FORBIDDEN);
            }
            if ($encuesta->id_estado !== EstadosEnum::EN_EDICION->value) {
                return $this->error('Encuesta no editable', 'Ya no puedes actualizar las preguntas de esta encuesta', Response::HTTP_FORBIDDEN);
            }

            DB::beginTransaction();

            $formulario = $request->input('formulario');

            // Validar campos especiales requeridos segun categoria de pregunta
            foreach ($formulario as $pregunta) {
                $tipoPregunta = CategoriaPreguntasEnum::from($pregunta['type']);
                $camposRequeridos = $tipoPregunta->fieldsRequired();
                foreach ($camposRequeridos as $campo) {
                    if (!isset($pregunta[$campo])) {
                        return $this->error('Error de validación', 'El campo ' . $campo . ' es obligatorio para el tipo de pregunta ' . $tipoPregunta->name, Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
                }
            }

            // Actualizar con nuevas preguntas
            $encuesta->preguntas()->delete();
            foreach ($formulario as $pregunta) {
                $srvy_pregunta = Pregunta::create([
                    'id_categoria_pregunta' => CategoriaPreguntasEnum::from($pregunta['type'])->id(),
                    'id_encuesta' => $encuesta->id,
                    'descripcion' => $pregunta['shortQuestion'],
                    'es_abierta' => $pregunta['allowOtherOption'] ?? false,
                ]);
                switch ($pregunta['type']) {
                    case CategoriaPreguntasEnum::TEXTO_CORTO->value:
                        break;
                    case CategoriaPreguntasEnum::TEXTO_LARGO->value:
                        break;
                    case CategoriaPreguntasEnum::SELECCION_MULTIPLE->value:
                        $srvy_preguntas_opciones = [];
                        foreach ($pregunta['options'] as $index => $opcion) {
                            $srvy_preguntas_opciones[] = [
                                'id_pregunta' => $srvy_pregunta->id,
                                'opcion' => $opcion,
                                'orden_inicial' => $index
                            ];
                        }
                        $srvy_pregunta->preguntasOpciones()->createMany($srvy_preguntas_opciones);
                        break;
                    case CategoriaPreguntasEnum::SELECCION_UNICA->value:
                        $srvy_preguntas_opciones = [];
                        foreach ($pregunta['options'] as $index => $opcion) {
                            $srvy_preguntas_opciones[] = [
                                'id_pregunta' => $srvy_pregunta->id,
                                'opcion' => $opcion,
                                'orden_inicial' => $index
                            ];
                        }
                        $srvy_pregunta->preguntasOpciones()->createMany($srvy_preguntas_opciones);
                        break;
                    case CategoriaPreguntasEnum::ORDENAMIENTO->value:
                        $srvy_preguntas_opciones = [];
                        foreach ($pregunta['options'] as $index => $opcion) {
                            $srvy_preguntas_opciones[] = [
                                'id_pregunta' => $srvy_pregunta->id,
                                'opcion' => $opcion,
                                'orden_inicial' => $index
                            ];
                        }
                        $srvy_pregunta->preguntasOpciones()->createMany($srvy_preguntas_opciones);
                        break;
                    case CategoriaPreguntasEnum::ESCALA_NUMERICA->value:
                        $srvy_preguntas_escala_numerica = [
                            'id_pregunta' => $srvy_pregunta->id,
                            'min_val' => $pregunta['rangeFrom'],
                            'max_val' => $pregunta['rangeTo'],
                        ];
                        $srvy_pregunta->preguntasEscalasNumericas()->create($srvy_preguntas_escala_numerica);
                        break;
                    case CategoriaPreguntasEnum::ESCALA_LIKERT->value:
                        $srvy_preguntas_opciones = [];
                        foreach ($pregunta['options'] as $index => $opcion) {
                            $srvy_preguntas_opciones[] = [
                                'id_pregunta' => $srvy_pregunta->id,
                                'opcion' => $opcion,
                                'orden_inicial' => $index
                            ];
                        }
                        $srvy_pregunta->preguntasOpciones()->createMany($srvy_preguntas_opciones);
                        break;
                    case CategoriaPreguntasEnum::FALSO_VERDADERO->value:
                        $srvy_preguntas_texto_booleano = [
                            'id_pregunta' => $srvy_pregunta->id,
                            'false_txt' => $pregunta['options'][1] ?? 'Falso',
                            'true_txt' => $pregunta['options'][0] ?? 'Verdadero',
                        ];
                        $srvy_pregunta->preguntasTextosBooleanos()->create($srvy_preguntas_texto_booleano);
                        break;
                    default:
                        break;
                }
            }
            DB::commit();
            $encuesta->load(['preguntas.preguntasOpciones', 'preguntas.preguntasTextosBooleanos', 'preguntas.preguntasEscalasNumericas']);
            return $this->success('Formulario de la encuesta actualizado exitosamente', $encuesta, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al actualizar el formulario de la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function publishSurvey(Request $request, $id)
    {
        try {
            $encuesta = Encuesta::find($id);
            if (!$encuesta) {
                return $this->error('Encuesta no encontrada', 'No se encontró la encuesta con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            if ($encuesta->id_usuario !== auth('api')->user()->id) {
                return $this->error('No tienes permiso para realizar esta acción', 'Acceso denegado', Response::HTTP_FORBIDDEN);
            }
            if ($encuesta->id_estado === EstadosEnum::ACTIVO->value) {
                return $this->error('La encuesta ya se encuentra publicada', 'Encuesta ya publicada', Response::HTTP_FORBIDDEN);
            }
            if ($encuesta->preguntas->isEmpty()) {
                return $this->error('La encuesta no tiene ninguna pregunta configurada', 'No puedes publicar una encuesta sin preguntas', Response::HTTP_FORBIDDEN);
            }
            if ($encuesta->id_grupo_meta === null) {
                return $this->error('La encuesta no tiene un grupo meta asignado', 'No puedes publicar una encuesta sin grupo meta', Response::HTTP_FORBIDDEN);
            }
            DB::beginTransaction();
            $encuesta->update([
                'id_estado' => EstadosEnum::ACTIVO->value,
                'fecha_publicacion' => now(),
            ]);
            DB::commit();
            return $this->success('Encuesta publicada exitosamente', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al publicar la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showSurveyToAnswer(Request $request, $codigo)
    {
        try {
            $encuesta = Encuesta::where('codigo', $codigo)->first();
            if (!$encuesta) {
                return $this->error('Encuesta no encontrada', 'No se encontró la encuesta con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            if ($encuesta->id_estado !== EstadosEnum::ACTIVO->value) {
                return $this->error('La encuesta no está disponible para responder', 'Encuesta no disponible', Response::HTTP_FORBIDDEN);
            }
            $formulario = $encuesta->preguntas()->with(['preguntasOpciones', 'preguntasTextosBooleanos', 'preguntasEscalasNumericas'])->get();
            foreach ($formulario as $pregunta) {
                $optionsList = [];
                if ($pregunta->categoriaPregunta->codigo === CategoriaPreguntasEnum::FALSO_VERDADERO->value) {
                    $optionsList = [
                        [
                            'id' => 1,
                            'opcion' => $pregunta->preguntasTextosBooleanos->true_txt,
                        ],
                        [
                            'id' => 2,
                            'opcion' => $pregunta->preguntasTextosBooleanos->false_txt,
                        ]
                    ];
                } else {
                    $optionsList = $pregunta->preguntasOpciones->map(function ($opcion) {
                        return [
                            'id' => $opcion->id,
                            'opcion' => $opcion->opcion,
                        ];
                    });
                }
                $formularioResponse[] = [
                    'idPregunta' => $pregunta->id,
                    'nombre' => $pregunta->categoriaPregunta->nombre,
                    'type' => $pregunta->categoriaPregunta->codigo,
                    'shortQuestion' => $pregunta->descripcion,
                    'allowOtherOption' => $pregunta->es_abierta,
                    'options' => $optionsList,
                    'rangeFrom' => $pregunta->preguntasEscalasNumericas->first()?->min_val ?? 0,
                    'rangeTo' => $pregunta->preguntasEscalasNumericas->first()?->max_val ?? 0,
                ];
            }
            $response = [
                'idEncuesta' => $encuesta->id,
                'titulo' => $encuesta->titulo,
                'descripcion' => $encuesta->descripcion,
                'createdBy' => $encuesta->user->persona->nombre . ' ' . $encuesta->user->persona->apellido,
                'formulario' => $formularioResponse,
            ];
            return $this->success('Formulario completo de la encuesta obtenido exitosamente', $response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error('Error al obtener el formulario completo de la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function answerSurvey(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'codigo' => 'required|string|max:255|exists:srvy_encuestas,codigo',
                // Datos del encuestado
                'encuestado' => 'array|required',
                'encuestado.nombres' => 'string|max:50|regex:/^[a-zA-Z\sáéíóúñÁÉÍÓÚÑ]+$/',
                'encuestado.apellidos' => 'string|max:50|regex:/^[a-zA-Z\sáéíóúñÁÉÍÓÚÑ]+$/',
                'encuestado.email' => 'string|email|max:50',
                'encuestado.fecha_nacimiento' => 'date_format:Y-m-d',
                'encuestado.telefono' => 'string|max:20|regex:/^[0-9\s]+$/',
                'encuestado.edad' => 'integer|min:0|max:120',
                // Respuestas
                'respuestas' => 'array|required',
                'respuestas.*.idPregunta' => 'required|integer|exists:srvy_preguntas,id',
                'respuestas.*.answer' => 'string|max:255',
                'respuestas.*.options' => 'array',
                'respuestas.*.options.*' => 'integer|exists:srvy_preguntas_opciones,id',
                'respuestas.*.openAnswer' => 'string|max:50',
                'respuestas.*.rangeValue' => 'integer|min:0|max:100',

            ], [
                'codigo.required' => 'El código de la encuesta es obligatorio',
                'codigo.string' => 'El código de la encuesta debe ser una cadena de texto',
                'codigo.max' => 'El código de la encuesta no puede exceder los 255 caracteres',
                'codigo.exists' => 'No existe una encuesta con el código proporcionado',
                // Datos del encuestado
                'encuestado.nombres.regex' => 'Caracteres no válidos en el nombre',
                'encuestado.nombres.max' => 'El nombre no puede exceder los 50 caracteres',
                'encuestado.apellidos.regex' => 'Caracteres no válidos en el apellido',
                'encuestado.apellidos.max' => 'El apellido no puede exceder los 50 caracteres',
                'encuestado.email.email' => 'El email no es válido',
                'encuestado.email.max' => 'El email no puede exceder los 50 caracteres',
                'encuestado.fecha_nacimiento.date_format' => 'La fecha de nacimiento no tiene un formato válido',
                'encuestado.telefono.regex' => 'Caracteres no válidos en el teléfono',
                'encuestado.telefono.max' => 'El teléfono no puede exceder los 20 caracteres',
                'encuestado.edad.integer' => 'La edad debe ser un número entero',
                'encuestado.edad.min' => 'La edad debe ser mayor que 0',
                'encuestado.edad.max' => 'Enserio tienes mas de 120 años?',
                // Respuestas
                'respuestas.required' => 'No se ha enviado ninguna respuesta',
                'respuestas.array' => 'Las respuestas deben ser un arreglo',
                'respuestas.*.idPregunta.required' => 'El ID de la pregunta es obligatorio',
                'respuestas.*.idPregunta.integer' => 'El ID de la pregunta debe ser un número entero',
                'respuestas.*.idPregunta.exists' => 'El ID de la pregunta no es válido',
                'respuestas.*.answer.string' => 'La respuesta debe ser una cadena de texto',
                'respuestas.*.answer.max' => 'La respuesta no puede exceder los 255 caracteres',
                'respuestas.*.options.array' => 'Las respuestas de selección deben manejarse como un arreglo',
                'respuestas.*.options.*.integer' => 'Las respuestas de selección deben ser un arreglo de números enteros',
                'respuestas.*.options.*.exists' => 'Una de las opciones seleccionadas no es válida',
                'respuestas.*.openAnswer.string' => 'La respuesta abierta debe ser una cadena de texto',
                'respuestas.*.openAnswer.max' => 'La respuesta abierta no puede exceder los 255 caracteres',
                'respuestas.*.rangeValue.integer' => 'El valor de rango debe ser un número entero',
                'respuestas.*.rangeValue.min' => 'El valor de rango debe ser mayor o igual a 0',
                'respuestas.*.rangeValue.max' => 'El valor de rango debe ser menor o igual a 100',
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedData = $validator->validated();

            $encuesta = Encuesta::where('codigo', $request->input('codigo'))->first();

            // Verificar si el encuestado ya ha respondido la encuesta
            $existeEncuestado = EncuestaRespuesta::where('id_encuesta', $encuesta->id)
                ->whereHas('encuestado', function ($query) use ($validatedData) {
                    $query->where('correo', $validatedData['encuestado']['email']);
                })
                ->exists();
            if ($existeEncuestado) {
                return $this->error('Ya has respondido esta encuesta', 'No puedes volver a responder', Response::HTTP_FORBIDDEN);
            }

            $encuesta = Encuesta::where('codigo', $validatedData['codigo'])->first();
            if (!$encuesta) {
                return $this->error('Encuesta no encontrada', 'No se encontró la encuesta con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }

            if ($encuesta->id_estado !== EstadosEnum::ACTIVO->value) {
                return $this->error('La encuesta no está disponible para responder', 'Encuesta no disponible', Response::HTTP_FORBIDDEN);
            }

            // Verificar si es el encuestador
            // if ($encuesta->id_usuario === auth('api')->user()->id) {
            //     return $this->error('No puedes responder tu propia encuesta', 'Encuesta no disponible', Response::HTTP_FORBIDDEN);
            // }

            DB::beginTransaction();

            // Guardar los datos del encuestado
            $encuestado = Encuestado::create([
                'nombres' => $validatedData['encuestado']['nombres'] ?? null,
                'apellidos' => $validatedData['encuestado']['apellidos'] ?? null,
                'correo' => $validatedData['encuestado']['email'] ?? null,
                'fecha_nacimiento' => $validatedData['encuestado']['fecha_nacimiento'] ?? null,
                'telefono' => $validatedData['encuestado']['telefono'] ?? null,
                'edad' => $validatedData['encuestado']['edad'] ?? null,
            ]);

            // Registrar la respuesta de la encuesta
            $encuestaRespuesta = EncuestaRespuesta::create([
                'id_encuesta' => $encuesta->id,
                'id_encuestado' => $encuestado->id,
            ]);

            // Guardar las respuestas
            foreach ($validatedData['respuestas'] as $respuesta) {

                $pregunta = Pregunta::where('id', $respuesta['idPregunta'])
                    ->where('id_encuesta', $encuesta->id)
                    ->first();
                if (!$pregunta) {
                    return $this->error('La pregunta no existe o no pertenece a esta encuesta X(' . $respuesta['idPregunta'] . ')', 'No se encontró la pregunta con el ID proporcionado', Response::HTTP_NOT_FOUND);
                }
                $preguntaType = $pregunta->categoriaPregunta->codigo;
                $preguntaIsAbierta = $pregunta->es_abierta;
                $preguntaId = $pregunta->id;

                switch ($preguntaType) {
                    case CategoriaPreguntasEnum::TEXTO_CORTO->value:
                        if (strlen($respuesta['answer']) > 50) {
                            return $this->error('La respuesta corta no puede exceder los 50 caracteres P(' . $preguntaId . ')', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        RespuestaPregunta::create([
                            'id_encuesta_respuesta' => $encuestaRespuesta->id,
                            'id_pregunta' => $preguntaId,
                            'respuesta_abierta' => $respuesta['answer'] ?? '',
                        ]);

                        break;
                    case CategoriaPreguntasEnum::TEXTO_LARGO->value:
                        if (strlen($respuesta['answer']) > 255) {
                            return $this->error('La respuesta larga P(' . $preguntaId . ') no puede exceder los 255 caracteres', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        RespuestaPregunta::create([
                            'id_encuesta_respuesta' => $encuestaRespuesta->id,
                            'id_pregunta' => $preguntaId,
                            'respuesta_abierta' => $respuesta['answer'] ?? '',
                        ]);

                        break;
                    case CategoriaPreguntasEnum::SELECCION_MULTIPLE->value:
                        if (!isset($respuesta['options']) && !$preguntaIsAbierta) {
                            return $this->error('La selección de respuestas P(' . $preguntaId . ') no puede estar vacía', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        if (!isset($respuesta['options']) && $preguntaIsAbierta && !isset($respuesta['openAnswer'])) {
                            return $this->error('Debe indicar una respuesta para P(' . $preguntaId . ')', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        if (isset($respuesta['options'])) {
                            if (count($respuesta['options']) > 15) {
                                return $this->error('La respuesta P(' . $preguntaId . ') no puede exceder las 15 opciones', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                            if (count($respuesta['options']) < 1) {
                                return $this->error('Debe seleccionar al menos una opción en las preguntas cerradas P(' . $preguntaId . ')', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                            $arrayIds = $pregunta->preguntasOpciones->pluck('id')->toArray();
                            $idsSeleccionados = array_intersect($respuesta['options'], $arrayIds);
                            if (count($idsSeleccionados) !== count($respuesta['options'])) {
                                return $this->error('Una de las opciones seleccionadas no es válida P(' . $preguntaId . ')', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                        }
                        $respPregunta = RespuestaPregunta::create([
                            'id_encuesta_respuesta' => $encuestaRespuesta->id,
                            'id_pregunta' => $preguntaId,
                            'respuesta_abierta' => $preguntaIsAbierta && isset($respuesta['openAnswer']) ? $respuesta['openAnswer'] : '',
                            'es_abierta' => $preguntaIsAbierta && isset($respuesta['openAnswer']),
                        ]);
                        if (!$preguntaIsAbierta) {
                            foreach ($respuesta['options'] as $index => $opcionId) {
                                $opcionId = (int)$opcionId;
                                OpcionSeleccionada::create([
                                    'id_pregunta_opcion' => $opcionId,
                                    'id_respuesta_pregunta' => $respPregunta->id,
                                    'orden_final' => $index,
                                ]);
                            }
                        }

                        break;
                    case CategoriaPreguntasEnum::SELECCION_UNICA->value:
                        if (!isset($respuesta['options']) && !$preguntaIsAbierta) {
                            return $this->error('La selección de respuestas P(' . $preguntaId . ') no puede estar vacía', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        if (!isset($respuesta['options']) && $preguntaIsAbierta && !isset($respuesta['openAnswer'])) {
                            return $this->error('Debe indicar una respuesta para P(' . $preguntaId . ')', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        if (isset($respuesta['options'])) {
                            if (!isset($respuesta['options'])) {
                                return $this->error('La respuesta P(' . $preguntaId . ') no puede estar vacía', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                            if (count($respuesta['options']) !== 1) {
                                return $this->error('La respuesta P(' . $preguntaId . ') debe tener una sola opción seleccionada', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                            $arrayIds = $pregunta->preguntasOpciones->pluck('id')->toArray();
                            $idsSeleccionados = array_intersect($respuesta['options'], $arrayIds);
                            if (count($idsSeleccionados) !== count($respuesta['options'])) {
                                return $this->error('Una de las opciones seleccionadas no es válida P(' . $preguntaId . ')', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                        }
                        $respPregunta = RespuestaPregunta::create([
                            'id_encuesta_respuesta' => $encuestaRespuesta->id,
                            'id_pregunta' => $preguntaId,
                            'respuesta_abierta' => $preguntaIsAbierta && isset($respuesta['openAnswer']) ? $respuesta['openAnswer'] : '',
                            'es_abierta' => $preguntaIsAbierta && isset($respuesta['openAnswer']),
                        ]);

                        if (!$preguntaIsAbierta && !isset($respuesta['openAnswer'])) {
                            foreach ($respuesta['options'] as $index => $opcionId) {
                                $opcionId = (int)$opcionId;
                                OpcionSeleccionada::create([
                                    'id_pregunta_opcion' => $opcionId,
                                    'id_respuesta_pregunta' => $respPregunta->id,
                                    'orden_final' => $index,
                                ]);
                            }
                        }

                        break;
                    case CategoriaPreguntasEnum::ORDENAMIENTO->value:
                        if (!isset($respuesta['options'])) {
                            return $this->error('La respuesta P(' . $preguntaId . ') no puede estar vacía', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        $arrayIds = $pregunta->preguntasOpciones->pluck('id')->toArray();
                        $idsSeleccionados = array_intersect($respuesta['options'], $arrayIds);
                        if (count($idsSeleccionados) !== count($respuesta['options'])) {
                            return $this->error('No se han seleccionado las opciones disponibles para ordenar en esta pregunta P(' . $preguntaId . ')', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        $respPregunta = RespuestaPregunta::create([
                            'id_encuesta_respuesta' => $encuestaRespuesta->id,
                            'id_pregunta' => $preguntaId,
                        ]);

                        foreach ($respuesta['options'] as $index => $opcionId) {
                            $opcionId = (int)$opcionId;
                            OpcionSeleccionada::create([
                                'id_pregunta_opcion' => $opcionId,
                                'id_respuesta_pregunta' => $respPregunta->id,
                                'orden_final' => $index,
                            ]);
                        }
                        break;
                    case CategoriaPreguntasEnum::ESCALA_NUMERICA->value:
                        if ($respuesta['rangeValue'] < $pregunta->preguntasEscalasNumericas->first()->min_val || $respuesta['rangeValue'] > $pregunta->preguntasEscalasNumericas->first()->max_val) {
                            return $this->error('El valor de la escala numérica no está dentro del rango indicado P(' . $preguntaId . ')', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        $respPregunta = RespuestaPregunta::create([
                            'id_encuesta_respuesta' => $encuestaRespuesta->id,
                            'id_pregunta' => $preguntaId,
                            'respuesta_numero' => $respuesta['rangeValue'] ?? 0,
                        ]);
                        break;
                    case CategoriaPreguntasEnum::ESCALA_LIKERT->value:
                        if (!isset($respuesta['options'])) {
                            return $this->error('La respuesta P(' . $preguntaId . ') no puede estar vacía', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        if (count($respuesta['options']) !== 1) {
                            return $this->error('La respuesta P(' . $preguntaId . ') debe tener una sola opción seleccionada', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        $arrayIds = $pregunta->preguntasOpciones->pluck('id')->toArray();
                        $idsSeleccionados = array_intersect($respuesta['options'], $arrayIds);
                        if (count($idsSeleccionados) !== count($respuesta['options'])) {
                            return $this->error('Una de las opciones seleccionadas no es válida P(' . $preguntaId . ')', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        $respPregunta = RespuestaPregunta::create([
                            'id_encuesta_respuesta' => $encuestaRespuesta->id,
                            'id_pregunta' => $preguntaId,
                        ]);

                        foreach ($respuesta['options'] as $index => $opcionId) {
                            $opcionId = (int)$opcionId;
                            OpcionSeleccionada::create([
                                'id_pregunta_opcion' => $opcionId,
                                'id_respuesta_pregunta' => $respPregunta->id,
                                'orden_final' => $index,
                            ]);
                        }
                        break;
                    case CategoriaPreguntasEnum::FALSO_VERDADERO->value:
                        if (!isset($respuesta['options'])) {
                            return $this->error('La respuesta P(' . $preguntaId . ') no puede estar vacía', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        if (count($respuesta['options']) !== 1) {
                            return $this->error('La respuesta P(' . $preguntaId . ') debe tener una sola opción seleccionada', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        $arrayIds = [1, 2]; // IDs de las opciones de verdadero y falso, en ese orden
                        $idsSeleccionados = array_intersect($respuesta['options'], $arrayIds);
                        if (count($idsSeleccionados) !== count($respuesta['options'])) {
                            return $this->error('La opción seleccionada no es válida P(' . $preguntaId . ')', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        $respPregunta = RespuestaPregunta::create([
                            'id_encuesta_respuesta' => $encuestaRespuesta->id,
                            'id_pregunta' => $preguntaId,
                            'respuesta_booleana' => $respuesta['options'][0] === 1 ? true : false,
                        ]);
                        break;
                    default:
                        return $this->error('La categoria de la pregunta no es válida', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        break;
                }
            }
            DB::commit();
            return $this->success('Tu respuesta ha sido guardada', 'Gracias :D', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al responder la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function surveyStatistics(Request $request, $id)
    {
        try {
            $encuesta = Encuesta::find($id);
            if (!$encuesta) {
                return $this->error('Encuesta no encontrada', 'No se encontró la encuesta con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            if ($encuesta->id_usuario !== auth('api')->user()->id) {
                return $this->error('Acceso denegado', 'No tienes permiso para acceder a esta encuesta', Response::HTTP_FORBIDDEN);
            }
            if ($encuesta->id_estado !== EstadosEnum::ACTIVO->value) {
                return $this->error('La encuesta no está disponible para responder', 'Encuesta no disponible', Response::HTTP_FORBIDDEN);
            }

            $preguntas = $encuesta->preguntas()->with(['preguntasOpciones', 'preguntasTextosBooleanos', 'preguntasEscalasNumericas'])->get();
            $respuestas = $encuesta->encuestasRespuestas()->with(['respuestasPreguntas', 'respuestasPreguntas.opcionesSeleccionadas'])->get();
            $formGeneralInfo = [
                'idEncuesta' => $encuesta->id,
                'titulo' => $encuesta->titulo,
                'descripcion' => $encuesta->descripcion,
                'createdBy' => $encuesta->user->persona->nombre . ' ' . $encuesta->user->persona->apellido,
                'totalRespuestas' => $respuestas->count(),
            ];

            /**
             * El formato de visualizacion variará dependiendo del tipo de pregunta
             * 1. Pregunta de texto corto / largo: array de respuestas de texto
             * 2. Pregunta de selección múltiple / única: objecto para graficos (labels, dataset) para mostrar la cantidad de respuestas por cada opción
             *    Si la pregunta es abierta, se añadirá también un array de respuestas de texto
             * 3. Pregunta de ordenamiento: dataset para crear un stacked bar chart
             * 4. Pregunta de escala numérica: dataset para crear graficos con los valores mas frecuentes
             * 5. Pregunta de escala Likert: dataset para crear graficos con los valores mas frecuentes
             * 6. Pregunta de verdadero/falso: dataset para crear graficos con los valores mas frecuentes
             */
            $formStats = [];
            // Obtener datos de cada pregunta
            foreach ($preguntas as $pregunta) {
                $preguntaType = $pregunta->categoriaPregunta->codigo;
                $preguntaIsAbierta = $pregunta->es_abierta;
                $preguntaId = $pregunta->id;

                switch ($preguntaType) {
                    case CategoriaPreguntasEnum::TEXTO_CORTO->value:
                        $respuestasTextoCorto = $respuestas->map(function ($respuesta) use ($preguntaId) {
                            return $respuesta->respuestasPreguntas->where('id_pregunta', $preguntaId)->first()?->respuesta_abierta;
                        })->filter()->values()->all();
                        $formStats[] = [
                            'idPregunta' => $preguntaId,
                            'nombre' => $pregunta->categoriaPregunta->nombre,
                            'type' => $preguntaType,
                            'shortQuestion' => $pregunta->descripcion,
                            'allowOtherOption' => $preguntaIsAbierta,
                            'textResults' => $respuestasTextoCorto,
                            'graphData' => null,
                        ];
                        break;
                    case CategoriaPreguntasEnum::TEXTO_LARGO->value:
                        $respuestasTextoLargo = $respuestas->map(function ($respuesta) use ($preguntaId) {
                            return $respuesta->respuestasPreguntas->where('id_pregunta', $preguntaId)->first()?->respuesta_abierta;
                        })->filter()->values()->all();
                        $formStats[] = [
                            'idPregunta' => $preguntaId,
                            'nombre' => $pregunta->categoriaPregunta->nombre,
                            'type' => $preguntaType,
                            'shortQuestion' => $pregunta->descripcion,
                            'allowOtherOption' => $preguntaIsAbierta,
                            'textResults' => $respuestasTextoLargo,
                            'graphData' => null,
                        ];
                        break;
                    case CategoriaPreguntasEnum::SELECCION_MULTIPLE->value:
                        $respuestasSeleccionMultiple = $respuestas->map(function ($respuesta) use ($preguntaId) {
                            return $respuesta->respuestasPreguntas->where('id_pregunta', $preguntaId)->first()?->opcionesSeleccionadas;
                        })->filter()->values()->all();
                        $respuestasAbiertas = [];
                        if ($preguntaIsAbierta) {
                            $respuestasAbiertas = $respuestas->map(function ($respuesta) use ($preguntaId) {
                                return $respuesta->respuestasPreguntas->where('id_pregunta', $preguntaId)->first()?->respuesta_abierta;
                            })->filter()->values()->all();
                        }
                        $opciones = $pregunta->preguntasOpciones->map(function ($opcion) use ($respuestasSeleccionMultiple) {
                            $cantidad = 0;
                            foreach ($respuestasSeleccionMultiple as $respuestaSeleccionada) {
                                if ($respuestaSeleccionada->contains('id_pregunta_opcion', $opcion->id)) {
                                    $cantidad++;
                                }
                            }
                            return [
                                'id' => $opcion->id,
                                'opcion' => $opcion->opcion,
                                'cantidad' => $cantidad,
                            ];
                        });
                        $formStats[] = [
                            'idPregunta' => $preguntaId,
                            'nombre' => $pregunta->categoriaPregunta->nombre,
                            'type' => $preguntaType,
                            'shortQuestion' => $pregunta->descripcion,
                            'allowOtherOption' => $preguntaIsAbierta,
                            'textResults' => $respuestasAbiertas,
                            'graphData' => [
                                'labels' => $opciones->pluck('opcion')->toArray(),
                                'datasets' => [
                                    [
                                        'label' => 'Cantidad de respuestas',
                                        'data' => $opciones->pluck('cantidad')->toArray(),
                                    ],
                                ],
                            ],
                        ];
                        break;
                    case CategoriaPreguntasEnum::SELECCION_UNICA->value:
                        $respuestasSeleccionUnica = $respuestas->map(function ($respuesta) use ($preguntaId) {
                            return $respuesta->respuestasPreguntas->where('id_pregunta', $preguntaId)->first()?->opcionesSeleccionadas;
                        })->filter()->values()->all();
                        $respuestasAbiertas = [];
                        if ($preguntaIsAbierta) {
                            $respuestasAbiertas = $respuestas->map(function ($respuesta) use ($preguntaId) {
                                return $respuesta->respuestasPreguntas->where('id_pregunta', $preguntaId)->first()?->respuesta_abierta;
                            })->filter()->values()->all();
                        }
                        $opciones = $pregunta->preguntasOpciones->map(function ($opcion) use ($respuestasSeleccionUnica) {
                            $cantidad = 0;
                            foreach ($respuestasSeleccionUnica as $respuestaSeleccionada) {
                                if ($respuestaSeleccionada->contains('id_pregunta_opcion', $opcion->id)) {
                                    $cantidad++;
                                }
                            }
                            return [
                                'id' => $opcion->id,
                                'opcion' => $opcion->opcion,
                                'cantidad' => $cantidad,
                            ];
                        });
                        $formStats[] = [
                            'idPregunta' => $preguntaId,
                            'nombre' => $pregunta->categoriaPregunta->nombre,
                            'type' => $preguntaType,
                            'shortQuestion' => $pregunta->descripcion,
                            'allowOtherOption' => $preguntaIsAbierta,
                            'textResults' => $respuestasAbiertas,
                            'graphData' => [
                                'labels' => $opciones->pluck('opcion')->toArray(),
                                'datasets' => [
                                    [
                                        'label' => 'Cantidad de respuestas',
                                        'data' => $opciones->pluck('cantidad')->toArray(),
                                    ],
                                ],
                            ],
                        ];
                        break;
                    case CategoriaPreguntasEnum::ORDENAMIENTO->value:
                        $respuestasOrdenamiento = $respuestas->map(function ($respuesta) use ($preguntaId) {
                            return $respuesta->respuestasPreguntas->where('id_pregunta', $preguntaId)->first()?->opcionesSeleccionadas;
                        })->filter()->values()->all();
                        $cantidadOpciones = $pregunta->preguntasOpciones->count();
                        $arrayCantidadOpciones = range(0, $cantidadOpciones - 1);
                        $datasets = [];
                        $labels = [];
                        foreach ($arrayCantidadOpciones as $index => $opcionNo) {
                            $labels[] = "Posición " . $index + 1;
                            $opciones = $pregunta->preguntasOpciones->map(function ($opcion) use ($respuestasOrdenamiento, $index) {
                                $cantidad = 0;
                                foreach ($respuestasOrdenamiento as $respuestaOrdenada) {
                                    foreach ($respuestaOrdenada as $respuesta) {
                                        if (($respuesta->id_pregunta_opcion === $opcion->id) && ($respuesta->orden_final === $index)) {
                                            $cantidad++;
                                        }
                                    }
                                }
                                return [
                                    'id' => $opcion->id,
                                    'opcion' => $opcion->opcion,
                                    'cantidad' => $cantidad,
                                ];
                            });
                            $datasets[] = [
                                'label' => $opciones[$index]['opcion'],
                                'data' => $opciones->pluck('cantidad')->toArray(),
                            ];
                        }
                        $formStats[] = [
                            'idPregunta' => $preguntaId,
                            'nombre' => $pregunta->categoriaPregunta->nombre,
                            'type' => $preguntaType,
                            'shortQuestion' => $pregunta->descripcion,
                            'allowOtherOption' => $preguntaIsAbierta,
                            'textResults' => null,
                            'graphData' => [
                                'labels' => $labels,
                                'datasets' => $datasets,
                            ],
                        ];
                        break;
                    case CategoriaPreguntasEnum::ESCALA_NUMERICA->value:
                        $respuestasEscalaNumerica = $respuestas->map(function ($respuesta) use ($preguntaId) {
                            return $respuesta->respuestasPreguntas->where('id_pregunta', $preguntaId)->first()?->respuesta_numero;
                        })->filter()->values()->all();
                        $valores = [];
                        foreach ($respuestasEscalaNumerica as $respuesta) {
                            if ($respuesta !== null) {
                                $valores[] = $respuesta;
                            }
                        }
                        $frecuencias = array_count_values($valores);
                        $labels = array_keys($frecuencias);
                        $data = array_values($frecuencias);
                        $formStats[] = [
                            'idPregunta' => $preguntaId,
                            'nombre' => $pregunta->categoriaPregunta->nombre,
                            'type' => $preguntaType,
                            'shortQuestion' => $pregunta->descripcion,
                            'allowOtherOption' => $preguntaIsAbierta,
                            'textResults' => null,
                            'graphData' => [
                                'labels' => $labels,
                                'datasets' => [
                                    [
                                        'label' => 'Frecuencia',
                                        'data' => $data,
                                    ],
                                ],
                            ],
                        ];
                        break;
                    case CategoriaPreguntasEnum::ESCALA_LIKERT->value:
                        $respuestasEscalaLikert = $respuestas->map(function ($respuesta) use ($preguntaId) {
                            return $respuesta->respuestasPreguntas->where('id_pregunta', $preguntaId)->first()?->opcionesSeleccionadas;
                        })->filter()->values()->all();
                        $respuestasAbiertas = [];
                        if ($preguntaIsAbierta) {
                            $respuestasAbiertas = $respuestas->map(function ($respuesta) use ($preguntaId) {
                                return $respuesta->respuestasPreguntas->where('id_pregunta', $preguntaId)->first()?->respuesta_abierta;
                            })->filter()->values()->all();
                        }
                        $opciones = $pregunta->preguntasOpciones->map(function ($opcion) use ($respuestasEscalaLikert) {
                            $cantidad = 0;
                            foreach ($respuestasEscalaLikert as $respuestaSeleccionada) {
                                if ($respuestaSeleccionada->contains('id_pregunta_opcion', $opcion->id)) {
                                    $cantidad++;
                                }
                            }
                            return [
                                'id' => $opcion->id,
                                'opcion' => $opcion->opcion,
                                'cantidad' => $cantidad,
                            ];
                        });
                        $formStats[] = [
                            'idPregunta' => $preguntaId,
                            'nombre' => $pregunta->categoriaPregunta->nombre,
                            'type' => $preguntaType,
                            'shortQuestion' => $pregunta->descripcion,
                            'allowOtherOption' => $preguntaIsAbierta,
                            'textResults' => $respuestasAbiertas,
                            'graphData' => [
                                'labels' => $opciones->pluck('opcion')->toArray(),
                                'datasets' => [
                                    [
                                        'label' => 'Cantidad de respuestas',
                                        'data' => $opciones->pluck('cantidad')->toArray(),
                                    ],
                                ],
                            ],
                        ];
                        break;
                    case CategoriaPreguntasEnum::FALSO_VERDADERO->value:
                        $respuestasFalsoVerdadero = $respuestas->map(function ($respuesta) use ($preguntaId) {
                            return $respuesta->respuestasPreguntas->where('id_pregunta', $preguntaId)->first()?->respuesta_booleana;
                        })->filter()->values()->all();
                        $respuestasAbiertas = [];
                        if ($preguntaIsAbierta) {
                            $respuestasAbiertas = $respuestas->map(function ($respuesta) use ($preguntaId) {
                                return $respuesta->respuestasPreguntas->where('id_pregunta', $preguntaId)->first()?->respuesta_abierta;
                            })->filter()->values()->all();
                        }
                        $opciones = [
                            [
                                'id' => 1,
                                'opcion' => 'Verdadero',
                                'cantidad' => 0,
                            ],
                            [
                                'id' => 2,
                                'opcion' => 'Falso',
                                'cantidad' => 0,
                            ],
                        ];
                        foreach ($respuestasFalsoVerdadero as $respuesta) {
                            if ($respuesta) {
                                $opciones[0]['cantidad']++;
                            } else {
                                $opciones[1]['cantidad']++;
                            }
                        }
                        $formStats[] = [
                            'idPregunta' => $preguntaId,
                            'nombre' => $pregunta->categoriaPregunta->nombre,
                            'type' => $preguntaType,
                            'shortQuestion' => $pregunta->descripcion,
                            'allowOtherOption' => $preguntaIsAbierta,
                            'textResults' => $respuestasAbiertas,
                            'graphData' => [
                                'labels' => array_column($opciones, 'opcion'),
                                'datasets' => [
                                    [
                                        'label' => 'Cantidad de respuestas',
                                        'data' => array_column($opciones, 'cantidad'),
                                    ],
                                ],
                            ],
                        ];
                        break;
                    default:
                        return $this->error('La categoria de la pregunta no es válida', 'Error de validación', Response::HTTP_UNPROCESSABLE_ENTITY);
                        break;
                }
            }

            $response = [
                'formGeneralInfo' => $formGeneralInfo,
                'formStats' => $formStats,
            ];

            return $this->success('Estadísticas de la encuesta obtenidas exitosamente', $response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error('Error al obtener las estadísticas de la encuesta', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
