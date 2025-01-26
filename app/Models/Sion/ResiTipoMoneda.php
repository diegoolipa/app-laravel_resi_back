<?php

namespace App\Models\Sion;

use App\Models\Gedeon\TablaBase;
use App\Models\Gedeon\Departamento;

class ResiTipoMoneda extends TablaBase
{
   protected $table = 'sion.resi_tipo_moneda';
   protected $primaryKey = 'id_tipo_moneda';

   protected $fillable = [
       'nombre',
       'codigo',
       'simbolo',
       'descripcion',
       'orden'
   ];

   // Relación con Departamentos
   public function departamentos()
   {
       return $this->hasMany(Departamento::class, 'id_tipo_moneda');
   }
}
