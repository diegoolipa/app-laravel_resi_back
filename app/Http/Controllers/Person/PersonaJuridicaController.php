<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Models\Salomon\PersonaJuridica;
use App\Models\Salomon\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PersonaJuridicaController extends Controller
{
   /**
    * Listar personas jurídicas
    */
   public function index(Request $request)
   {
       try {
           $perPage = $request->input('per_page', 10);
           $search = $request->input('search');

           $query = PersonaJuridica::with('persona');

           if ($search) {
               $query->where(function($q) use ($search) {
                   $q->where('razon_social', 'ilike', "%{$search}%")
                     ->orWhere('nombre_comercial', 'ilike', "%{$search}%")
                     ->orWhereHas('persona', function($subQuery) use ($search) {
                         $subQuery->where('documento', 'ilike', "%{$search}%");
                     });
               });
           }

           $personasJuridicas = $query->paginate($perPage);

           return $this->successResponse($personasJuridicas, 200);
       } catch (Exception $e) {
           return $this->errorResponse('Error al listar personas jurídicas: ' . $e->getMessage(), 500);
       }
   }

   /**
    * Crear nueva persona jurídica
    */
    public function procesarPersonaJuridica($validado, $persona)
    {
       try {
           // Buscar persona jurídica existente
           $personaJuridica = PersonaJuridica::where('id_persona', $persona->id_persona)->first();

           if ($personaJuridica) {
               // Actualizar si existe
               $personaJuridica->update([
                   'razon_social' => $validado['razon_social'],
                   'nombre_comercial' => $validado['nombre_comercial'] ?? null,
                   'sitio_web' => $validado['sitio_web'] ?? null
               ]);
           } else {
               // Crear si no existe
               $personaJuridica = PersonaJuridica::create([
                   'id_persona' => $persona->id_persona,
                   'razon_social' => $validado['razon_social'],
                   'nombre_comercial' => $validado['nombre_comercial'] ?? null,
                   'sitio_web' => $validado['sitio_web'] ?? null
               ]);
           }

           return $personaJuridica;
       } catch (Exception $e) {
           throw $e;
       }
    }

    public function store(Request $request)
    {
      try {
          DB::beginTransaction();

          $validado = $request->validate([
              'id_usuario' => 'required|exists:pgsql.users,id',
              'razon_social' => 'required|string|max:255',
              'nombre_comercial' => 'nullable|string|max:255',
              'sitio_web' => 'nullable|url|max:255',
          ]);

          // Buscar persona por id_usuario
          $persona = Persona::where('id_usuario', $validado['id_usuario'])->first();

          if (!$persona) {
              throw new Exception('No se encontró una persona para el usuario especificado');
          }

          $personaJuridica = $this->procesarPersonaJuridica($validado, $persona);

          DB::commit();

          $personaJuridica->load('persona');

          return $this->successResponse($personaJuridica, 201);
      } catch (Exception $e) {
          DB::rollBack();
          return $this->errorResponse('Error al procesar persona jurídica: ' . $e->getMessage(), 500);
      }
    }
   /**
    * Mostrar detalles de persona jurídica
    */
   public function show($id)
   {
       try {
           $personaJuridica = PersonaJuridica::with('persona')
                                             ->findOrFail($id);

           return $this->successResponse($personaJuridica, 200);
       } catch (Exception $e) {
           return $this->errorResponse('Error al obtener persona jurídica: ' . $e->getMessage(), 404);
       }
   }

   /**
    * Actualizar persona jurídica
    */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $validado = $request->validate([
                'razon_social' => 'sometimes|required|string|max:255',
                'nombre_comercial' => 'nullable|string|max:255',
                'sitio_web' => 'nullable|url|max:255',
            ]);

            // Buscar persona jurídica
            $personaJuridica = PersonaJuridica::findOrFail($id);

            // Actualizar datos de persona jurídica
            $personaJuridica->update(collect($validado)->only([
                'razon_social',
                'nombre_comercial',
                'sitio_web'
            ])->toArray());

            DB::commit();

            $personaJuridica->load('persona');

            return $this->successResponse($personaJuridica, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al actualizar persona jurídica: ' . $e->getMessage(), 500);
        }
    }

   /**
    * Eliminar persona jurídica
    */
   public function destroy($id)
   {
       try {
           DB::beginTransaction();

           $personaJuridica = PersonaJuridica::findOrFail($id);

           // Eliminar persona jurídica
           $personaJuridica->delete();

           // Eliminar persona asociada
           $personaJuridica->persona->delete();

           DB::commit();

           return $this->successResponse(null, 204);
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse('Error al eliminar persona jurídica: ' . $e->getMessage(), 500);
       }
   }
}
