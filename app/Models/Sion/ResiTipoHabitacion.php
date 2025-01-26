<?php

namespace App\Models\Sion;

use App\Models\Gedeon\TablaBase;

class ResiTipoHabitacion extends TablaBase
{
   protected $table = 'sion.resi_tipo_habitaciones';
   protected $primaryKey = 'id_tipo_habitacion';

   protected $fillable = [
       'id_residencia',
       'nombre',
       'descripcion',
       'capacidad_maxima',
       'precio_base',
       'caracteristicas'
   ];

   protected $casts = [
       'precio_base' => 'decimal:2',
       'caracteristicas' => 'array'
   ];

   // Relación con Residencia
   public function residencia()
   {
       return $this->belongsTo(ResiResidencia::class, 'id_residencia');
   }

   // Scope para tipos de habitación por capacidad
   public function scopePorCapacidad($query, $capacidad)
   {
       return $query->where('capacidad_maxima', '>=', $capacidad);
   }
}
