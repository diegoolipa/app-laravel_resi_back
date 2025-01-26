<?php

namespace App\Models\Gedeon;

use App\Models\Gedeon\TablaBase;
use App\Models\Salomon\Persona;
use App\Models\Sion\ResiTipoMoneda;
use App\Models\Sion\ResiResidencia;

class Departamento extends TablaBase
{
   protected $table = 'gedeon.departamentos';
   protected $primaryKey = 'id_departamento';

   protected $fillable = [
       'id_entidad',
       'id_encargado',
       'id_tipo_moneda',
       'nombre',
       'codigo',
       'descripcion',
       'latitud',
       'longitud'
   ];

   protected $casts = [
       'latitud' => 'decimal:8',
       'longitud' => 'decimal:8'
   ];

   // Relación con Entidad
   public function entidad()
   {
       return $this->belongsTo(Entidad::class, 'id_entidad');
   }

   // Relación con Persona Encargada
   public function encargado()
   {
       return $this->belongsTo(Persona::class, 'id_encargado');
   }

   // Relación con Tipo de Moneda
   public function tipoMoneda()
   {
       return $this->belongsTo(ResiTipoMoneda::class, 'id_tipo_moneda');
   }

   // Relación con Residencia
   public function residencia()
   {
       return $this->hasOne(ResiResidencia::class, 'id_departamento');
   }
}
