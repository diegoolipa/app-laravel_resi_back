<?php

namespace App\Models\Salomon\Access;

use App\Models\Gedeon\TablaBase;

class RolMenu extends TablaBase
{
   protected $table = 'salomon.rol_menus';
   protected $primaryKey = 'id_rol_menu';

   protected $fillable = [
       'id_rol',
       'id_menu'
   ];

   public function rol()
   {
       return $this->belongsTo(Role::class, 'id_rol');
   }

   public function menu()
   {
       return $this->belongsTo(Menu::class, 'id_menu');
   }

   public function acciones()
   {
       return $this->belongsToMany(Accion::class, 'salomon.rol_menu_acciones', 'id_rol_menu', 'id_accion')
                   ->withPivot(['estado_activacion']);
   }
}
