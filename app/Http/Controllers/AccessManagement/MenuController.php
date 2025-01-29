<?php

namespace App\Http\Controllers\AccessManagement;

use App\Http\Controllers\Controller;
use App\Models\Salomon\Access\Menu;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        try {
            $menus = Menu::with(['modulo', 'padre', 'hijos'])->where('estado', 1);

            if ($request->filled('nombre')) {
                $menus->where('nombre', 'ilike', "%{$request->nombre}%");
            }

            return $this->successResponse($menus->get());
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'codigo' => 'required|unique:pgsql.salomon.menus,codigo',
                'nombre' => 'required',
                'id_modulo' => 'required|exists:pgsql.salomon.modulos,id_modulo',
                'id_padre' => 'nullable|exists:pgsql.salomon.menus,id_menu'
            ]);

            $menu = Menu::create($request->all());
            DB::commit();

            return $this->successResponse($menu, 201, 'Menú creado exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $menu = Menu::findOrFail($id);

            $request->validate([
                'codigo' => "required|unique:pgsql.salomon.menus,codigo,{$id},id_menu",
                'nombre' => 'required',
                'id_modulo' => 'required|exists:pgsql.salomon.modulos,id_modulo',
                'id_padre' => 'nullable|exists:pgsql.salomon.menus,id_menu'
            ]);

            $menu->update($request->all());
            DB::commit();

            return $this->successResponse($menu, 200, 'Menú actualizado exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $menu = Menu::findOrFail($id);
            $menu->estado = 0;
            $menu->save();
            DB::commit();

            return $this->successResponse(null, 200, 'Menú eliminado exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }
}
