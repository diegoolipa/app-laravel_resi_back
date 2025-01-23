<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Models\Salomon\PersonaNatural;
use App\Models\Salomon\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PersonaNaturalController extends Controller
{
    /**
     * Listar personas naturales
     */
    public function index(Request $request)
    {
        try {
            // Configurar paginación y búsqueda
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search');

            $query = PersonaNatural::with(['persona', 'genero', 'estadoCivil']);

            // Aplicar búsqueda si existe
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombres', 'ilike', "%{$search}%")
                        ->orWhere('apellido_paterno', 'ilike', "%{$search}%")
                        ->orWhere('apellido_materno', 'ilike', "%{$search}%")
                        ->orWhereHas('persona', function ($subQuery) use ($search) {
                            $subQuery->where('documento', 'ilike', "%{$search}%");
                        });
                });
            }

            // Ordenar y paginar
            $personasNaturales = $query->orderBy('nombres')
                ->paginate($perPage);

            return $this->successResponse($personasNaturales, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Error al listar personas naturales: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Guardar nueva persona natural
     */
    public function procesarPersonaNatural($validado, $persona)
    {
       try {
           // Buscar persona natural existente
           $personaNatural = PersonaNatural::where('id_persona', $persona->id_persona)->first();

           if ($personaNatural) {
               // Actualizar si existe
               $personaNatural->update([
                   'nombres' => $validado['nombres'],
                   'apellido_paterno' => $validado['apellido_paterno'],
                   'apellido_materno' => $validado['apellido_materno'] ?? null,
                   'fecha_nacimiento' => $validado['fecha_nacimiento'] ?? null,
                   'id_tipo_genero' => $validado['id_tipo_genero'],
                   'id_tipo_estado_civil' => $validado['id_tipo_estado_civil'] ?? null,
                   'nombres_completos' => trim("{$validado['nombres']} {$validado['apellido_paterno']} " . ($validado['apellido_materno'] ?? ''))
               ]);
           } else {
               // Crear si no existe
               $personaNatural = PersonaNatural::create([
                   'id_persona' => $persona->id_persona,
                   'nombres' => $validado['nombres'],
                   'apellido_paterno' => $validado['apellido_paterno'],
                   'apellido_materno' => $validado['apellido_materno'] ?? null,
                   'fecha_nacimiento' => $validado['fecha_nacimiento'] ?? null,
                   'id_tipo_genero' => $validado['id_tipo_genero'],
                   'id_tipo_estado_civil' => $validado['id_tipo_estado_civil'] ?? null,
                   'nombres_completos' => trim("{$validado['nombres']} {$validado['apellido_paterno']} " . ($validado['apellido_materno'] ?? ''))
               ]);
           }

           return $personaNatural;
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
               'nombres' => 'required|string|max:100',
               'apellido_paterno' => 'required|string|max:100',
               'apellido_materno' => 'nullable|string|max:100',
               'fecha_nacimiento' => 'nullable|date',
               'id_tipo_genero' => 'required|exists:pgsql.salomon.tipo_generos,id_tipo_genero',
               'id_tipo_estado_civil' => 'nullable|exists:pgsql.salomon.tipo_estado_civiles,id_tipo_estado_civil',
           ]);

           // Buscar persona por id_usuario
           $persona = Persona::where('id_usuario', $validado['id_usuario'])->first();

           if (!$persona) {
               throw new Exception('No se encontró una persona para el usuario especificado');
           }

           $personaNatural = $this->procesarPersonaNatural($validado, $persona);

           DB::commit();

           $personaNatural->load(['persona', 'genero', 'estadoCivil']);

           return $this->successResponse($personaNatural, 201);
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse('Error al procesar persona natural: ' . $e->getMessage(), 500);
       }
    }
    /**
     * Actualizar persona natural
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $validado = $request->all(); // Captura todos los datos recibidos

            $personaNatural = PersonaNatural::findOrFail($id);

            // Actualizar directamente con todos los campos recibidos
            $personaNatural->fill($validado);

            // Generar nombres completos si es necesario
            if (isset($validado['nombres']) ||
                isset($validado['apellido_paterno']) ||
                isset($validado['apellido_materno'])) {
                $personaNatural->nombres_completos = trim(
                    ($validado['nombres'] ?? $personaNatural->nombres) . ' ' .
                    ($validado['apellido_paterno'] ?? $personaNatural->apellido_paterno) . ' ' .
                    ($validado['apellido_materno'] ?? $personaNatural->apellido_materno ?? '')
                );
            }

            $personaNatural->save();

            DB::commit();

            $personaNatural->load(['persona', 'genero', 'estadoCivil']);

            return $this->successResponse($personaNatural, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al actualizar persona natural: ' . $e->getMessage(), 500);
        }
    }
    /**
     * Mostrar detalles de una persona natural
     */
    public function show($id)
    {
        try {
            $personaNatural = PersonaNatural::with(['persona', 'genero', 'estadoCivil'])
                ->findOrFail($id);

            return $this->successResponse($personaNatural, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener detalles de persona natural: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Eliminar persona natural
     */
    public function destroy($id)
    {
        try {
            // Iniciar transacción
            DB::beginTransaction();

            $personaNatural = PersonaNatural::findOrFail($id);

            // Eliminar primero la persona natural
            $personaNatural->delete();

            // Luego eliminar la persona
            $personaNatural->persona->delete();

            // Commit de la transacción
            DB::commit();

            return $this->successResponse(null, 204);
        } catch (Exception $e) {
            // Rollback en caso de error
            DB::rollBack();
            return $this->errorResponse('Error al eliminar persona natural: ' . $e->getMessage(), 500);
        }
    }
}
