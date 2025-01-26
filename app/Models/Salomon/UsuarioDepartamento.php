<?php

namespace App\Models\Salomon;

use App\Models\User;
use App\Models\Gedeon\Departamento;
use App\Models\Gedeon\TablaBase;

class UsuarioDepartamento extends TablaBase
{
   protected $table = 'salomon.usuario_departamentos';
   protected $primaryKey = 'id_usuario_departamento';

   protected $fillable = [
       'id_usuario',
       'id_departamento',
       'es_activo',
       'fecha_asignacion'
   ];

   protected $casts = [
       'es_activo' => 'boolean',
       'fecha_asignacion' => 'datetime'
   ];

   // Relación con Usuario
   public function usuario()
   {
       return $this->belongsTo(User::class, 'id_usuario');
   }

   // Relación con Departamento
   public function departamento()
   {
       return $this->belongsTo(Departamento::class, 'id_departamento');
   }

   // Scope para departamentos activos
   public function scopeActivos($query)
   {
       return $query->where('es_activo', true);
   }

   // Método para cambiar departamento activo
   public static function cambiarDepartamentoActivo($usuarioId, $departamentoId)
   {
       // Desactivar todos los departamentos del usuario
       self::where('id_usuario', $usuarioId)
           ->update(['es_activo' => false]);

       // Activar el nuevo departamento
       return self::updateOrCreate(
           [
               'id_usuario' => $usuarioId,
               'id_departamento' => $departamentoId
           ],
           ['es_activo' => true]
       );
   }

   // Obtener departamento activo del usuario
   public static function departamentoActivoUsuario($usuarioId)
   {
       return self::where('id_usuario', $usuarioId)
           ->where('es_activo', true)
           ->first();
   }
}
