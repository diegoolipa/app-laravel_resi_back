<?php

namespace App\Http\Controllers\AccessManagement;

use App\Http\Controllers\Controller;
use App\Models\Salomon\Access\Modulo;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class ModuloController extends Controller
{
   public function index(Request $request)
   {
       try {
           $modulos = Modulo::with(['menus'])->where('estado', 1);

           if ($request->filled('nombre')) {
               $modulos->where('nombre', 'ilike', "%{$request->nombre}%");
           }

           return $this->successResponse($modulos->get());
       } catch (Exception $e) {
           return $this->errorResponse($e->getMessage());
       }
   }

   public function store(Request $request)
   {
       DB::beginTransaction();
       try {
           $request->validate([
               'codigo' => 'required|unique:pgsql.salomon.modulos,codigo',
               'nombre' => 'required|unique:pgsql.salomon.modulos,nombre'
           ]);

           $modulo = Modulo::create($request->all());
           DB::commit();

           return $this->successResponse($modulo, 201, 'Módulo creado exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function update(Request $request, $id)
   {
       DB::beginTransaction();
       try {
           $modulo = Modulo::findOrFail($id);

           $request->validate([
               'codigo' => "required|unique:pgsql.salomon.modulos,codigo,{$id},id_modulo",
               'nombre' => "required|unique:pgsql.salomon.modulos,nombre,{$id},id_modulo"
           ]);

           $modulo->update($request->all());
           DB::commit();

           return $this->successResponse($modulo, 200, 'Módulo actualizado exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function destroy($id)
   {
       DB::beginTransaction();
       try {
           $modulo = Modulo::findOrFail($id);
           $modulo->estado = 0;
           $modulo->save();
           DB::commit();

           return $this->successResponse(null, 200, 'Módulo eliminado exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }
}


