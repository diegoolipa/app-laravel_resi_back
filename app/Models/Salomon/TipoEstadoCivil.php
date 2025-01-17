<?php

namespace App\Models\Salomon;

use App\Models\TablaBase;
use Illuminate\Support\Facades\DB;


class TipoEstadoCivil extends TablaBase
{
   protected $table = 'salomon.tipo_estado_civiles';
   protected $primaryKey = 'id_tipo_estado_civil';

   protected $fillable = [
       'nombre',
       'sigla',
       'estado'
   ];

   // Relación con PersonaNatural
   public function personasNaturales()
   {
       return $this->hasMany(PersonaNatural::class, 'id_tipo_estado_civil');
   }

   // Scope para activos
   public function scopeActivos($query)
   {
       return $query->where('estado', 1);
   }
}
