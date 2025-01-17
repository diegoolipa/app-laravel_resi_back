<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Http\Requests\Person\PersonaStoreRequest;
use App\Models\Salomon\Persona;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PersonaController extends Controller
{
    public function listar(Request $request)
    {
        try {
            $query = Persona::query();

            if ($request->has('tipo_persona')) {
                $query->where('tipo_persona', $request->tipo_persona);
            }

            if ($request->has('numero_documento')) {
                $query->whereHas('documentos', function ($q) use ($request) {
                    $q->where('numero_documento', $request->numero_documento);
                });
            }

            $personas = $query->with([
                'personaNatural:id_persona,nombres_completos',
                'personaJuridica:id_persona,razon_social',
                'documentos.tipoDocumento',
                'direcciones' => fn($q) => $q->where('es_principal', true),
                'celulars' => fn($q) => $q->where('es_principal', true),
                'correos' => fn($q) => $q->where('es_principal', true)
            ])->paginate(10);

            return $this->successResponse($personas,200);

        } catch (Exception $e) {
            return $this->errorResponse('Error al obtener las personas: ' . $e->getMessage(), 500);
        }
    }

    public function store(PersonaStoreRequest $request)
    {

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            // Crear persona
            $persona = Persona::create([
                'tipo_persona' => $validated['tipo_persona'],
                'id_usuario' => $validated['id_usuario'] ?? null,
                'usuario_creacion' => auth()->id()
            ]);

            // Persona Natural
            if ($validated['tipo_persona'] === 'N') {
                $persona->personaNatural()->create([
                    'nombres' => $validated['nombres'],
                    'apellido_paterno' => $validated['apellido_paterno'],
                    'apellido_materno' => $validated['apellido_materno'] ?? null,
                    'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
                    'id_tipo_genero' => $validated['id_tipo_genero'],
                    'id_tipo_estado_civil' => $validated['id_tipo_estado_civil'],
                    'usuario_creacion' => auth()->id()
                ]);
            }
            // Persona Jurídica
            else {
                $persona->personaJuridica()->create([
                    'razon_social' => $validated['razon_social'],
                    'nombre_comercial' => $validated['nombre_comercial'] ?? null,
                    'sitio_web' => $validated['sitio_web'] ?? null,
                    'usuario_creacion' => auth()->id()
                ]);
            }

            // Documentos
            if (isset($validated['documentos'])) {
                foreach ($validated['documentos'] as $documento) {
                    $persona->documentos()->create([
                        'id_tipo_documento' => $documento['id_tipo_documento'],
                        'numero_documento' => $documento['numero_documento'],
                        'usuario_creacion' => auth()->id()
                    ]);
                }
            }

            // Dirección
            if (isset($validated['direccion'])) {
                $persona->direcciones()->create([
                    ...$validated['direccion'],
                    'usuario_creacion' => auth()->id()
                ]);
            }

            // Celular
            if (isset($validated['celular'])) {
                $persona->celulares()->create([
                    ...$validated['celular'],
                    'usuario_creacion' => auth()->id()
                ]);
            }

            // Correo
            if (isset($validated['correo'])) {
                $persona->correos()->create([
                    ...$validated['correo'],
                    'usuario_creacion' => auth()->id()
                ]);
            }

            DB::commit();

            $persona->load([
                'personaNatural',
                'personaJuridica',
                'documentos.tipoDocumento',
                'direcciones',
                'celulares',
                'correos'
            ]);

            return $this->successResponse($persona, 201);

        } catch (Exception $e) {
            DB::rollback();
            return $this->errorResponse('Error al crear la persona: ' . $e->getMessage(), 400);
        }
    }

    public function show($id)
    {
        try {
            $persona = Persona::with([
                'personaNatural',
                'personaJuridica',
                'documentos.tipoDocumento',
                'direcciones',
                'celulares',
                'correos'
            ])->findOrFail($id);

            return $this->successResponse($persona);

        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener la persona: ' . $e->getMessage(), 404);
        }
    }

    public function update(PersonaStoreRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $persona = Persona::findOrFail($id);
            $validated = $request->validated();

            if ($persona->tipo_persona === 'N') {
                $persona->personaNatural()->update([
                    'nombres' => $validated['nombres'],
                    'apellido_paterno' => $validated['apellido_paterno'],
                    'apellido_materno' => $validated['apellido_materno'] ?? null,
                    'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
                    'id_tipo_genero' => $validated['id_tipo_genero'],
                    'id_tipo_estado_civil' => $validated['id_tipo_estado_civil'],
                    'usuario_actualizacion' => auth()->id()
                ]);
            } else {
                $persona->personaJuridica()->update([
                    'razon_social' => $validated['razon_social'],
                    'nombre_comercial' => $validated['nombre_comercial'] ?? null,
                    'sitio_web' => $validated['sitio_web'] ?? null,
                    'usuario_actualizacion' => auth()->id()
                ]);
            }

            DB::commit();

            $persona->load([
                'personaNatural',
                'personaJuridica',
                'documentos.tipoDocumento',
                'direcciones',
                'celulares',
                'correos'
            ]);

            return $this->successResponse($persona, 'Persona actualizada con éxito');

        } catch (Exception $e) {
            DB::rollback();
            return $this->errorResponse('Error al actualizar la persona: ' . $e->getMessage(), 400);
        }
    }

    public function destroy($id)
    {
        try {
            $persona = Persona::findOrFail($id);

            $persona->update([
                'estado' => 0,
                'usuario_actualizacion' => auth()->id()
            ]);

            return $this->successResponse(null, 'Persona eliminada con éxito');

        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar la persona: ' . $e->getMessage(), 400);
        }
    }
}

