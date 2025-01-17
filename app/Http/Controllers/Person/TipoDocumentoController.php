<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Models\Salomon\TipoDocumento;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipoDocumentoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = TipoDocumento::query();

            // Filtro por tipo de persona
            if ($request->has('es_persona_natural')) {
                $query->where('es_persona_natural', $request->boolean('es_persona_natural'));
            }

            $tipoDocumentos = $query->activos()
                ->ordenado()
                ->get();
            return $this->successResponse($tipoDocumentos);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'sigla' => 'required|string|max:10|unique:pgsql.salomon.tipo_documentos,sigla',
            'es_persona_natural' => 'required|boolean',
            'regla' => 'nullable|string|max:250',
            'orden' => 'nullable|integer'
        ]);

        try {
            $userSession = $this->getUserSession();
            $tipoDocumento = TipoDocumento::create($validated);

            return $this->successResponse(
                $tipoDocumento,
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function show($id)
    {
        try {
            $tipoDocumento = TipoDocumento::findOrFail($id);
            return $this->successResponse($tipoDocumento);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'sigla' => 'required|string|max:10|unique:salomon.tipo_documentos,sigla,' . $id . ',id_tipo_documento',
            'es_persona_natural' => 'required|boolean',
            'regla' => 'nullable|string|max:250',
            'orden' => 'nullable|integer'
        ]);

        try {
            $tipoDocumento = TipoDocumento::findOrFail($id);

            $tipoDocumento->update([
                ...$request->validated(),
                'usuario_actualizacion' => Auth::guard('sanctum')->id()
            ]);

            return $this->successResponse(
                $tipoDocumento,
                'Tipo de documento actualizado con éxito'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function destroy($id)
    {
        try {
            $tipoDocumento = TipoDocumento::findOrFail($id);

            // Eliminación lógica
            $tipoDocumento->update([
                'estado' => 0,
                'usuario_actualizacion' => Auth::guard('sanctum')->id()
            ]);

            return $this->successResponse(
                null,
                'Tipo de documento eliminado con éxito'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    // Método adicional para obtener solo tipos de documento de personas naturales
    public function naturales()
    {
        try {
            $documentos = TipoDocumento::personaNatural()
                ->activos()
                ->ordenado()
                ->get();

            return $this->successResponse($documentos);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    // Método adicional para obtener solo tipos de documento de personas jurídicas
    public function juridicos()
    {
        try {
            $documentos = TipoDocumento::personaJuridica()
                ->activos()
                ->ordenado()
                ->get();

            return $this->successResponse($documentos);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
