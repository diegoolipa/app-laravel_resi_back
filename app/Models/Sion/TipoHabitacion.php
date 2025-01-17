<?php

namespace App\Models\Sion;

use App\Models\TablaBase;

class TipoHabitacion extends TablaBase
{
   protected $table = 'sion.tipo_habitaciones';
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
       'caracteristicas' => 'array',
       'precio_base' => 'decimal:2',
       'capacidad_maxima' => 'integer'
   ];

   // Relación con Residencia
   public function residencia()
   {
       return $this->belongsTo(Residencia::class, 'id_residencia');
   }

   // Relación con Habitaciones
   public function habitaciones()
   {
       return $this->hasMany(Habitacion::class, 'id_tipo_habitacion');
   }

   // Scope para tipos activos
   public function scopeActivos($query)
   {
       return $query->where('estado', 1);
   }

   // Scope por residencia
   public function scopePorResidencia($query, $idResidencia)
   {
       return $query->where('id_residencia', $idResidencia);
   }

   // Método para obtener habitaciones disponibles
   public function getHabitacionesDisponibles()
   {
       return $this->habitaciones()
           ->where('estado', 1)
           ->where('estado_habitacion', 'DISPONIBLE')
           ->get();
   }

   // Método para verificar disponibilidad en fechas
   public function verificarDisponibilidad($fechaIngreso, $fechaSalida)
   {
       return $this->habitaciones()
           ->where('estado', 1)
           ->whereDoesntHave('reservaDetalles', function($query) use ($fechaIngreso, $fechaSalida) {
               $query->where(function($q) use ($fechaIngreso, $fechaSalida) {
                   $q->whereBetween('fecha_ingreso', [$fechaIngreso, $fechaSalida])
                     ->orWhereBetween('fecha_salida', [$fechaIngreso, $fechaSalida])
                     ->orWhere(function($q) use ($fechaIngreso, $fechaSalida) {
                         $q->where('fecha_ingreso', '<=', $fechaIngreso)
                           ->where('fecha_salida', '>=', $fechaSalida);
                     });
               })->whereIn('estado_detalle', ['ACTIVO', 'PENDIENTE']);
           })
           ->count();
   }

   // Accessor para mostrar precio formateado
   public function getPrecioFormateadoAttribute()
   {
       return 'S/ ' . number_format($this->precio_base, 2);
   }

   // Accessor para mostrar caracteristicas como lista
   public function getCaracteristicasListaAttribute()
   {
       return collect($this->caracteristicas)->map(function($valor, $clave) {
           return ["caracteristica" => $clave, "valor" => $valor];
       })->values();
   }
}
