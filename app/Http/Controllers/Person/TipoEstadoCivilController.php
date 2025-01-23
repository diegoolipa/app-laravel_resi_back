<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Models\Salomon\TipoEstadoCivil;
use Illuminate\Http\Request;
use Exception;

class TipoEstadoCivilController extends Controller
{
    /**
     * Listar tipos de estado civil
     */
    public function index(Request $request)
    {
        try {
            $query = TipoEstadoCivil::activos();

            // Opcional: añadir búsqueda
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('nombre', 'ilike', "%{$search}%");
            }

            $tiposEstadoCivil = $query->get();

            return $this->successResponse($tiposEstadoCivil, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Error al listar tipos de estado civil: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Selector de tipos de estado civil
     */
    public function selector()
    {
        try {
            $tiposEstadoCivil = TipoEstadoCivil::activos()
                                               ->get(['id_tipo_estado_civil', 'nombre', 'sigla']);

            return $this->successResponse($tiposEstadoCivil, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener los tipos de estado civil: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Mostrar un tipo de estado civil específico
     */
    public function show($id)
    {
        try {
            $tipoEstadoCivil = TipoEstadoCivil::findOrFail($id);

            return $this->successResponse($tipoEstadoCivil, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener el tipo de estado civil: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Crear un nuevo tipo de estado civil
     */
    public function store(Request $request)
    {
        try {
            $validado = $request->validate([
                'nombre' => 'required|string|max:100|unique:salomon.tipo_estado_civiles,nombre',
                'sigla' => 'nullable|string|max:10|unique:salomon.tipo_estado_civiles,sigla',
                'estado' => 'nullable|boolean'
            ]);

            // Por defecto, el estado será activo si no se especifica
            $validado['estado'] = $validado['estado'] ?? true;

            $tipoEstadoCivil = TipoEstadoCivil::create($validado);

            return $this->successResponse($tipoEstadoCivil, 201);
        } catch (Exception $e) {
            return $this->errorResponse('Error al crear el tipo de estado civil: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar un tipo de estado civil
     */
    public function update(Request $request, $id)
    {
        try {
            $tipoEstadoCivil = TipoEstadoCivil::findOrFail($id);

            $validado = $request->validate([
                'nombre' => 'sometimes|required|string|max:100|unique:salomon.tipo_estado_civiles,nombre,' . $id . ',id_tipo_estado_civil',
                'sigla' => 'nullable|string|max:10|unique:salomon.tipo_estado_civiles,sigla,' . $id . ',id_tipo_estado_civil',
                'estado' => 'nullable|boolean'
            ]);

            $tipoEstadoCivil->update($validado);

            return $this->successResponse($tipoEstadoCivil, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Error al actualizar el tipo de estado civil: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Eliminar un tipo de estado civil
     */
    public function destroy($id)
    {
        try {
            $tipoEstadoCivil = TipoEstadoCivil::findOrFail($id);

            // Verificar si hay personas naturales asociadas
            if ($tipoEstadoCivil->personasNaturales()->exists()) {
                return $this->errorResponse('No se puede eliminar. Existen personas naturales asociadas a este estado civil.', 400);
            }

            $tipoEstadoCivil->delete();

            return $this->successResponse(null, 204);
        } catch (Exception $e) {
            return $this->errorResponse('Error al eliminar el tipo de estado civil: ' . $e->getMessage(), 500);
        }
    }
}
