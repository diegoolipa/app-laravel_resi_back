<?php

namespace App\Http\Controllers\AccessManagement;

use App\Http\Controllers\Controller;
use App\Models\Salomon\Access\RolMenuAccion;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class RolMenuAccionController extends Controller
{
   public function index(Request $request)
   {
       try {
           $rolMenuAcciones = RolMenuAccion::with(['rolMenu', 'accion'])
               ->where('estado', 1)
               ->activos();

           return $this->successResponse($rolMenuAcciones->get());
       } catch (Exception $e) {
           return $this->errorResponse($e->getMessage());
       }
   }

   public function store(Request $request)
   {
       DB::beginTransaction();
       try {
           $request->validate([
               'id_rol_menu' => 'required|exists:salomon.rol_menus,id_rol_menu',
               'id_accion' => 'required|exists:salomon.acciones,id_accion'
           ]);

           $rolMenuAccion = RolMenuAccion::create($request->all());
           DB::commit();

           return $this->successResponse($rolMenuAccion, 201, 'Acción asignada exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function update(Request $request, $id)
   {
       DB::beginTransaction();
       try {
           $rolMenuAccion = RolMenuAccion::findOrFail($id);

           $request->validate([
               'id_rol_menu' => 'required|exists:salomon.rol_menus,id_rol_menu',
               'id_accion' => 'required|exists:salomon.acciones,id_accion'
           ]);

           $rolMenuAccion->update($request->all());
           DB::commit();

           return $this->successResponse($rolMenuAccion, 200, 'Asignación actualizada exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function destroy($id)
   {
       DB::beginTransaction();
       try {
           $rolMenuAccion = RolMenuAccion::findOrFail($id);
           $rolMenuAccion->estado = 0;
           $rolMenuAccion->save();
           DB::commit();

           return $this->successResponse(null, 200, 'Asignación eliminada exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }
}
