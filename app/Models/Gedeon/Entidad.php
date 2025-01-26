<?php

namespace App\Models\Gedeon;

use App\Models\Gedeon\TablaBase;

class Entidad extends TablaBase
{
   protected $table = 'gedeon.entidades';
   protected $primaryKey = 'id_entidad';

   protected $fillable = [
       'id_empresa'
   ];

   // Relación con Empresa
   public function empresa()
   {
       return $this->belongsTo(Empresa::class, 'id_empresa');
   }

   // Relación con Departamentos
   public function departamentos()
   {
       return $this->hasMany(Departamento::class, 'id_entidad');
   }
}
