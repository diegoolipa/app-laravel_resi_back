<?php

namespace App\Http\Controllers\AccessManagement;

use App\Http\Controllers\Controller;
use App\Models\Salomon\Access\RolMenu;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class RolMenuController extends Controller
{
   public function index(Request $request)
   {
       try {
           $rolMenus = RolMenu::with(['rol', 'menu', 'acciones'])->where('estado', 1);
           return $this->successResponse($rolMenus->get());
       } catch (Exception $e) {
           return $this->errorResponse($e->getMessage());
       }
   }

   public function store(Request $request)
   {
       DB::beginTransaction();
       try {
           $request->validate([
               'id_rol' => 'required|exists:salomon.roles,id_rol',
               'id_menu' => 'required|exists:salomon.menus,id_menu'
           ]);

           $rolMenu = RolMenu::create($request->all());
           DB::commit();

           return $this->successResponse($rolMenu, 201, 'Rol-Menú creado exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function update(Request $request, $id)
   {
       DB::beginTransaction();
       try {
           $rolMenu = RolMenu::findOrFail($id);

           $request->validate([
               'id_rol' => 'required|exists:salomon.roles,id_rol',
               'id_menu' => 'required|exists:salomon.menus,id_menu'
           ]);

           $rolMenu->update($request->all());
           DB::commit();

           return $this->successResponse($rolMenu, 200, 'Rol-Menú actualizado exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function destroy($id)
   {
       DB::beginTransaction();
       try {
           $rolMenu = RolMenu::findOrFail($id);
           $rolMenu->estado = 0;
           $rolMenu->save();
           DB::commit();

           return $this->successResponse(null, 200, 'Rol-Menú eliminado exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }
}
