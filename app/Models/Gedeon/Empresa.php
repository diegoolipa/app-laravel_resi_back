<?php

namespace App\Models\Gedeon;

use App\Models\Gedeon\TablaBase;
use App\Models\Salomon\Persona;

class Empresa extends TablaBase
{
   protected $table = 'gedeon.empresas';
   protected $primaryKey = 'id_empresa';

   protected $fillable = [
       'logo',
       'id_persona'
   ];

   // Relación con Persona
   public function persona()
   {
       return $this->belongsTo(Persona::class, 'id_persona');
   }

   // Relación con Entidades
   public function entidades()
   {
       return $this->hasMany(Entidad::class, 'id_empresa');
   }
}
