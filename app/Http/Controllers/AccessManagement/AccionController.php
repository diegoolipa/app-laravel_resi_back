<?php

namespace App\Http\Controllers\AccessManagement;

use App\Http\Controllers\Controller;
use App\Models\Salomon\Access\Accion;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class AccionController extends Controller
{
   public function index(Request $request)
   {
       try {
           $acciones = Accion::with(['menu'])->where('estado', 1);

           if ($request->filled('nombre')) {
               $acciones->where('nombre', 'ilike', "%{$request->nombre}%");
           }

           return $this->successResponse($acciones->get());
       } catch (Exception $e) {
           return $this->errorResponse($e->getMessage());
       }
   }

   public function store(Request $request)
   {
       DB::beginTransaction();
       try {
           $request->validate([
               'codigo' => 'required|unique:pgsql.salomon.acciones,codigo',
               'nombre' => 'required',
               'id_menu' => 'required|exists:pgsql.salomon.menus,id_menu',
               'tipo_accion' => 'required'
           ]);

           $accion = Accion::create($request->all());
           DB::commit();

           return $this->successResponse($accion, 201, 'Acción creada exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function update(Request $request, $id)
   {
       DB::beginTransaction();
       try {
           $accion = Accion::findOrFail($id);

           $request->validate([
               'codigo' => "required|unique:pgsql.salomon.acciones,codigo,{$id},id_accion",
               'nombre' => 'required',
               'id_menu' => 'required|exists:pgsql.salomon.menus,id_menu',
               'tipo_accion' => 'required'
           ]);

           $accion->update($request->all());
           DB::commit();

           return $this->successResponse($accion, 200, 'Acción actualizada exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function destroy($id)
   {
       DB::beginTransaction();
       try {
           $accion = Accion::findOrFail($id);
           $accion->estado = 0;
           $accion->save();
           DB::commit();

           return $this->successResponse(null, 200, 'Acción eliminada exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }
}
