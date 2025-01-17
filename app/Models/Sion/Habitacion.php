<?php

namespace App\Models\Sion;

use App\Models\TablaBase;

class Habitacion extends TablaBase
{
   protected $table = 'sion.habitaciones';
   protected $primaryKey = 'id_habitacion';

   protected $fillable = [
       'id_residencia',
       'id_tipo_habitacion',
       'id_nivel',
       'nombre',
       'numero',
       'piso',
       'precio',
       'estado_habitacion',
       'observaciones'
   ];

   protected $casts = [
       'precio' => 'decimal:2',
       'estado_habitacion' => 'integer'
   ];

   // Estados de habitación como constantes
   const ESTADO_DISPONIBLE = 1;
   const ESTADO_OCUPADA = 2;
   const ESTADO_MANTENIMIENTO = 3;
   const ESTADO_LIMPIEZA = 4;

   // Relación con Residencia
   public function residencia()
   {
       return $this->belongsTo(Residencia::class, 'id_residencia');
   }

   // Relación con TipoHabitacion
   public function tipoHabitacion()
   {
       return $this->belongsTo(TipoHabitacion::class, 'id_tipo_habitacion');
   }

   // Relación con Nivel
   public function nivel()
   {
       return $this->belongsTo(Nivel::class, 'id_nivel');
   }

//    // Relación con reservas
//    public function reservaDetalles()
//    {
//        return $this->hasMany(ReservaDetalle::class, 'id_habitacion');
//    }

   // Scope para habitaciones disponibles
   public function scopeDisponibles($query)
   {
       return $query->where('estado_habitacion', self::ESTADO_DISPONIBLE)
                   ->where('estado', 1);
   }

   // Scope por residencia
   public function scopePorResidencia($query, $idResidencia)
   {
       return $query->where('id_residencia', $idResidencia);
   }

   // Método para verificar disponibilidad en fechas
   public function verificarDisponibilidad($fechaIngreso, $fechaSalida)
   {
       return !$this->reservaDetalles()
           ->where(function($query) use ($fechaIngreso, $fechaSalida) {
               $query->whereBetween('fecha_ingreso', [$fechaIngreso, $fechaSalida])
                   ->orWhereBetween('fecha_salida', [$fechaIngreso, $fechaSalida])
                   ->orWhere(function($q) use ($fechaIngreso, $fechaSalida) {
                       $q->where('fecha_ingreso', '<=', $fechaIngreso)
                         ->where('fecha_salida', '>=', $fechaSalida);
                   });
           })
           ->whereIn('estado_detalle', ['ACTIVO', 'PENDIENTE'])
           ->exists();
   }

   // Método para cambiar estado
   public function cambiarEstado($estado)
   {
       $this->estado_habitacion = $estado;
       return $this->save();
   }

   // Accessor para precio formateado
   public function getPrecioFormateadoAttribute()
   {
       return 'S/ ' . number_format($this->precio, 2);
   }

   // Accessor para estado en texto
   public function getEstadoTextoAttribute()
   {
       $estados = [
           self::ESTADO_DISPONIBLE => 'Disponible',
           self::ESTADO_OCUPADA => 'Ocupada',
           self::ESTADO_MANTENIMIENTO => 'En Mantenimiento',
           self::ESTADO_LIMPIEZA => 'En Limpieza'
       ];

       return $estados[$this->estado_habitacion] ?? 'Desconocido';
   }
}
