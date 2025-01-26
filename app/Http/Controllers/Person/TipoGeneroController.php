<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Models\Salomon\TipoGenero;
use Illuminate\Http\Request;
use Exception;

class TipoGeneroController extends Controller
{
    /**
     * Listar todos los tipos de género
     */
    public function index()
    {
        try {
            $tiposGenero = TipoGenero::activos()->ordenado()->get();

            return $this->successResponse($tiposGenero, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener los tipos de género: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Mostrar un tipo de género específico
     */
    public function show($id)
    {
        try {
            $tipoGenero = TipoGenero::findOrFail($id);

            return $this->successResponse($tipoGenero, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener el tipo de género: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Crear un nuevo tipo de género
     */
    public function store(Request $request)
    {
        dd($request);
        try {
            $validado = $request->validate([
                'nombre' => 'required|string|max:255',
                'sigla' => 'nullable|string|max:10',
                'orden' => 'nullable|integer',
            ]);

            $tipoGenero = TipoGenero::create($validado);

            return $this->successResponse($tipoGenero, 201);
        } catch (Exception $e) {
            return $this->errorResponse('Error al crear el tipo de género: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar un tipo de género
     */
    public function update(Request $request, $id)
    {
        try {
            $tipoGenero = TipoGenero::findOrFail($id);

            $validado = $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'sigla' => 'nullable|string|max:10',
                'orden' => 'nullable|integer',
            ]);

            $tipoGenero->update($validado);

            return $this->successResponse($tipoGenero, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Error al actualizar el tipo de género: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Eliminar un tipo de género
     */
    public function destroy($id)
    {
        try {
            $tipoGenero = TipoGenero::findOrFail($id);
            $tipoGenero->delete();

            return $this->successResponse(null, 204);
        } catch (Exception $e) {
            return $this->errorResponse('Error al eliminar el tipo de género: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Listar tipos de género para un selector
     */
    public function selector()
    {
        try {
            $tiposGenero = TipoGenero::activos()
                                     ->ordenado()
                                     ->get(['id_tipo_genero', 'nombre', 'sigla']);

            return $this->successResponse($tiposGenero, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener los tipos de género: ' . $e->getMessage(), 500);
        }
    }
}
