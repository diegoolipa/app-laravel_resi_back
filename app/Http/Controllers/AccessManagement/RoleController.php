<?php

namespace App\Http\Controllers\AccessManagement;

use App\Http\Controllers\Controller;
use App\Models\Salomon\Access\Role;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{

    public function index(Request $request)
    {
        try {
            $roles = Role::with(['entidad'])->where('estado', 1);

            if ($request->filled('nombre')) {
                $roles->where('nombre', 'ilike', "%{$request->nombre}%");
            }

            return $this->successResponse($roles->get());
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'nombre' => 'required|unique:pgsql.salomon.roles,nombre',
                'id_entidad' => 'required|exists:pgsql.gedeon.entidades,id_entidad'
            ]);

            $role = Role::create($request->all());
            DB::commit();

            return $this->successResponse($role, 201, 'Rol creado exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $role = Role::with(['entidad', 'menus', 'acciones'])->findOrFail($id);
            return $this->successResponse($role);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);

            $request->validate([
                'nombre' => "required|unique:pgsql.salomon.roles,nombre,{$id},id_rol",
                'id_entidad' => 'required|exists:pgsql.gedeon.entidades,id_entidad'
            ]);

            $role->update($request->all());
            DB::commit();

            return $this->successResponse($role, 200, 'Rol actualizado exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            $role->estado = 0;
            $role->save();
            DB::commit();

            return $this->successResponse(null, 200, 'Rol eliminado exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function asignarMenus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'menus' => 'required|array',
                'menus.*' => 'exists:salomon.menus,id_menu'
            ]);

            $role = Role::findOrFail($id);

            // Desactivar menús actuales
            $role->menus()->update(['estado' => 0]);

            // Asignar nuevos menús
            foreach ($request->menus as $menuId) {
                $role->menus()->attach($menuId, ['estado' => 1]);
            }

            DB::commit();
            return $this->successResponse(null, 200, 'Menús asignados exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function asignarAcciones(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'acciones' => 'required|array',
                'acciones.*' => 'exists:salomon.acciones,id_accion'
            ]);

            $role = Role::findOrFail($id);

            // Desactivar acciones actuales
            $role->acciones()->update(['estado' => 0]);

            // Asignar nuevas acciones
            foreach ($request->acciones as $accionId) {
                $role->acciones()->attach($accionId, ['estado' => 1]);
            }

            DB::commit();
            return $this->successResponse(null, 200, 'Acciones asignadas exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }
}
