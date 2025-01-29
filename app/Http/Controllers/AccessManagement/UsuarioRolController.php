<?php

namespace App\Http\Controllers\AccessManagement;

use App\Http\Controllers\Controller;
use App\Models\Salomon\Access\UsuarioRol;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class UsuarioRolController extends Controller
{
   public function index(Request $request)
   {
       try {
           $usuarioRoles = UsuarioRol::with(['usuario', 'rol'])
               ->where('estado', 1)
               ->activos();

           if ($request->filled('usuario')) {
               $usuarioRoles->whereHas('usuario', function($q) use($request) {
                   $q->where('name', 'ilike', "%{$request->usuario}%");
               });
           }

           return $this->successResponse($usuarioRoles->get());
       } catch (Exception $e) {
           return $this->errorResponse($e->getMessage());
       }
   }

   public function store(Request $request)
   {
       DB::beginTransaction();
       try {
           $request->validate([
               'id_usuario' => 'required|exists:users,id',
               'id_rol' => 'required|exists:salomon.roles,id_rol',
               'fecha_expiracion' => 'nullable|date|after:today'
           ]);

           $usuarioRol = UsuarioRol::create($request->all());
           DB::commit();

           return $this->successResponse($usuarioRol, 201, 'Rol asignado exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function update(Request $request, $id)
   {
       DB::beginTransaction();
       try {
           $usuarioRol = UsuarioRol::findOrFail($id);

           $request->validate([
               'id_usuario' => 'required|exists:users,id',
               'id_rol' => 'required|exists:salomon.roles,id_rol',
               'fecha_expiracion' => 'nullable|date|after:today'
           ]);

           $usuarioRol->update($request->all());
           DB::commit();

           return $this->successResponse($usuarioRol, 200, 'Asignación actualizada exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }

   public function destroy($id)
   {
       DB::beginTransaction();
       try {
           $usuarioRol = UsuarioRol::findOrFail($id);
           $usuarioRol->estado = 0;
           $usuarioRol->save();
           DB::commit();

           return $this->successResponse(null, 200, 'Asignación eliminada exitosamente');
       } catch (Exception $e) {
           DB::rollBack();
           return $this->errorResponse($e->getMessage());
       }
   }
}
