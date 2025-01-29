<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Gedeon\Empresa;
use App\Models\Gedeon\Entidad;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class EntidadController extends Controller
{
   public function index(Request $request)
   {
       try {
           $query = Entidad::query()->where('estado', 1);

           if ($request->filled('search')) {
               $search = $request->search;
               $query->whereHas('empresa.persona', function($q) use ($search) {
                   $q->whereHas('personaNatural', function($q) use ($search) {
                       $q->where('nombres', 'ilike', "%{$search}%")
                         ->orWhere('apellido_paterno', 'ilike', "%{$search}%")
                         ->orWhere('apellido_materno', 'ilike', "%{$search}%");
                   })
                   ->orWhereHas('personaJuridica', function($q) use ($search) {
                       $q->where('razon_social', 'ilike', "%{$search}%")
                         ->orWhere('nombre_comercial', 'ilike', "%{$search}%");
                   });
               });
           }

           $entidades = $query->with([
               'empresa.persona.personaNatural',
               'empresa.persona.personaJuridica',
               'empresa.persona.documentos.tipoDocumento',
               'departamentos'
           ])->paginate(10);

           return $this->successResponse($entidades);
       } catch (Exception $e) {
           return $this->errorResponse($e->getMessage());
       }
   }

   public function store(Request $request)
   {
       DB::beginTransaction();
       try {
           $request->validate([
               'logo' => 'nullable|string',
               'id_persona' => 'required|exists:pgsql.salomon.personas,id_persona'
           ]);

           // Crear empresa
           $empresa = Empresa::create([
               'logo' => $request->logo,
               'id_persona' => $request->id_persona
           ]);

           // Crear entidad asociada
           $entidad = Entidad::create([
               'id_empresa' => $empresa->id_empresa
           ]);

           DB::commit();

           return $this->successResponse(
               ['empresa' => $empresa, 'entidad' => $entidad],
               201,
               'Entidad creada exitosamente'
           );

       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function show($id)
   {
       try {
           $entidad = Entidad::with([
               'empresa.persona.personaNatural',
               'empresa.persona.personaJuridica',
               'empresa.persona.documentos.tipoDocumento',
               'departamentos'
           ])->findOrFail($id);

           return $this->successResponse($entidad);
       } catch (Exception $e) {
           return $this->errorResponse($e->getMessage());
       }
   }

   public function update(Request $request, $id)
   {
       DB::beginTransaction();
       try {
           $entidad = Entidad::findOrFail($id);

           $request->validate([
               'logo' => 'nullable|string',
               'id_persona' => 'required|exists:pgsql.salomon.personas,id_persona'
           ]);

           // Actualizar empresa
           $entidad->empresa->update([
               'logo' => $request->logo,
               'id_persona' => $request->id_persona
           ]);

           DB::commit();

           return $this->successResponse($entidad, 200, 'Entidad actualizada exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function destroy($id)
   {
       DB::beginTransaction();
       try {
           $entidad = Entidad::findOrFail($id);

           // Desactivar entidad y empresa
           $entidad->estado = 0;
           $entidad->save();

           $entidad->empresa->estado = 0;
           $entidad->empresa->save();

           DB::commit();

           return $this->successResponse(null, 200, 'Entidad eliminada exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function listarEntidadDepartamentos($idEntidad, Request $request)
   {
       try {
           $query = Entidad::findOrFail($idEntidad)
                          ->departamentos()
                          ->where('estado', 1);

           if ($request->filled('search')) {
               $search = $request->search;
               $query->where(function($q) use ($search) {
                   $q->where('nombre', 'ilike', "%{$search}%")
                     ->orWhere('codigo', 'ilike', "%{$search}%")
                     ->orWhereHas('encargado', function($q) use ($search) {
                         $q->whereHas('personaNatural', function($q) use ($search) {
                             $q->where('nombres', 'ilike', "%{$search}%")
                               ->orWhere('apellido_paterno', 'ilike', "%{$search}%");
                         });
                     });
               });
           }

           $departamentos = $query->with([
               'encargado.personaNatural',
               'encargado.documentos.tipoDocumento',
               'tipoMoneda',
               'residencia'
           ])->paginate(10);

           return $this->successResponse($departamentos);

       } catch (Exception $e) {
           return $this->errorResponse($e->getMessage());
       }
   }
}
