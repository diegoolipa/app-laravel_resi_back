<?php

namespace App\Models\Salomon;

use App\Models\Gedeon\TablaBase;
use Illuminate\Support\Facades\DB;


class TipoGenero extends TablaBase
{
   protected $table = 'salomon.tipo_generos';
   protected $primaryKey = 'id_tipo_genero';

   protected $fillable = [
       'nombre',
       'sigla',
       'orden'
   ];

   // Relación con PersonaNatural
   public function personasNaturales()
   {
       return $this->hasMany(PersonaNatural::class, 'id_tipo_genero');
   }

   // Scope para ordenar por el campo orden
   public function scopeOrdenado($query)
   {
       return $query->orderBy('orden');
   }

   // Scope para activos
   public function scopeActivos($query)
   {
       return $query->where('estado', 1);
   }
}
