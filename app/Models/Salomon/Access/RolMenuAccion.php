<?php
namespace App\Models\Salomon\Access;

use App\Models\Gedeon\TablaBase;

class RolMenuAccion extends TablaBase
{
   protected $table = 'salomon.rol_menu_acciones';
   protected $primaryKey = 'id_rol_menu_accion';

   protected $fillable = [
       'id_rol_menu',
       'id_accion',
       'estado_activacion'
   ];

   protected $casts = [
       'estado_activacion' => 'boolean'
   ];

   public function rolMenu()
   {
       return $this->belongsTo(RolMenu::class, 'id_rol_menu');
   }

   public function accion()
   {
       return $this->belongsTo(Accion::class, 'id_accion');
   }

   public function scopeActivos($query)
   {
       return $query->where('estado_activacion', true);
   }
}
