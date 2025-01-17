<?php

namespace App\Models\Sion;

use App\Models\TablaBase;

class Nivel extends TablaBase
{
   protected $table = 'sion.niveles';
   protected $primaryKey = 'id_nivel';

   protected $fillable = [
       'id_residencia',
       'nombre',
       'numero',
       'descripcion'
   ];

   protected $casts = [
       'numero' => 'integer'
   ];

   // Relación con Residencia
   public function residencia()
   {
       return $this->belongsTo(Residencia::class, 'id_residencia');
   }

   // Relación con Habitaciones
   public function habitaciones()
   {
       return $this->hasMany(Habitacion::class, 'id_nivel');
   }

   // Scope para niveles activos
   public function scopeActivos($query)
   {
       return $query->where('estado', 1);
   }

   // Scope para ordenar por número
   public function scopeOrdenado($query)
   {
       return $query->orderBy('numero');
   }
}
