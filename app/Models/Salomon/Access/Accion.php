<?php
namespace App\Models\Salomon\Access;

use App\Models\Gedeon\TablaBase;

class Accion extends TablaBase
{
   protected $table = 'salomon.acciones';
   protected $primaryKey = 'id_accion';

   protected $fillable = [
       'id_menu',
       'codigo',
       'nombre',
       'descripcion',
       'tipo_accion',
       'requiere_autorizacion'
   ];

   protected $casts = [
       'requiere_autorizacion' => 'boolean'
   ];

   public function menu()
   {
       return $this->belongsTo(Menu::class, 'id_menu');
   }

   public function roles()
   {
       return $this->belongsToMany(Role::class, 'salomon.rol_menu_acciones', 'id_accion', 'id_rol_menu')
                   ->withPivot(['estado_activacion']);
   }

   public function scopeLectura($query)
   {
       return $query->where('tipo_accion', 'lectura');
   }

   public function scopeEscritura($query)
   {
       return $query->where('tipo_accion', 'escritura');
   }
}
