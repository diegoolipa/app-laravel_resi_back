<?php

namespace App\Models\Sion;

use App\Models\Salomon\Persona;
use App\Models\TablaBase;
use App\Models\User;

class Residencia extends TablaBase
{
   protected $table = 'sion.residencias';
   protected $primaryKey = 'id_residencia';

   protected $fillable = [
       'id_empresa',
       'id_encargado',
       'nombre',
       'descripcion',
       'latitud',
       'longitud'
   ];

   protected $casts = [
       'latitud' => 'decimal:8',
       'longitud' => 'decimal:8'
   ];

   // Relación con Empresa
   public function empresa()
   {
       return $this->belongsTo(Empresa::class, 'id_empresa');
   }

   // Relación con Persona (encargado)
   public function encargado()
   {
       return $this->belongsTo(Persona::class, 'id_encargado','id_persona');
   }

   // Relación con habitaciones
   public function habitaciones()
   {
       return $this->hasMany(Habitacion::class, 'id_residencia');
   }

   // Relación con usuarios a través de la tabla pivot
   public function usuarios()
   {
       return $this->belongsToMany(
           User::class,
           'sion.usuario_residencias',
           'id_residencia',
           'id_usuario'
       )->withPivot('es_actual')
        ->withTimestamps();
   }

   // Obtener usuarios activos de la residencia
   public function usuariosActivos()
   {
       return $this->usuarios()
           ->wherePivot('es_actual', true)
           ->wherePivot('estado', 1);
   }

   // Scope para residencias activas
   public function scopeActivas($query)
   {
       return $query->where('estado', 1);
   }
}
