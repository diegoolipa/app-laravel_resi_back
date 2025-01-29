<?php

namespace App\Models\Salomon\Access;

use App\Models\Gedeon\TablaBase;

class Menu extends TablaBase
{
   protected $table = 'salomon.menus';
   protected $primaryKey = 'id_menu';

   protected $fillable = [
       'id_modulo',
       'codigo',
       'nombre',
       'descripcion',
       'url',
       'icono',
       'id_padre',
       'nivel',
       'es_navegable'
   ];

   protected $casts = [
       'nivel' => 'integer',
       'es_navegable' => 'boolean'
   ];

   public function modulo()
   {
       return $this->belongsTo(Modulo::class, 'id_modulo');
   }

   public function padre()
   {
       return $this->belongsTo(Menu::class, 'id_padre');
   }

   public function hijos()
   {
       return $this->hasMany(Menu::class, 'id_padre')->orderBy('nombre');
   }

   public function roles()
   {
       return $this->belongsToMany(Role::class, 'salomon.rol_menus', 'id_menu', 'id_rol');
   }

   public function acciones()
   {
       return $this->hasMany(Accion::class, 'id_menu');
   }

   public function getMenusHijos($idPadre = null)
   {
       return $this->where('id_padre', $idPadre)
                  ->where('estado', 1)
                  ->orderBy('nombre')
                  ->get();
   }
}
