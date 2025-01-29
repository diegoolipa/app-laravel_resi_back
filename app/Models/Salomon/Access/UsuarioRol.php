<?php

namespace App\Models\Salomon\Access;

use App\Models\Gedeon\TablaBase;
use App\Models\User;

class UsuarioRol extends TablaBase
{
   protected $table = 'salomon.usuario_roles';
   protected $primaryKey = 'id_usuariorol';

   protected $fillable = [
       'id_usuario',
       'id_rol',
       'fecha_asignacion',
       'fecha_expiracion'
   ];

   protected $casts = [
       'fecha_asignacion' => 'datetime',
       'fecha_expiracion' => 'datetime'
   ];

   public function usuario()
   {
       return $this->belongsTo(User::class, 'id_usuario');
   }

   public function rol()
   {
       return $this->belongsTo(Role::class, 'id_rol');
   }

   public function scopeActivos($query)
   {
       return $query->whereNull('fecha_expiracion')
                   ->orWhere('fecha_expiracion', '>', now());
   }
}
